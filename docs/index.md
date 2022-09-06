---
nav_order: 1
title: Introduction
---

# MTRGen

![MTRGen Logo](assets/images/logo.png)

File generator engine that can generate PHP files from JSON/YAML/NEON templates.

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
matronator/mtrgen v1.0.0 requires composer-runtime-api ^2.2 -> found composer-runtime-api[2.1.0] but it does not match the constraint.
```

Run this command to update composer to the latest version:

```
composer self-update
```

If you can't or don't want to update composer, use version `"^1.0"` of this package as that doesn't depend on Composer runtime API 2.2.

## Quickstart

You run the script from terminal using this command:

```
# To list all available commands
vendor/bin/mtrgen list

# To see usage of the generate command
vendor/bin/mtrgen generate --help
vendor/bin/mtrgen gen -h

# Generate from file
vendor/bin/mtrgen generate --path=my/folder/template.json
vendor/bin/mtrgen gen -p my/folder/template.json

# Generate from the global store
vendor/bin/mtrgen generate TemplateName

# Save a template to the global store
vendor/bin/mtrgen save path/to/template.json
vendor/bin/mtrgen s path/to/template.json

# Optionally provide an alias to save the template under
vendor/bin/mtrgen save path/to/template.json --alias=NewName

# Remove a template from the global store
vendor/bin/mtrgen remove TemplateName
vendor/bin/mtrgen r TemplateName
```

## License

MIT License

Copyright (c) 2022 Matronator

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
