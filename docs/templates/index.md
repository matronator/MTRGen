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
> It is recommended to name your templates with a `.template` suffix after the name just before the extension (eg. `entity.template.yaml`). If you do that, the [MTRGen Templates Syntax Highlighting](https://marketplace.visualstudio.com/items?itemName=matronator.mtrgen-yaml-templates) VSCode extension, will automatically assign the correct schema to all `*.template.(yaml|yml|json|neon)` files, giving you auto-completion and instant validation.

## Template syntax

Even though generator templates are just regular YAML/JSON/NEON files, they are run through a custom template parser which enables some extra features using a special syntax.

### Variables

You can define **template variables** (or *template parameters*) anywhere in the template. Variables are wrapped in `<%` and `%>` with optional space on either side (both `<%nospace%>` and `<% space %>` are valid) and the name must be an alphanumeric string with optional underscore/s (must match this regex `[a-zA-Z0-9_]+?`).

To show you an example, here we define template parameters `prefix` and `dir` (*note the use of both syntaxes, one with spaces and one without*):

```yaml
name: TestEntity
filename: <% prefix %>TestEntity
path: <%dir%>
file:
...
```

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

### Template syntax highlighting for VS Code

To get syntax highlighting for template files (highlight/colorize `<% variable|filter %>` even inside strings), you can download the [MTRGen Templates Syntax Highlighting](https://marketplace.visualstudio.com/items?itemName=matronator.mtrgen-yaml-templates) extension for VS Code.
