<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Template;

class TemplateNotFoundException extends \RuntimeException
{
    public function __construct(string $path, \Throwable|null $previous = null)
    {
        parent::__construct(message: sprintf('Template "%s" was not found', $path), previous: $previous);
    }
}
