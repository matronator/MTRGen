<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Template;

use Matronator\Mtrgen\FileObject;
use Matronator\Mtrgen\Store\Path;
use Matronator\Parsem\Parser;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Property;

class Generator
{
    public static function isBundle(string $path, ?string $contents = null): bool
    {
        $object = Parser::decodeByExtension($path, $contents);
        return self::is($object->templates);
    }

    public static function parse(string $filename, string $contents, array $arguments): FileObject
    {
        $object = Parser::decodeByExtension($filename, $contents);

        return self::generateFile($object, $arguments);
    }

    public static function parseFile(string $path, array $arguments): FileObject
    {
        $object = Parser::decodeByExtension($path);

        return self::generateFile($object, $arguments);
    }

    public static function generateFile(object $parsed, array $arguments): FileObject
    {
        $filename = Parser::parseString($parsed->filename, $arguments);
        $outDir = Parser::parseString(Path::canonicalize($parsed->path) . DIRECTORY_SEPARATOR, $arguments);

        $file = self::generate($parsed->file, $arguments);

        if (isset($parsed->autoImport) && $parsed->autoImport === true) $file = self::autoImportTypes($file);

        return new FileObject($outDir, $filename, $file);
    }

    public static function getName(string $path)
    {
        $object = Parser::decodeByExtension($path);

        return $object->name;
    }

    public static function generate(object $body, array $args): PhpFile
    {
        $file = new PhpFile;

        if (self::is($body->strict) && $body->strict === true) $file->setStrictTypes();

        if (isset($body->namespace)) {
            self::namespace($body->namespace, $file, $args);
        }

        if (isset($body->class)) {
            self::class($body->class, $args, $file);
        }
        if (isset($body->interface)) {
            self::interface($body->interface, $args, $file);
        }
        if (isset($body->trait)) {
            self::trait($body->trait, $args, $file);
        }

        return $file;
    }

    /**
     * @return ClassType
     * @param object $object
     * @param array $args
     * @param PhpFile|PhpNamespace|null $parent
     */
    private static function class(object $object, array $args, &$parent = null): ClassType
    {
        $class = !$parent ? new ClassType(Parser::parseString($object->name, $args)) : $parent->addClass(Parser::parseString($object->name, $args));

        return self::defineObject($object, $args, $class);
    }

    /**
     * @return ClassType
     * @param object $object
     * @param array $args
     * @param PhpFile|PhpNamespace|null $parent
     */
    private static function interface(object $object, array $args, &$parent = null): ClassType
    {
        $interface = !$parent ? ClassType::interface(Parser::parseString($object->name, $args)) : $parent->addInterface(Parser::parseString($object->name, $args));

        return self::defineObject($object, $args, $interface);
    }

    /**
     * @return ClassType
     * @param object $object
     * @param array $args
     * @param PhpFile|PhpNamespace|null $parent
     */
    private static function trait(object $object, array $args, &$parent = null): ClassType
    {
        $trait = !$parent ? ClassType::trait(Parser::parseString($object->name, $args)) : $parent->addTrait(Parser::parseString($object->name, $args));

        return self::defineObject($object, $args, $trait);
    }

    /**
     * @return ClassType
     * @param object $object
     * @param array $args
     * @param ClassType $class
     */
    private static function defineObject(object $object, array $args, ClassType $class): ClassType
    {
        if (self::is($object->modifier)) {
            if ($object->modifier === 'final') $class->setFinal();
            if ($object->modifier === 'abstract') $class->setAbstract();
        }
        if (self::is($object->extends)) $class->setExtends(Parser::parseString($object->extends, $args));
        if (self::is($object->implements)) {
            foreach ($object->implements as $implement) {
                $class->addImplements(Parser::parseString($implement, $args));
            }
        }
        if (self::is($object->traits)) {
            foreach ($object->traits as $trait) {
                $class->addTrait(Parser::parseString($trait, $args));
            }
        }
        if (self::is($object->constants)) {
            foreach ($object->constants as $const) {
                $constant = $class->addConstant(Parser::parseString($const->name, $args), Parser::parseString($const->value, $args));
                if (self::is($const->visibility)) $constant->setVisibility($const->visibility);
                if (self::is($const->comments)) {
                    foreach ($const->comments as $comment) {
                        $constant->addComment(Parser::parseString($comment, $args));
                    }
                }
            }
        }
        if (self::is($object->props)) {
            foreach ($object->props as $prop) {
                $property = self::property($prop, $args, $class);
                $class->addMember($property);
            }
        }
        if (self::is($object->methods)) {
            foreach ($object->methods as $method) {
                $classMethod = self::method($method, $args);
                $class->addMember($classMethod);
            }
        }
        if (self::is($object->comments)) {
            foreach ($object->comments as $comment) {
                $class->addComment(Parser::parseString($comment, $args));
            }
        }

        return $class;
    }

    private static function namespace(object $object, PhpFile &$file, array $args): PhpNamespace
    {
        $namespace = $file->addNamespace(Parser::parseString($object->name, $args));

        if (self::is($object->use)) {
            foreach ($object->use as $use) {
                if (strpos($use, ' as ') !== false) {
                    $parts = explode(' as ', $use);
                    $namespace->addUse(Parser::parseString($parts[0], $args), Parser::parseString($parts[1], $args));
                } else {
                    $namespace->addUse(Parser::parseString($use, $args));
                }
            }
        }
        if (isset($object->class)) {
            self::class($object->class, $args, $namespace);
        }
        if (isset($object->interface)) {
            self::interface($object->interface, $args, $namespace);
        }
        if (isset($object->trait)) {
            self::trait($object->trait, $args, $namespace);
        }

        return $namespace;
    }

    private static function property(object $prop, array $args, ?ClassType $class = null): Property
    {
        $property = new Property(Parser::parseString($prop->name, $args));

        if (self::is($prop->visibility)) $property->setVisibility($prop->visibility);
        if (self::is($prop->static)) $property->setStatic($prop->static);
        if (self::is($prop->type)) $property->setType(Parser::parseString($prop->type, $args));
        if (self::is($prop->nullable) && $prop->nullable) $property->setNullable($prop->nullable);
        if (self::is($prop->value)) $property->setValue(Parser::parseString($prop->value, $args));
        if (self::is($prop->init) && $prop->init) $property->setInitialized($prop->init);
        if (self::is($prop->comments)) {
            foreach ($prop->comments as $comment) {
                $property->addComment(Parser::parseString($comment, $args));
            }
        }
        if ($class) {
            if (self::is($prop->getter) && $prop->getter) {
                $getter = self::getter($property->getName(), $property->getType());
                $class->addMember($getter);
            }
            if (self::is($prop->setter) && $prop->setter) {
                $setter = self::setter($property->getName(), $property->getType());
                $class->addMember($setter);
            }
        }

        return $property;
    }

    private static function getter(string $name, string $type): Method
    {
        $getter = new Method('get' . ucfirst($name));
        $getter->addComment("@return $type");

        $getter->setReturnType($type);
        $getter->addBody("return \$this->$name;");

        return $getter;
    }

    private static function setter(string $name, string $type): Method
    {
        $setter = new Method('set' . ucfirst($name));
        $setter->addComment("@param $type \$$name");

        $param = $setter->addParameter($name);
        $param->setType($type);

        $setter->setReturnType('void');
        $setter->addBody("\$this->$name = \$$name;");

        return $setter;
    }

    private static function method(object $object, array $args): Method
    {
        $method = new Method(Parser::parseString($object->name, $args));

        if (self::is($object->modifier)) {
            if ($object->modifier === 'final') $method->setFinal();
            if ($object->modifier === 'abstract') $method->setAbstract();
        }
        if (self::is($object->visibility)) $method->setVisibility($object->visibility);
        if (self::is($object->static)) $method->setStatic($object->static);
        if (self::is($object->nullable) && $object->nullable) $method->setReturnNullable($object->nullable);
        if (self::is($object->ref) && $object->ref) $method->setReturnReference($object->ref);
        if (self::is($object->return)) $method->setReturnType(Parser::parseString($object->return, $args));
        if (self::is($object->comments)) {
            foreach ($object->comments as $comment) {
                $method->addComment(Parser::parseString($comment, $args));
            }
        }
        if (self::is($object->params)) {
            foreach ($object->params as $param) {
                if (isset($param->promoted) && $param->promoted) {
                    $promotedParam = $method->addPromotedParameter(Parser::parseString($param->name, $args));
                    if (self::is($param->nullable) && $param->nullable) $promotedParam->setNullable($param->nullable);
                    if (self::is($param->type)) $promotedParam->setType(Parser::parseString($param->type, $args));
                    if (self::is($param->value)) $promotedParam->setDefaultValue(Parser::parseString($param->value, $args));
                    if (self::is($param->ref) && $param->ref) $promotedParam->setReference($param->ref);
                    if (self::is($param->visibility)) $promotedParam->setVisibility($param->visibility);
                } else {
                    $parameter = $method->addParameter(Parser::parseString($param->name, $args));
                    if (self::is($param->nullable) && $param->nullable) $parameter->setNullable($param->nullable);
                    if (self::is($param->type)) $parameter->setType(Parser::parseString($param->type, $args));
                    if (self::is($param->value)) $parameter->setDefaultValue(Parser::parseString($param->value, $args));
                    if (self::is($param->ref) && $param->ref) $parameter->setReference($param->ref);
                }
            }
        }
        if (self::is($object->body)) {
            foreach ($object->body as $body) {
                $method->addBody(Parser::parseString($body, $args));
            }
        }

        return $method;
    }

    private static function autoImportTypes(PhpFile &$file): PhpFile
    {
        foreach ($file->getNamespaces() as $namespace) {
            $classnames = [];
            foreach ($namespace->getClasses() as $class) {
                $classnames[] = $namespace->resolveName($class->getName());
                $types = [];
                foreach ($class->getMethods() as $method) {
                    $types[] = $method->getReturnType();
                    array_map(function ($param) use (&$types) { $types[] = $param->getType(); }, $method->getParameters());
                }
                array_map(function ($param) use (&$types) { $types[] = $param->getType(); }, $class->getProperties());
                foreach (array_filter($types, function($item) use ($classnames) {
                    return !in_array($item, ['string', 'int', 'bool', 'float', 'null', 'false', 'true', 'array', 'object', 'void', false, null]) && !in_array($item, $classnames);
                }) as $type) {
                    $namespace->addUse((string) $type);
                }
            }
        }

        return $file;
    }

    public static function is(&$subject): bool
    {
        return is_array($subject) ? isset($subject) && count($subject) > 0 : isset($subject);
    }
}
