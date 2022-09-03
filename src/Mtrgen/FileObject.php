<?php

declare(strict_types=1);

namespace Matronator\Mtrgen;

use Nette\PhpGenerator\PhpFile;

class FileObject
{
    public PhpFile $contents;

    public string $filename;

    public string $directory;

    public ?string $entity = null;

    public function __construct(string $directory, string $filename, PhpFile $contents, ?string $entity = null) {
        $this->filename = $filename . '.php';
        $this->contents = $contents;
        $this->directory = $directory;
        $this->entity = $entity;
    }
}
