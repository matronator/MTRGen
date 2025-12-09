---
layout: default
title: Template structure
nav_order: 2
parent: Templates
---

# Template structure

{: .warning }
> This page describes **legacy templates** - JSON/YAML/NEON files that generate PHP files. For modern templates (any file format), see the [Templates Overview](../).

Legacy templates are YAML, JSON or NEON files that generate PHP files. All legacy templates must follow the same structure, which is defined in a JSON schema. You can find the full schema here: [mtrgen-template-schema.json](https://www.mtrgen.com/storage/schemas/template/latest/mtrgen-template-schema.json)

{: .note }
> Modern templates (any file format) don't use this schema. They simply use a header block and template variables directly in the file content. See the [Templates Overview](../) for more information.

Here is a simplified version of the schema in YAML format for better readability.

## mtrgen-template-schema

```yaml
name: string # name of the template
filename: string # name of the generated file without the `.php` extension
path: string # path where to generate the file
autoImports: boolean # (optional) if true, use statements will be generated automatically for parameter and return types
file:
  strict: boolean # (optional) if true, the file will start by declaring strict_types=1
  use: # (optional) a string[] array with a list of dependencies to define with a use statement
    - string # you can specify an alias for your use statement like this: Some\Class\Name as MyAlias
    - string
    - string as string

  class: # (optional)
    name: string # name of the class
    modifier: final|abstract # (optional) class modifier
    extends: string # (optional) fully qualified class name from which to extend
    implements: # (optional) array of interfaces this class implements (use fully qualified names)
      - string
      - string
    constants: # (optional) array of class constants
      - name: string # constant name
        value: any # constant value
        comments: # (optional) each array entry is one comment line
          - string
          - string
    methods: # (optional) array of class methods
      - name: string # method name
        modifier: final|abstract # (optional) method modifier
        visibility: private|public|protected # (optional) method visibility - public if not specified
        return: string # (optional) the return type of the method
        ref: boolean # (optional) if true, return value by reference
        nullable: boolean # (optional) if true, return value can be null
        static: boolean # (optional) if true, the method will be static
        params: # (optional) array of parameters
          - name: string # parameter name
            type: string # (optional) parameter type
            value: any # (optional) default value
            promoted: boolean # (optional) if true, converts to promoted property
            nullable: boolean # (optional)
            ref: boolean # (optional) if true, parameter is passed by reference
        body: # (optional) array of string[] where each entry represents single line
          - string
          - string
          - string
        comments: # (optional) same as above
    props: # (optional) array of class properties
      - name: string # property name
        visibility: private|public|protected # (optional) property visibility - public if not specified
        type: string # (optional) property type
        value: any # (optional) property value
        getter: boolean # (optional) if true, getter method will be generated automatically
        setter: boolean # (optional) if true, setter method will be generated automatically
        static: boolean # (optional) if true, property will be static
        init: boolean # (optional) if true, property will be initialized
        nullable: boolean # (optional) if true, property will be nullable
        comments: # (optional) same as above
    comments: # (optional) each array entry is one comment line
      - string
      - string
    traits: # (optional) array of traits to use (use fully qualified names)
      - string
      - string

  interface: # (optional)
    name: string # name of the interface
    extends: string # same as above
    constants: # same as above
    methods: # same as above, but without body
    comments: # same as above

  trait: # (optional)
    name: string # name of the trait
    props: # same as above
    methods: # same as above
    comments: # same as above

  namespace: # (optional) If you want to put your classes and whatnot in a namespace, you can define them here
    name: string # fully qualified name of the namespace (eg. App\MyNamespace\DeeperLevel)
    use: # Same as above
    class: # Same as above
    interface: # Same as above
    trait: # Same as above
```
