---
layout: default
title: Online registry
nav_order: 4
has_children: true
---

# Online Registry

In addition to saving your templates locally from a file, you can also download and save templates that others have made using the **online template registry**.

## Adding templates to your local store

You can download a template from the registry and add it to your local store using the `add` command. You can specify the identifier as an argument or if you run the command without any arguments, it will ask you for an identifier after you run it.

Valid identifier is in the format `vendor/name` where `vendor` is the template author's username that they published the template under and `name` is the name of the template itself. So for example it could look like this: `matronator/my-template`.

### Using the `add` command

```
vendor/bin/mtrgen add vendor/template
```

After you saved the template to your local store, you can start generating files with it, using the identifier for the `name` argument, like so:

```
vendor/bin/mtrgen generate vendor/template
```

## Generating from registry without saving the template locally

If you just want to generate a file from a template in the online registry one-time only and don't want to add it to your local store, you can use the `use` command. This command works just like the `generate` command, except it takes an identifier as an argument and searches the online registry instead of your local store.

When it finds the template, it will prompt you to provide arguments for the template (if there are any) and generates the PHP file without saving the template in your local store. This is useful if you just want to use a template one time and know you won't be needing it in the future again.

### Using the `use` command

```
vendor/bin/mtrgen use vendor/template-name
```