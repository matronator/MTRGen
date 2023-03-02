<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Registry;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Parsem\Parser;
use Symfony\Component\Console\Output\OutputInterface;

class Connection
{
    public const PROD_URL = 'https://www.mtrgen.com/api';
    public const DEBUG_URL = 'http://localhost:8000/api';

    public string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = self::isDebug() ? self::DEBUG_URL : self::PROD_URL;
    }

    public function createUser(string $username, string $password)
    {
        $url = $this->apiUrl . '/signup';
        $client = new Client;
        $response = $client->post($url, ['form_params' => [
            'username' => strtolower($username),
            'password' => $password,
        ]]);

        return $response->getReasonPhrase();
    }

    public function login(string $username, string $password, int $duration = 24): ?object
    {
        $client = new Client;
        $url = $this->apiUrl . '/login';
        try {
            $response = $client->post($url, ['form_params' => [
                'username' => strtolower($username),
                'password' => $password,
                'duration' => $duration,
            ]]);
        } catch (RequestException $e) {
            if (!$e->hasResponse()) {
                $response = (object) ['status' => 'error', 'message' => 'Something went wrong.'];
            } else {
                $response = $e->getResponse();
            }
        }

        return json_decode($response->getBody()->getContents());
    }

    public function getTemplate(string $identifier): object
    {
        [$vendor, $name] = explode('/', $identifier);

        $url = $this->apiUrl . "/templates/$vendor/$name/get";

        $client = new Client();
        $response = $client->get($url, [
            'headers' => [
                'X-Requested-By' => 'cli',
            ],
        ]);
        $contentType = $response->getHeaderLine('Content-Type');

        switch ($contentType) {
            case 'application/json':
            case 'text/json':
                $extension = 'json';
            case 'text/x-yaml':
            case 'application/x-yaml':
            case 'text/yaml':
                $extension = 'yaml';
            case 'application/x-neon':
            case 'text/x-neon':
            case 'text/neon':
                $extension = 'neon';
            default:
                $extension = 'neon';
        }

        $typeUrl = $this->apiUrl . "/templates/$vendor/$name/type";
        $typeResponse = $client->get($typeUrl);

        $type = json_decode($typeResponse->getBody()->getContents());

        return (object) [
            'filename' => "$name.$type.$extension",
            'contents' => $response->getBody()->getContents(),
            'type' => $contentType,
        ];
    }

    public function getTemplateFromBundle(string $identifier, string $templateName): object
    {
        [$vendor, $name] = explode('/', $identifier);

        $url = $this->apiUrl . "/bundles/$vendor/$name/$templateName/get";

        $client = new Client();
        $response = $client->get($url, [
            'headers' => [
                'X-Requested-By' => 'cli',
            ],
        ]);
        $contentType = $response->getHeaderLine('Content-Type');

        switch ($contentType) {
            case 'application/json':
            case 'text/json':
                $extension = 'json';
            case 'text/x-yaml':
            case 'application/x-yaml':
            case 'text/yaml':
                $extension = 'yaml';
            case 'application/x-neon':
            case 'text/x-neon':
            case 'text/neon':
                $extension = 'neon';
            default:
                $extension = 'neon';
        }

        return (object) [
            'filename' => "$name.template.$extension",
            'contents' => $response->getBody()->getContents(),
            'type' => $contentType,
        ];
    }

    public function postTemplate(string $path, ?OutputInterface $io = null): mixed
    {
        $profile = new Profile;
        if (!$profile->authenticate())
            return '<fg=red>You must login first.</>';

        $matched = preg_match('/^(.+\/)?(.+?\.(json|yml|yaml|neon))$/', $path, $matches);
        if (!$matched)
            return "Couldn't get filename from path '$path'.";

        $filename = $matches[2];
        $template = Parser::decodeByExtension($path, file_get_contents($path));

        $profileObject = $profile->loadProfile();

        $client = new Client();
        $storage = new Storage;
        if ($storage->isBundle($filename)) {
            if (!Parser::isValidBundle($path, file_get_contents($path)))
                return '<fg=red>Invalid bundle.</>';
            
            $templates = [];
            foreach ($template->templates as $item) {
                $templates[] = (object) [
                    'name' => $item->name,
                    'filename' => $item->path,
                    'contents' => file_get_contents($storage->templateDir . DIRECTORY_SEPARATOR . $item->path),
                ];
            }
            $body = [
                'username' => $profileObject->username,
                'filename' => $filename,
                'name' => strtolower($template->name),
                'contents' => file_get_contents($path),
                'templates' => $templates,
            ];
            $url = $this->apiUrl . '/bundles';
        } else {
            if (!Parser::isValid(Path::makeAbsolute($path), file_get_contents(Path::makeAbsolute($path))))
                return '<fg=red>Invalid template.</>';
            
            $body = [
                'username' => $profileObject->username,
                'filename' => $filename,
                'name' => strtolower($template->name),
                'contents' => file_get_contents(Path::makeAbsolute($path)),
            ];
            $url = $this->apiUrl . '/templates';
        }

        if ($io) {
            $io->writeln('');
            $io->writeln('<fg=green>Publishing...</>');
        }

        $response = $client->request('POST', $url, ['form_params' => $body, 'progress' => function () use ($io) {
            if ($io) {
                $io->write('.');
            }
        }, 'headers' => [
            'Authorization' => 'Bearer ' . $profileObject->token,
        ]]);

        if ($io) $io->writeln('');

        return json_decode($response->getBody()->getContents());
    }

    public static function isDebug()
    {
        $config = json_decode(file_get_contents(__DIR__ . '/../../../config.json'));

        return $config->debug ?? false;
    }
}
