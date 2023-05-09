<?php

declare(strict_types=1);

namespace Matronator\Mtrgen;

use Matronator\Parsem\Parser;

class Template
{
    public static function isLegacy(string $path): bool
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($extension, ['json', 'yaml', 'neon'])) {
            if (Parser::isValid($path, file_get_contents($path))) {
                return true;
            } else if (Parser::isValidBundle($path, file_get_contents($path))) {
                return true;
            }
        }
        return false;
    }
}
