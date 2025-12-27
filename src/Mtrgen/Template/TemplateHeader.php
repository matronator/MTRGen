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
        if (!isset($array['name']) || !isset($array['filename']) || !isset($array['path'])) {
            throw new \RuntimeException('Template header is missing some required properties (name, filename, path).');
        }

        if (!isset($array['defaults'])) {
            $array['defaults'] = [];
        }

        return new static($array['name'], $array['filename'], $array['path'], $array['defaults']);
    }
}
