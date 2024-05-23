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

- [`Matronator\Mtrgen\Template\Generator`](./../code-api/generator.md) - This class is used for parsing the templates into file objects.
- `Matronator\Mtrgen\FileGenerator` - This class is used for writing parsed file objects to actual files.
- `Matronator\Mtrgen\Registry\Profile` - This class handles everything related to user profiles.
- `Matronator\Mtrgen\Registry\Connection` - This is the main class for communicating with the online template registry's API.
- [`Matronator\Mtrgen\Store\Storage`](./../code-api/storage.md) - This handles saving, loading and removing templates and bundles from the local store.

There is also a `Matronator\Mtrgen\FileObject` class that serves as the main data structure for the parsed templates. It contains all the information about the file that is needed for writing it to the disk.

#### `FileObject` structure

```php
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
```

### Parsing templates

