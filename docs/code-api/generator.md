---
layout: default
title: Generator
nav_order: 2
parent: API
---

# `Generator` class

{: .warning }
> This page documents the modern `Generator` class for parsing templates of any file format. For legacy PHP generation from JSON/YAML/NEON templates, see the `ClassicGenerator` class.

The `Generator` class provides functionality for parsing modern templates (any file format) and generating files. It uses [Pars'Em](https://github.com/matronator/parsem) for template parsing and variable substitution.

- [Important Classes](#important-classes)
  - [GenericFileObject](#genericfileobject)
  - [TemplateHeader](#templateheader)
- [Constants](#constants)
- [Methods](#methods)
  - [`parseAnyFile`](#parseanyfilestring-path-array-arguments--bool-usec commentsyntax--false-genericfileobject)
  - [`writeFiles`](#writefilesgenericfileobject--genericfileobject-files-void)
  - [`getName`](#getnamestring-content--null-string-path--null-string)
  - [`getTemplateHeader`](#gettemplateheaderstring-content-templateheader)
  - [`removeHeader`](#removeheaderstring-content-string)

## Important Classes

### GenericFileObject

`Matronator\Mtrgen\GenericFileObject` is the main data structure for parsed modern templates. It contains all the information about the file that is needed for writing it to the disk.

```php
namespace Matronator\Mtrgen;

class GenericFileObject
{
    public string $contents;  // The file contents as a string
    public string $filename;  // The output filename (including extension)
    public string $directory; // The output directory path

    public function __construct(string $directory, string $filename, string $contents) {
        $this->filename = $filename;
        $this->contents = $contents;
        $this->directory = $directory;
    }
}
```

### TemplateHeader

`Matronator\Mtrgen\Template\TemplateHeader` represents the metadata header of a template file.

```php
namespace Matronator\Mtrgen\Template;

class TemplateHeader
{
    public string $name;     // Template name (for storage/identification)
    public string $filename;  // Output filename (can use template variables)
    public string $path;     // Output directory path (can use template variables)

    public function __construct(string $name, string $filename, string $path);
    public static function fromArray(array $array): static;
}
```

## Constants

-   `HEADER_PATTERN`: Regular expression pattern for matching the template header block (`/^--- MTRGEN ---(.+)--- \/MTRGEN ---/ms`).
-   `COMMENT_PATTERN`: Regular expression pattern for matching comment-based template variables (for use with `useCommentSyntax` option).

## Methods

### `parseAnyFile(string $path, array $arguments = [], bool $useCommentSyntax = false): GenericFileObject`

Parses a template file of any format and returns a `GenericFileObject` ready to be written to disk.

-   `$path`: The path to the template file.
-   `$arguments`: (optional) An array of arguments to pass to the template variables.
-   `$useCommentSyntax`: (optional) If `true`, uses comment-based syntax (`/*variable*/`) instead of `<%variable%>` syntax.
-   Returns: A `GenericFileObject` representing the parsed file.

**Example:**

```php
$file = Generator::parseAnyFile('component.js.mtr', [
    'name' => 'MyComponent',
    'event' => 'click'
]);
// $file->filename will be "MyComponent.js" (from header)
// $file->directory will be the path from header
// $file->contents will be the parsed template content
```

### `writeFiles(GenericFileObject|GenericFileObject[] $files): void`

Writes one or more parsed file objects to disk. Creates directories if they don't exist.

-   `$files`: A single `GenericFileObject` or an array of `GenericFileObject` instances.

**Example:**

```php
// Write a single file
Generator::writeFiles($file);

// Write multiple files
Generator::writeFiles([$file1, $file2, $file3]);
```

### `getName(?string $content = null, ?string $path = null): string`

Gets the template name from the header. Either `$content` or `$path` must be provided.

-   `$content`: (optional) The template file contents.
-   `$path`: (optional) The path to the template file.
-   Returns: The template name from the header.
-   Throws: `RuntimeException` if neither `$content` nor `$path` is provided, or if the header is missing required fields.

### `getTemplateHeader(string $content): TemplateHeader`

Extracts and parses the template header from the file content.

-   `$content`: The template file contents (including the header).
-   Returns: A `TemplateHeader` object with `name`, `filename`, and `path` properties.
-   Throws: `RuntimeException` if the header is missing required properties.

### `removeHeader(string $content): string`

Removes the template header block from the file content, returning only the template body.

-   `$content`: The template file contents (including the header).
-   Returns: The template content without the header block.

