<?php

namespace Matronator\Mtrgen\Template;

use Matronator\Mtrgen\GenericFileObject;
use Matronator\Mtrgen\Template\TemplateHeader;
use Matronator\Parsem\Parser;

class Generator
{
    public const RESERVED_KEYWORDS = ClassicGenerator::RESERVED_KEYWORDS;
    public const RESERVED_CONSTANTS = ClassicGenerator::RESERVED_CONSTANTS;

    public const HEADER_PATTERN = '/^\S+ --- MTRGEN ---.(.+)\s\S+ --- MTRGEN ---/s';

    public const COMMENT_PATTERN = '/\/\*\s?([a-zA-Z0-9_]+)\|?(([a-zA-Z0-9_]+?)(?:\:(?:(?:\'|")?\w(?:\'|")?,?)+?)*?)?\s?\*\//m';

    public static function parseAnyFile(string $path, bool $useCommentSyntax = false): GenericFileObject
    {
        $file = file_get_contents($path);

        if (!$file) {
            throw new \RuntimeException(sprintf('File "%s" was not found', $path));
        }

        $arguments = Parser::getArguments($file, $useCommentSyntax !== false ? self::COMMENT_PATTERN : null);
        $parsed = Parser::parseString($file, $arguments, $useCommentSyntax !== false ? self::COMMENT_PATTERN : null);

        preg_match_all(self::HEADER_PATTERN, $parsed, $matches);

        return new GenericFileObject(dirname($path), basename($path), $parsed);
    }

    /**
     * Get the template name
     *
     * @param string $content
     * @return string
     */
    public static function getName(string $content): string
    {
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
}
