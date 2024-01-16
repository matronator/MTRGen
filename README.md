# MTRGen

![MTRGen Logo](docs/assets/images/logo.png)

*Generate files from templates with ease.*

[![Latest Stable Version](https://poser.pugx.org/matronator/mtrgen/v)](https://packagist.org/packages/matronator/mtrgen)
[![Total Downloads](https://poser.pugx.org/matronator/mtrgen/downloads)](https://packagist.org/packages/matronator/mtrgen)
[![License](https://poser.pugx.org/matronator/mtrgen/license)](https://packagist.org/packages/matronator/mtrgen)

#### [Official Website](https://www.mtrgen.com) | [Documentation](https://www.mtrgen.com/docs/) | [Template Repository](https://www.mtrgen.com/repository)

File generator for source code files.

MTRGen is a CLI tool that can be used in any project and generate files in any language. Create your own templates or use templates from the [online repository](https://www.mtrgen.com/repository). MTRGen is a great tool for generating boilerplate code, but it can also be used to generate any other type of file.

## Requirements

- PHP >= 8.1
- Composer

## Instalation

Install with Composer:

```
composer require matronator/mtrgen
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

## Documentation

[Read the full documentation here](https://www.mtrgen.com/docs/) *- needs to be updated to version 2!*

## Quickstart

Here are some examples of commands you can run with MTRGen:

```bash
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

# Download template from the online repository and save it to the global store
vendor/bin/mtrgen add vendor/template
vendor/bin/mtrgen a vendor/template

# Save a template to the global store
vendor/bin/mtrgen save path/to/template.json
vendor/bin/mtrgen s path/to/template.json

# Optionally provide an alias to save the template under
vendor/bin/mtrgen save path/to/template.json --alias=NewName

# Save a bundle to the global store
vendor/bin/mtrgen save-bundle BundleName path/to/template1.json path/to/template2.json
vendor/bin/mtrgen sb BundleName path/to/template1.json path/to/template2.json

# Remove a template from the global store
vendor/bin/mtrgen remove TemplateName
vendor/bin/mtrgen rm TemplateName

# Repair the global store (remove all templates that don't exist)
vendor/bin/mtrgen repair
vendor/bin/mtrgen r
```

## License

MIT License

Copyright (c) 2022 Matronator

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
