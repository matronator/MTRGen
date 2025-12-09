---
nav_order: 1
title: Introduction
---

# MTRGen

![MTRGen Logo](assets/images/logo.png)

File generator engine that can generate files of any format from templates. Templates can be any file format (JavaScript, PHP, TypeScript, Python, etc.) and will generate files in the same format. MTRGen also supports legacy JSON/YAML/NEON templates for generating PHP files.

## Requirements

- PHP >= 7.4
- Composer

## Instalation

Install with Composer:

```
composer require matronator/mtrgen --dev
```

#### Troubleshooting

If you get this error when trying to install:

```
matronator/mtrgen requires composer-runtime-api ^2.2 -> found composer-runtime-api[2.1.0] but it does not match the constraint.
```

Run this command to update composer to the latest version:

```
composer self-update
```

## Quickstart

You run the script from terminal using this command:

```
# To list all available commands
vendor/bin/mtrgen list

# To see usage of the generate command
vendor/bin/mtrgen generate --help
vendor/bin/mtrgen gen -h

# Generate from file (any format)
vendor/bin/mtrgen generate --path=my/folder/component.js.mtr
vendor/bin/mtrgen gen -p my/folder/template.php.mtr

# Generate from the local store
vendor/bin/mtrgen generate TemplateName

# Save a template to the local store
vendor/bin/mtrgen save path/to/template.js.mtr
vendor/bin/mtrgen s path/to/template.php.mtr

# Optionally provide an alias to save the template under
vendor/bin/mtrgen save path/to/template.js.mtr --alias=NewName

# Remove a template from the local store
vendor/bin/mtrgen remove TemplateName
vendor/bin/mtrgen r TemplateName
```

## Acknowledgement

This project uses [Pars'Em](https://github.com/matronator/parsem) for template parsing and variable substitution. For legacy PHP file generation, it uses [Nette](https://nette.org)'s [`php-generator`](https://github.com/nette/php-generator) package.

## License

MIT License

Copyright (c) 2022 Matronator

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
