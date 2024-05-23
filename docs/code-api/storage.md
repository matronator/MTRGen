---
layout: default
title: Storage
nav_order: 3
parent: API
---

# `Storage` class

The `Storage` class is a utility class responsible for managing file storage and retrieval for templates in a local store.

- [Properties](#properties)
- [Methods](#methods)
  - [`__construct()`](#__construct)
  - [`save`](#savestring-filename-string-alias--null-string-bundle--null-bool)
  - [`saveBundle`](#savebundleobject-bundleobject-string-format--json-bool)
  - [`getBasename`](#getbasenamestring-path-string)
  - [`download`](#downloadstring-identifier-string-filename-string-content-mixed)
  - [`remove`](#removestring-name-bool)
  - [`saveFolder`](#savefolderstring-path-int)
  - [`getContent`](#getcontentstring-name-string)
  - [`load`](#loadstring-name-object)
  - [`getFilename`](#getfilenamestring-name-string)
  - [`getFullPath`](#getfullpathstring-name-string)

Properties
----------

-   `$homeDir`: The path to the main directory for storing templates and related files.
-   `$tempDir`: The path to the directory for storing temporary files.
-   `$templateDir`: The path to the directory for storing template files.
-   `$store`: The path to the file that stores metadata about the templates in the global store.

Methods
-------

### `__construct()`

The constructor initializes the `Storage` object and sets up the directory structure if it doesn't exist.

### `save(string $filename, ?string $alias = null, ?string $bundle = null): bool`

Saves a template to the global store. It takes the following parameters:

-   `$filename`: The path to the template file.
-   `$alias` (optional): The alias to save the template under, instead of the name defined inside the template.
-   `$bundle` (optional): The bundle name, if the template is part of a bundle.

Returns `true` if the save is successful, `false` otherwise.

### `saveBundle(object $bundleObject, string $format = 'json'): bool`

Saves a bundle of templates to the global store. It takes the following parameters:

-   `$bundleObject`: An object containing the bundle information.
-   `$format`: The format of the bundle file (default is 'json').

Returns `true` if the save is successful, `false` otherwise.

### `getBasename(string $path): string`

Returns the basename of a file. It takes the following parameter:

-   `$path`: The path to the file.

### `download(string $identifier, string $filename, string $content): mixed`

Downloads a template and saves it to the global store. It takes the following parameters:

-   `$identifier`: The identifier for the template.
-   `$filename`: The filename for the template.
-   `$content`: The content of the template.

Returns the number of bytes written to the file, or `false` on failure.

### `remove(string $name): bool`

Removes a template from the global store. It takes the following parameter:

-   `$name`: The name under which the template is stored.

Returns `true` if removed successfully, `false` otherwise.

### `saveFolder(string $path): ?int`

Saves all templates from a folder to the global store. It takes the following parameter:

-   `$path`: The path to the folder.

Returns the number of saved templates or `null` on failure.

### `getContent(string $name): string`

Returns the content of a template, or `false` if the template is not found. It takes the following parameter:

-   `$name`: The name under which the template is stored.

### `load(string $name): ?object`

Loads a template from the global store and returns an object with properties `filename` (filename of the template) and `contents` (contents of the template), or `null` if not found. It takes the following parameter:

-   `$name`: The name under which the template is stored.

### `getFilename(string $name): ?string`

Returns the filename of the template from the local store, or `null` if not found. It takes the following parameter:

-   `$name`: The name under which the template is stored.

### `getFullPath(string $name): ?string`

Returns the full canonicalized path of the template, or `null` if not found. It takes the following parameter:

-   `$name`: The name under which the template is stored.

### `listAll(): object`

Retrieves all templates from the global store and returns them as an object containing key-value pairs of template names and their corresponding filenames.

-   Returns: An object containing all the templates as [name => filename].

### `isBundle(string $filename): bool`

Checks if a given template is a bundle by analyzing its filename.

-   Parameters:
    -   `$filename`: The name of the template file.
-   Returns: `true` if the template is a bundle, and `false` otherwise.
