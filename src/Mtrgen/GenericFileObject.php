<?php

namespace Matronator\Mtrgen;

class GenericFileObject
{
    public string $contents;

    public string $filename;

    public string $directory;

    public function __construct(string $directory, string $filename, string $contents) {
        $this->filename = $filename . '.php';
        $this->contents = $contents;
        $this->directory = $directory;
    }
}
