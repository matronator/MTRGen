<?php

declare(strict_types=1);

namespace Matronator\Mtrgen;

use Matronator\Mtrgen\Template\TemplateNotFoundException;
use Matronator\Parsem\Parser;

class Template
{
    public static function isLegacy(string $path): bool
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($extension, ['json', 'yaml', 'neon'])) {
            if (!is_readable($path)) {
                throw new TemplateNotFoundException($path);
            }
            $contents = file_get_contents($path);
            if (!$contents) {
                throw new TemplateNotFoundException($path);
            }
            if (Parser::isValid($path, $contents)) {
                return true;
            } else if (Parser::isValidBundle($path, $contents)) {
                return true;
            }
        }
        return false;
    }
}
