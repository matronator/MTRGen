<?php

namespace Matronator\Mtrgen\Template;

use Matronator\Mtrgen\ClassicFileGenerator;
use Matronator\Mtrgen\GenericFileObject;
use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Template\TemplateHeader;
use Matronator\Parsem\Parser;

class Generator
{
    public const HEADER_PATTERN = '/^\S+ --- MTRGEN ---.(.+)\s\S+ --- MTRGEN ---/ms';

    public const COMMENT_PATTERN = '/\/\*\s?([a-zA-Z0-9_]+)\|?(([a-zA-Z0-9_]+?)(?:\:(?:(?:\'|")?\w(?:\'|")?,?)+?)*?)?\s?\*\//m';

    public static function parseAnyFile(string $path, array $arguments = [], bool $useCommentSyntax = false): GenericFileObject
    {
        $file = ltrim(file_get_contents($path));

        if (!$file) {
            throw new \RuntimeException(sprintf('File "%s" was not found', $path));
        }

        $parsed = Parser::parseString($file, $arguments, $useCommentSyntax !== false ? self::COMMENT_PATTERN : null);

        $header = static::getTemplateHeader($parsed);
        $parsed = static::removeHeader($parsed);

        return new GenericFileObject($header->path, $header->filename, $parsed);
    }

    /**
     * Write parsed files to disk
     *
     * @param GenericFileObject[]|GenericFileObject $files
     * @return void
     */
    public static function writeFiles(mixed $files)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                self::write($file);
            }
        } else {
            self::write($files);
        }
    }

    private static function write(GenericFileObject $file): void
    {
        if (!ClassicFileGenerator::folderExist($file->directory) && !mkdir($concurrentDirectory = $file->directory, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        file_put_contents(Path::safe(str_ends_with($file->directory, DIRECTORY_SEPARATOR) 
            ? $file->directory . $file->filename
            : $file->directory . DIRECTORY_SEPARATOR . $file->filename), $file->contents);
    }

    /**
     * Get the template name
     *
     * @param string|null $content
     * @param string|null $path
     * @return string
     */
    public static function getName(?string $content = null, ?string $path = null): string
    {
        if (!$content && !$path) {
            throw new \RuntimeException('Either content or path must be provided.');
        }
        if (!$content && $path) {
            $content = file_get_contents(Path::canonicalize($path));
        }
        return static::getTemplateHeader($content)->name;
    }

    public static function getTemplateHeader(string $content): TemplateHeader
    {
        preg_match_all(self::HEADER_PATTERN, $content, $matches);

        $header = $matches[1][0];
        $lines = preg_split('/\n|\r\n/', $header);
        $info = [];
        foreach ($lines as $line) {
            $line = trim($line, " /\t\n\r\0\x0B\\");
            $keyValue = explode(':', $line);
            $key = trim($keyValue[0]);
            $value = trim($keyValue[1]);
            $info[$key] = $value;
        }

        if (!isset($info['name']) || !isset($info['filename']) || !isset($info['path'])) {
            throw new \RuntimeException('Template header is missing some required properties (name, filename, path).');
        }

        return TemplateHeader::fromArray($info);
    }

    public static function removeHeader(string $content): string
    {
        return ltrim(preg_replace(self::HEADER_PATTERN, '', $content));
    }
}
