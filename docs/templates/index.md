---
layout: default
title: Templates
nav_order: 2
has_children: true
---

# Templates

## Introduction

Generator templates (or just templates) describe the structure of the generated file. Templates can be either YAML, JSON or NEON files and they have to conform to a JSON schema. We will talk more about the schema in the next chapter (*see [mtrgen-template-schema.json](template-structure.md#mtrgen-template-schema)*).

{: .tip }
> It is recommended to name your templates with a `.mtr` suffix after the name right after the extension (eg. `entity.yaml.mtr`). If you do that, the [MTRGen Templates Syntax Highlighting](https://marketplace.visualstudio.com/items?itemName=matronator.mtrgen-yaml-templates) VSCode extension, will add syntax highlighting and snippets to all `*.mtr` files.

## Template syntax

Templates are parsed with [Pars'Em](https://github.com/matronator/parsem) template parser which enables some extra features to make the templates more generic and useful.

### Header

All templates must include a header block with details about the template at the very top of the file starting on the first line. Templates without a header are not considered valid and even though can still be parsed with Pars'Em, they won't work with the `mtrgen` CLI tool.

The header block looks like this:

```
--- MTRGEN ---
name: template-name
filename: <% name|firstUpper %>.php
path: ./app/Controllers
--- MTRGEN ---

// Rest of the template
```

You can use template variables for the header values, except for the `name` field, which must be known beforehand to be able to correctly save and use the template later.

#### Header fields

##### `name`

A unique name of the template to be saved under in the local store. If published to online repository, it will be used for the identifier (user/**name**). Can't use template variables in the value.

##### `filename`

The filename of the generated file. Can use template variables to make the filename dynamic.

##### `path`

Path to the directory into which to generate the file. Can use variables to make path dynamic.

### Conditions

You can use conditions in your templates by using the `<% if %>` and `<% endif %>` tags. The condition must be a valid PHP expression that will be evaluated and if it returns `true`, the content between the tags will be included in the final output.

To use a variable provided in the arguments array in a condition, you must use the `$` sign before the variable name, like this: `<% if $variable == 'value' %>`. The `$` sign is used to differentiate between the template variable and a keyword such as `true` or `null`.

{: .note-title .code-break }
> Example
>
> ```yaml
> some:
> key
> <% if $variable === 'value' %>
> with value
> <% endif %>
> ```
>
> If you provide an argument `['variable' => 'value']`, the final output will be this:
>
> ```yaml
> some:
> key
> with value
> ```

### Variables

You can define **template variables** (or *placeholder variables*) anywhere in the template. Variables are wrapped in `<%` and `%>` with optional space on either side (both `<%nospace%>` and `<% space %>` are valid) and the name must be an alphanumeric string with optional underscore/s (must match this regex `[a-zA-Z0-9_]+?`).

To show you an example, here we define template parameters `prefix` and `dir` (*note the use of both syntaxes, one with spaces and one without*):

```yaml
name: TestEntity
filename: <% prefix %>TestEntity
path: <%dir%>
file:
...
```

#### Default values

Variables can optionally have a default value that will be used if no argument is provided for that variable during parsing. You can specify a default value like this: `<% variable='Default' %>`

{: .tip .code-break }
> If you're going to use filters, the default value comes before the filter, ie.: `<% variable='Default'|filter %>`

### Filters

You can optionally provide filter to a variable by placing the pipe symbol `|` right after the variable name and the filter right after that (no space around the `|` pipe symbol), like this: `<% variable|filter %>`.

The filter can be any PHP function with the variable used as the function's argument.

{: .note-title .code-break }
> Example
>
> If we have `<% foo|strtoupper %>` in the template and we provide an argument `['foo' => 'hello world']`, the final (parsed) output will be this: `HELLO WORLD`.

#### Additional filter arguments

Filters can also have additional arguments apart from the variable itself. To pass additional arguments to a filter, write it like this: `<% var|filter:'arg','arg2',20,true %>`. Each argument after the colon is separated by a comma and can have any scalar type as a value.

The first argument will always the variable on which we're declaring the filter, with any other arguments passed after that.

{: .note-title }
> Example
>
> If we have `<% foo|substr:1,3 %>` and provide an argument `['foo' => 'abcdef']`, the filter will get called like this using the arguments provided: `substr('abcdef', 1, 3)`. The final parsed output will thus be this: `bcd`.

*So far you can specify only one filter per variable declaration, but that will probably change in the future.*

#### Built-in filters

There are a few built-in filters that you can use:

`upper` - Converts the variable to uppercase

`lower` - Converts the variable to lowercase

`upperFirst` - Converts the first character of the variable to uppercase

`lowerFirst` - Converts the first character of the variable to lowercase

`first` - Returns the first character of the variable

`last` - Returns the last character of the variable

`camelCase` - Converts the variable to camelCase

`snakeCase` - Converts the variable to snake_case

`kebabCase` - Converts the variable to kebab-case

`pascalCase` - Converts the variable to PascalCase

`titleCase` - Converts the variable to Title Case

`length` - Returns the length of the variable

`reverse` - Reverses the variable

`random` - Returns a random character from the variable

`truncate` - Truncates the variable to the specified length

## VSCode Extension

To get syntax highlighting for template files (highlight/colorize `<% variable|filter %>` and `<% if %><% endif %>` even inside strings) and some helper snippets (like `---` to insert the template header), you can download the [MTRGen Templates Syntax Highlighting](https://marketplace.visualstudio.com/items?itemName=matronator.mtrgen-yaml-templates) extension for VS Code.
