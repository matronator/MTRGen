<?php

declare(strict_types=1);

namespace Matronator\Mtrgen;

use Matronator\Mtrgen\Store\Path;
use Nette\PhpGenerator\PsrPrinter;
use Nette\PhpGenerator\Printer;

class ClassicFileGenerator
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

    private static function write(PhpFileObject $file, Printer $printer): void
    {
        if (!self::folderExist($file->directory) && !mkdir($concurrentDirectory = $file->directory, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        file_put_contents(Path::safe($file->directory . $file->filename), $printer->printFile($file->contents));
    }

    /**
     * Check if the folder exists.
     * @return string|false The path to the folder or false if it doesn't exits
     * @param string $folder
     */
    public static function folderExist(string $folder): mixed
    {
        $path = Path::canonicalize($folder);

        return ($path !== false && is_dir($path)) ? $path : false;
    }
}
