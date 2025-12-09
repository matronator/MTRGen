---
layout: default
title: Usage
nav_order: 3
has_children: true
---

# Usage

- [CLI usage](#cli-usage)
- [Usage in code](#usage-in-code)

## CLI usage

Now when the parser reads the template, it finds these variables and use them as arguments for the CLI tool during the generation process.

You can then assign any value you want to these parameters during generation. The tool will automatically ask you to provide values for the arguments if you don't provide any in the initial command.

![Terminal output](https://user-images.githubusercontent.com/5470780/188733063-6018db5d-f8ef-4ca7-9bf0-b5ed07e45fa0.png)

But if you know what parameters the template needs, you can pass them as an argument to the `generate` command:

```sh
vendor/bin/mtrgen generate --path=my.template.yaml name=Hello anotherArg=app/entity/test numberArg=42
```

## Usage in code

You can also use the tool in your code. There are several classes that you are most likely to use:

- [`Matronator\Mtrgen\Template\Generator`](./../code-api/generator.md) - This class is used for parsing modern templates (any file format) into file objects.
- `Matronator\Mtrgen\Template\ClassicGenerator` - This class is used for parsing legacy templates (JSON/YAML/NEON) that generate PHP files.
- `Matronator\Mtrgen\ClassicFileGenerator` - This class is used for writing legacy PHP file objects to actual files.
- `Matronator\Mtrgen\Registry\Profile` - This class handles everything related to user profiles.
- `Matronator\Mtrgen\Registry\Connection` - This is the main class for communicating with the online template registry's API.
- [`Matronator\Mtrgen\Store\Storage`](./../code-api/storage.md) - This handles saving, loading and removing templates and bundles from the local store.

There are two main file object classes:

#### `GenericFileObject` (Modern Templates)

For modern templates (any file format), the `GenericFileObject` class serves as the main data structure:

```php
namespace Matronator\Mtrgen;

class GenericFileObject
{
    public string $contents;  // The file contents as a string
    public string $filename;   // The output filename (including extension)
    public string $directory;  // The output directory path

    public function __construct(string $directory, string $filename, string $contents) {
        $this->filename = $filename;
        $this->contents = $contents;
        $this->directory = $directory;
    }
}
```

#### `PhpFileObject` (Legacy Templates)

For legacy templates that generate PHP files, the `PhpFileObject` class is used:

```php
declare(strict_types=1);

namespace Matronator\Mtrgen;

use Nette\PhpGenerator\PhpFile;

class PhpFileObject
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
```

### Parsing templates

#### Modern Templates (Any File Format)

For modern templates, use the `Generator::parseAnyFile()` method:

```php
use Matronator\Mtrgen\Template\Generator;

// Parse a template file
$file = Generator::parseAnyFile('path/to/template.js.mtr', [
    'name' => 'MyComponent',
    'event' => 'click'
]);

// Write the file to disk
Generator::writeFiles($file);
```

#### Legacy Templates (PHP Generation)

For legacy JSON/YAML/NEON templates that generate PHP files, use the `ClassicGenerator` class:

```php
use Matronator\Mtrgen\Template\ClassicGenerator;
use Matronator\Mtrgen\ClassicFileGenerator;

// Parse a legacy template
$file = ClassicGenerator::parseFile('path/to/template.yaml', [
    'name' => 'MyEntity',
    'namespace' => 'App\\Entity'
]);

// Write the PHP file to disk
ClassicFileGenerator::writeFile($file);
```

