---
layout: default
title: Usage
nav_order: 3
has_children: true
---

# Usage

## CLI usage

Now when the parser reads the template, it finds these variables and use them as arguments for the CLI tool during the generation process.

You can then assign any value you want to these parameters during generation. The tool will automatically ask you to provide values for the arguments if you don't provide any in the initial command.

![Terminal output](https://user-images.githubusercontent.com/5470780/188733063-6018db5d-f8ef-4ca7-9bf0-b5ed07e45fa0.png)

But if you know what parameters the template needs, you can pass them as an argument to the `generate` command:

```sh
vendor/bin/mtrgen generate --path=my.template.yaml name=Hello anotherArg=app/entity/test numberArg=42
```
