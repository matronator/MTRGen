---
nav_order: 2
title: CLI Commands
---

# CLI Commands

Here is a list of all the commands the CLI utility supports (excluding default `symfony/console` commands).

### Command list:

- [`generate`, `gen` (default)](#generate-gen-default)
- [`save`, `s`](#save-s)
- [`save-bundle`, `sb`, `save-b`](#save-bundle-sb-save-b)
- [`saved`, `ls`](#saved-ls)
- [`remove`, `rm`](#remove-rm)
- [`validate`, `v`](#validate-v)
- [`add`, `a`](#add-a)
- [`signup`, `sign`](#signup-sign)
- [`login`, `in`](#login-in)
- [`publish`, `pub`](#publish-pub)
- [`use`, `u`](#use-u)


## `generate`, `gen` (default)

The `generate` command is the default command of the CLI tool, meaning it will be run if no command is provided (eg. `vendor/bin/mtrgen` or with some parameters: `vendor/bin/mtrgen -p my/path/to/file.json`). It is used for generating PHP files from a template or a bundle.

### Usage

```sh
vendor/bin/mtrgen generate [options] [--] [<name> [<args>...]]
gen # Alias
```

### Arguments

`name` - The name of the template to generate under which it is saved in the local store. If no name is provided, the program will show you a list of all the templates you have saved in your local store and you can choose a template to generate from that list.

`args` - Arguments to pass to the template (`key=value` items separated by space). If no arguments are provided, the program will ask you to provide values for the arguments it finds when parsing the template.

### Options

`-p, --path` - The path to a template file. You can use this instead of the `name` argument.

## `save`, `s`

Saves a template to the local store.

### Usage

```sh
vendor/bin/mtrgen save [options] [--] <path>
s # Alias
```

### Arguments

`path` - The path to a template file.

### Options

`-a, --alias` - Alias to use instead of the name defined inside the template.

## `save-bundle`, `sb`, `save-b`

Creates a bundle from two or more template files and add it to your local store.

### Usage

```sh
save-bundle [options] [--] <name> [<templates>...]
sb # Alias
save-b # Alias
```

### Arguments

`name` - The name of the bundle.

`templates` - The paths to the template files separated by space.

### Options

`-f, --format` - The file format that should be used for the bundle. Options: `json|yaml|neon`.

## `saved`, `ls`

Lists all the templates saved in your local store.

### Usage

```sh
vendor/bin/mtrgen saved
ls # Alias
```

## `remove`, `rm`

Removes a template or bundle from your local store.

### Usage

```sh
vendor/bin/mtrgen remove <name>
rm # Alias
```

### Arguments

`name` - The name of the template or bundle to remove.

## `validate`, `v`

Checks if a file is valid template or bundle.

### Usage

```sh
vendor/bin/mtrgen validate <path>
v # Alias
```

### Arguments

`path` - The path to the file to validate.

## `add`, `a`

Adds a template from the online registry to your local store.

### Usage

```sh
vendor/bin/mtrgen add <identifier>
a # Alias
```

### Arguments

`identifier` - Full identifier of the template (`vendor/name`).

## `signup`, `sign`

Creates new user account on the online registry.

### Usage

```sh
vendor/bin/mtrgen signup <username> <password> [<email>]
sign # Alias
```

### Arguments

`username` - The username to use for the account.

`password` - The password to use for the account.

`email` (optional) - The email address to use for the account.

## `login`, `in`

Logs in to the online registry.

### Usage

```sh
vendor/bin/mtrgen login [options] [--] <username> <password>
in # Alias
```

### Arguments

`username` - The username to use for the account.

`password` - The password to use for the account.

### Options

`-d, --duration` - The duration (in hours) for which the user should stay logged in. Use 0 to never logout (not recommended). (default=`24`)

## `publish`, `pub`

Publishes a template to the online registry.

### Usage

```sh
vendor/bin/mtrgen publish [options] [--] [<name>]
pub # Alias
```

### Arguments

`name` - The name of the template to publish. If no name is provided, the program will show you a list of all the templates you have saved in your local store and you can choose a template to publish from that list.

### Options

`-p, --path` - The path to a template file. You can use this instead of the `name` argument.

## `use`, `u`

Uses a template from the online registry to generate a file without saving that template locally.

### Usage

```sh
vendor/bin/mtrgen use [<identifier> [<args>...]]
u # Alias
```

### Arguments

`identifier` - Full identifier of the template (`vendor/name`).

`args` - Arguments to pass to the template (`key=value` items separated by space). If no arguments are provided, the program will ask you to provide values for the arguments it finds when parsing the template.
