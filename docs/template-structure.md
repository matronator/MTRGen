# Template structure

As mentioned earlier, the template parser supports YAML, JSON and NEON files, so you can write your templates using any of these formats. All templates must follow the same structure, which is defined in a JSON schema. You can find the full schema here: https://files.matronator.com/public/mtrgen/mtrgen-template-schema.json

Here is a simplified version of the schema in YAML format for better readability.

## mtrgen-template-schema

```yaml
name: string # name of the template

filename: string # name of the generated file

path: string # path where to generate the file

file:
  strict: true # boolean - if true, the file will start by declaring strict_types=1

  use: # (optional) a string[] array with a list of dependencies to define with a use statement
    - string
    - string
    - string

  class: # (optional)
    name: string # name of the class

  interface: # (optional)
    name: string # name of the interface

  trait: # (optional)
    name: string # name of the trait

  namespace: # If you want to put your classes and whatnot in a namespace, you can define them here
    name: string # fully qualified name of the namespace (eg. App\MyNamespace\DeeperLevel)
    class: # Same as above
    interface: # Same as above
    trait: # Same as above
    use: # Same as above
```
