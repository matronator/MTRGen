<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Matronator\Mtrgen\Template\Generator;
use Tester\Assert;
use Tester\TestCase;

class GeneratorTest extends TestCase
{
    public function testGetTemplateHeader()
    {
        $expected = <<<EOT
// --- MTRGEN ---
// name: js-template
// filename: <% name %>.js
// path: assets/js
// --- MTRGEN ---

document.addEventListener('<% event %>', function() {
    var template = document.querySelector('#<% id %>');
    var templateContent = template.content;
    var clone = document.importNode(templateContent, true);
    document.body.appendChild(clone);
});
EOT;

        $header = Generator::getTemplateHeader($expected);
        Assert::equal($header->name, 'js-template');
        Assert::equal($header->filename, '<% name %>.js');
        Assert::equal($header->path, 'assets/js');
    }
}

(new GeneratorTest)->run();
