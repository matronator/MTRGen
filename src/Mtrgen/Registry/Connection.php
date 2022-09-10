<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Registry;

use GuzzleHttp\Client;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;
use Matronator\Parsem\Parser;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

class Connection
{
    public const PROD_URL = 'https://mtrgen.matrogames.com';
    public const DEBUG_URL = 'http://localhost:8000';

    public string $apiUrl;

    public string $profile;

    public function __construct()
    {
        $storage = new Storage();

        $this->profile = Path::canonicalize($storage->homeDir . '/profile.json');

        if (!file_exists($this->profile))
            $this->createProfile();
    }

    public static function isDebug()
    {
        $config = json_decode(file_get_contents(__DIR__ . '/../../../config.json'));

        return $config->debug ?? false;
    }

    public function createProfile()
    {
        $profile = (object) [
            'username' => '',
            'password' => '',
        ];

        $this->saveProfile($profile);
    }

    public function saveProfile(object $profile)
    {
        file_put_contents(Path::safe($this->profile), json_encode($profile));
    }

    private function loadProfile(): object
    {
        return json_decode(file_get_contents(Path::safe($this->profile)));
    }

    public function writeToProfile(string $username, string $password)
    {
        $profile = $this->loadProfile();

        $profile->username = $username;
        $profile->password = password_hash($password, PASSWORD_DEFAULT);

        $this->saveProfile($profile);
    }

    public function getTemplate(string $identifier): string
    {
        [$vendor, $name] = explode('/', $identifier);

        $urlBase = self::isDebug() ? self::DEBUG_URL : self::PROD_URL;

        $url = $urlBase . "/api/templates/$vendor/$name/get";

        return file_get_contents($url);
    }

    public function postTemplate(string $path, ?OutputInterface $io = null): string
    {
        $urlBase = self::isDebug() ? self::DEBUG_URL : self::PROD_URL;
        $url = $urlBase . "/api/templates";

        $matched = preg_match('/^(.+\/)?(.+?\.(json|yml|yaml|neon))$/', $path, $matches);
        if (!$matched)
            return "Couldn't get filename from path '$path'.";

        $filename = $matches[2];

        $profile = $this->loadProfile();
        if (!isset($profile->username) || $profile->username === '' || !isset($profile->password) || $profile->password === '')
            return '<fg=red>You must login first.</>';

        $template = Parser::decodeByExtension($filename);

        $body = [
            'username' => $profile->username,
            'password' => $profile->password,
            'filename' => $filename,
            'name' => $template->name,
            'contents' => file_get_contents(Path::makeAbsolute($path)),
        ];

        $client = new Client();
        if ($io) {
            $io->writeln('');
            $io->writeln('<fg=green>Publishing...</>');
        }
        $response = $client->request('POST', $url, ['form_params' => $body, 'progress' => function () use ($io) {
            if ($io) {
                $io->write(['.']);
            }
        }]);

        $io->writeln('');

        return $response->getReasonPhrase();
    }
}
