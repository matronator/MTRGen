<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Template;

class TemplateHeader
{
    public string $name;
    public string $filename;
    public string $path;
    public array $defaults = [];

    public function __construct(string $name, string $filename, string $path, array $defaults = [])
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->path = $path;
        $this->defaults = $defaults;
    }

    public static function fromArray(array $array): static
    {
        return new static($array['name'], $array['filename'], $array['path'], $array['defaults'] ?? []);
    }
}
