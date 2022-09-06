---
layout: default
title: Template structure
nav_order: 2
parent: Templates
---

# Template structure

As mentioned earlier, the template parser supports YAML, JSON and NEON files, so you can write your templates using any of these formats. All templates must follow the same structure, which is defined in a JSON schema. You can find the full schema here: https://files.matronator.com/public/mtrgen/mtrgen-template-schema.json

Here is a simplified version of the schema in YAML format for better readability.

## mtrgen-template-schema

```yaml
name: string # name of the template

filename: string # name of the generated file

path: string # path where to generate the file

file:
  strict: true # (optional) boolean - if true, the file will start by declaring strict_types=1

  use: # (optional) a string[] array with a list of dependencies to define with a use statement
    - string
    - string
    - string

  class: # (optional)
    name: string # name of the class
    modifier: final|abstract # (optional) class modifier
    extends: string # (optional) fully qualified class name from which to extend

    constants: # (optional) array of class constants
      - name: string # constant name
        value: number # constant value

    props: # (optional) array of class properties
      - name: string # property name
        visibility: private|public|protected # (optional) property visibility - public if not specified
        type: string # (optional) property type
        value: app/model/database/entity # (optional) property value

  interface: # (optional)
    name: string # name of the interface

  trait: # (optional)
    name: string # name of the trait

  namespace: # (optional) If you want to put your classes and whatnot in a namespace, you can define them here
    name: string # fully qualified name of the namespace (eg. App\MyNamespace\DeeperLevel)
    class: # Same as above
    interface: # Same as above
    trait: # Same as above
    use: # Same as above
```
