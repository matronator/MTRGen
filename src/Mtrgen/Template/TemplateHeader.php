<?php

declare(strict_types=1);

namespace Matronator\Mtrgen;

class TemplateHeader
{
    public string $name;
    public string $filename;
    public string $path;

    public function __construct(string $name, string $filename, string $path)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->path = $path;
    }

    public static function fromArray(array $array): static
    {
        return new static($array['name'], $array['filename'], $array['path']);
    }
}
