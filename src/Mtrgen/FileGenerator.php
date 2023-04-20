<?php

declare(strict_types=1);

namespace Matronator\Mtrgen;

use Matronator\Mtrgen\Store\Path;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\Printer;

class FileGenerator
{
    public static function writeFile($files): void
    {
        $printer = new PsrPrinter;

        if (is_array($files)) {
            foreach ($files as $file) {
                self::write($file, $printer);
            }
        } else {
            self::write($files, $printer);
        }
    }

    private static function write(FileObject $file, Printer $printer): void
    {
        if (!self::folderExist($file->directory)) {
            mkdir($file->directory, 0777, true);
        }

        file_put_contents(Path::safe($file->directory . $file->filename), $printer->printFile($file->contents));
    }

    /**
     * Check if the folder exists.
     * @return string|false The path to the folder or false if it doesn't exits
     * @param string $folder
     */
    public static function folderExist(string $folder)
    {
        $path = Path::canonicalize($folder);

        return ($path !== false && is_dir($path)) ? $path : false;
    }
}
