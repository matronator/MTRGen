---
layout: default
title: Generator
nav_order: 2
parent: API
---

# `Generator` class

This code provides a `Generator` class for parsing, generating, and manipulating PHP files. It handles various PHP constructs such as namespaces, classes, interfaces, traits, and more. Additionally, it can detect reserved keywords and make names safe for use in generated code.

- [Important Classes](#important-classes)
  - [FileObject](#fileobject)
  - [PhpFile](#phpfile)
- [Constants](#constants)
- [Methods](#methods)
  - [`isBundle`](#isbundlestring-path-string-contents--null-bool)
  - [`parse`](#parsestring-filename-string-contents-array-arguments-fileobject)
  - [`parseFile`](#parsefilestring-path-array-arguments-fileobject)
  - [`generateFile`](#generatefileobject-parsed-array-arguments-fileobject)
  - [`getName`](#getnamestring-path-string-contents--null-string)
  - [`generate`](#generateobject-body-array-args-phpfile)
  - [`is`](#ismixed-subject-bool)
  - [`isReservedKeyword`](#isreservedkeywordstring-name-bool)
  - [`makeNameSafe`](#makenamesafestring-name-string)

## Important Classes

### FileObject

`Matronator\Mtrgen\FileObject` is the main data structure for the parsed templates. It contains all the information about the file that is needed for writing it to the disk.

```php
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

### PhpFile

`Nette\PhpGenerator\PhpFile` is a class from the [Nette](https://nette.org) [PHP Generator library](https://github.com/nette/php-generator). It is used to represent a PHP file and its contents. It is used by the `Generator` class for generating the parsed templates.

Constants
---------

-   `RESERVED_KEYWORDS`: An array of reserved keywords in PHP.
-   `RESERVED_CONSTANTS`: An array of reserved constants in PHP.

Methods
-------

### `isBundle(string $path, ?string $contents = null): bool`

Checks if a given template is a bundle.

-   `$path`: The path of the template file.
-   `$contents`: (optional) The contents of the template file.
-   Returns: A boolean indicating whether the template is a bundle.

### `parse(string $filename, string $contents, array $arguments): FileObject`

Parses and generates a `FileObject` from string content and a filename.

-   `$filename`: The filename of the file to parse.
-   `$contents`: The contents of the file to parse.
-   `$arguments`: An array of arguments for parsing.
-   Returns: A `FileObject` representing the parsed file.

### `parseFile(string $path, array $arguments): FileObject`

Parses and generates a `FileObject` from a file.

-   `$path`: The path to the file to parse.
-   `$arguments`: An array of arguments for parsing.
-   Returns: A `FileObject` representing the parsed file.

### `generateFile(object $parsed, array $arguments): FileObject`

Generates a `FileObject` from a parsed object.

-   `$parsed`: The parsed object.
-   `$arguments`: An array of arguments for generating the file.
-   Returns: A `FileObject` representing the generated file.

### `getName(string $path, ?string $contents = null): string`

Gets the name of the template from a file.

-   `$path`: The path of the template file.
-   `$contents`: (optional) The contents of the template file.
-   Returns: The name of the template.

### `generate(object $body, array $args): PhpFile`

Generates a `PhpFile` from a parsed object.

-   `$body`: The parsed object.
-   `$args`: An array of arguments for generating the file.
-   Returns: A `PhpFile` representing the generated file.

### `is(mixed &$subject): bool`

Shorthand for checking if a variable is set and not empty.

-   `$subject`: The variable to check.
-   Returns: A boolean indicating whether the variable is set and not empty.

### `isReservedKeyword(string $name): bool`

Checks if the given name is a reserved keyword.

-   `$name`: The name to check.
-   Returns: A boolean indicating whether the name is a reserved keyword.

### `makeNameSafe(string $name): string`

Makes the given name safe by adding an underscore if it is a reserved keyword.

-   `$name`: The name to make safe.
-   Returns: A safe name that doesn't conflict with reserved keywords or constants.

