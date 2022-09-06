# Using templates

## Introduction

In addition to the built-in generators, you can also make your own using **generator templates**.

Generator templates (or just templates) describe the structure of the generated file. Templates can be either YAML, JSON or NEON files and they have to conform to a JSON schema. We will talk more about the schema in the next chapter (*see [mtrgen-template-schema.json](template-structure.md#mtrgen-template-schema)*).

{: .tip }
> It is recommended to name your templates with a `.template` suffix after the name just before the extension (eg. `entity.template.yaml`). If you do that, the [MTRGen Templates Syntax Highlighting](https://marketplace.visualstudio.com/items?itemName=matronator.mtrgen-yaml-templates) VSCode extension, will automatically assign the correct schema to all `*.template.(yaml|yml|json|neon)` files, giving you auto-completion and instant validation.

## Template syntax

Even though generator templates are just regular YAML/JSON/NEON files, they are run through a custom template parser which enables some extra features using a special syntax.

{: .info }
> As of right now, there's currently only one such feature implemented (*template parameters*), but in the future more might come.

You can define **template parameters** anywhere in the template using `<%` and `%>` around the parameter name which can be any alphanumeric string and can contain underscores (basically the name must match this regex `[a-zA-Z0-9_]+?`).

To show you an example, here we define template parameters `prefix` and `dir` (*note the first parameter has whitespace around the name, while the other one doesn't. Both ways are correct, so if you find it more readable to write spaces around the parameter name, you can do so*):

```yaml
name: TestEntity
filename: <% prefix %>TestEntity
path: <%dir%>
file:
...
```

Now when the parser reads the template, it finds these parameters and use them as arguments for the CLI tool during the generation process.

You can then assign any value you want to these parameters during generation. The tool will automatically ask you to provide values for the arguments if you don't provide any in the initial command.

![Terminal output](https://user-images.githubusercontent.com/5470780/188733063-6018db5d-f8ef-4ca7-9bf0-b5ed07e45fa0.png)

But if you know what parameters the template needs, you can pass them as an argument to the `generate` command:

```sh
vendor/bin/mtrgen gen:template --path=my.template.yaml prefix=Hello dir=app/entity/test arg3=42
```
