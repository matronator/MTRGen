<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Store;

use Matronator\Mtrgen\Template\Generator;
use InvalidArgumentException;
use Matronator\Mtrgen\ClassicFileGenerator;
use Matronator\Mtrgen\Template;
use Matronator\Mtrgen\Template\ClassicGenerator;
use Matronator\Parsem\Parser;
use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Symfony\Component\Yaml\Yaml;

class Storage
{
    public string $homeDir;
    public string $tempDir;
    public string $templateDir;
    public string $store;

    public function __construct()
    {
        $this->homeDir = Path::canonicalize('~/.mtrgen');
        $this->tempDir = Path::canonicalize('~/.mtrgen/temp');
        $this->templateDir = Path::canonicalize('~/.mtrgen/templates');
        $this->store = Path::canonicalize('~/.mtrgen/templates.json');

        if (!ClassicFileGenerator::folderExist($this->homeDir) && !mkdir($concurrentDirectory = $this->homeDir, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        if (!ClassicFileGenerator::folderExist($this->tempDir) && !mkdir($concurrentDirectory = $this->tempDir, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        if (!ClassicFileGenerator::folderExist($this->templateDir) && !mkdir($concurrentDirectory = $this->templateDir, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        if (!file_exists($this->store)) {
            $this->createStore();
        }
    }

    /**
     * Save template to global store
     * @return boolean True if save is successful, false otherwise
     * @param string $filename
     * @param string|null $alias Alias to save the template under instead of the name defined inside the template
     * @param string|null $parentBundle Name of the bundle if the template belongs to a bundle or null if not a bundle
     */
    public function save(string $filename, ?string $alias = null, ?string $parentBundle = null): bool
    {
        $file = Path::canonicalize($filename);

        if (!file_exists($file))
            return false;

        if (Template::isLegacy($file)) {
            $name = ClassicGenerator::getName($file);
        } else {
            $name = Generator::getName(path: $file);
        }

        $basename = $parentBundle ? $parentBundle . DIRECTORY_SEPARATOR . basename($file) : basename($file);
        if ($parentBundle && !ClassicFileGenerator::folderExist($this->templateDir . DIRECTORY_SEPARATOR . $parentBundle) && !mkdir($concurrentDirectory = $this->templateDir . DIRECTORY_SEPARATOR . $parentBundle, 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $this->saveEntry($alias ?? ($parentBundle ? $parentBundle . ':' . $name : $name), $basename);
        copy($file, Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $basename));

        return true;
    }

    public function saveBundle(object $bundleObject, string $format = 'json'): bool
    {
        $name = $bundleObject->name;

        $contents = '';

        switch ($format) {
            case 'json':
                $contents = json_encode($bundleObject);
                break;
            case 'yaml':
                $contents = Yaml::dump($bundleObject);
                break;
            case 'neon':
                $contents = Neon::encode($bundleObject, true);
                break;
            default:
                throw new InvalidArgumentException('Unsupported bundle format.');
        }

        $filename = "$name.bundle.$format";

        file_put_contents(Path::safe(Path::canonicalize($this->tempDir . DIRECTORY_SEPARATOR . $filename)), $contents);

        if ($this->save($this->tempDir . DIRECTORY_SEPARATOR . $filename, $name)) {
            unlink(Path::safe(Path::canonicalize($this->tempDir . DIRECTORY_SEPARATOR . $filename)));
            return true;
        }

        return false;
    }

    /**
     * Returns the basename of a file
     *
     * @param string $path
     * @return string
     */
    public static function getBasename(string $path): string
    {
        $filename = Path::canonicalize($path);
        return basename($filename);
    }

    public function download(string $identifier, string $filename, string $content): mixed
    {
        $this->saveEntry($identifier, $filename);
        return file_put_contents(Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $filename), $content);
    }

    /**
     * Remove template from global store
     * @return boolean True if removed successfully, false otherwise
     * @param string $name Name under which the template is stored
     */
    public function remove(string $name): bool
    {
        $store = $this->loadStore();

        if (!isset($store->templates->{$name}))
            return false;

        $filename = $this->removeEntry($name);

        if ($this->isBundle($filename)) {
            $parsed = Parser::decodeByExtension(Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $filename));
            foreach ($parsed->templates as $template) {
                $path = $this->removeEntry("$name:" . $template->name);
                unlink(Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $path));
            }
            rmdir(Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $name));
        }

        unlink(Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $filename));

        return true;
    }

    /**
     * Save all templates from a folder.
     * @return int|null The number of saved templates or null on fail
     * @param string $path Path to the folder
     */
    public function saveFolder(string $path): ?int
    {
        $path = Path::canonicalize($path);
        if (!ClassicFileGenerator::folderExist($path))
            return null;

        $store = $this->loadStore();

        $files = Finder::findFiles('*.template.*', '*.mtr.*', '*.mtr')->in($path);
        $added = 0;
        foreach ($files as $key => $file) {
            if (!Parser::isValid($key, $file->read())) continue;

            $store = $this->entry($store, ClassicGenerator::getName($key), $key);
            $added++;
        }

        $this->saveStore($store);

        return $added;
    }

    /**
     * Returns the template contents or false
     * @return string|false
     * @param string $name Name under which the template is stored
     */
    public function getContent(string $name): string|false
    {
        return file_get_contents(Path::safe($this->getFullPath($name)));
    }

    /**
     * Returns an object with properties `filename` (filename of the template)
     * and `contents` (contents of the template) or `null` if not found.
     * @return object|null
     * @param string $name Name under which the template is stored
     */
    public function load(string $name): ?object
    {
        $store = $this->loadStore();

        if (!isset($store->templates->{$name}))
            return null;

        return (object) [
            'filename' => $this->getFilename($name),
            'contents' => $this->getContent($name),
        ];
    }

    /**
     * Returns the filename of the template from local store
     * @return string|null
     * @param string $name Name under which the template is stored
     */
    public function getFilename(string $name): ?string
    {
        $store = $this->loadStore();

        if (!isset($store->templates->{$name}))
            return null;

        return $store->templates->{$name};
    }

    /**
     * Returns the full canonicalized path of the template
     * @return string|null
     * @param string $name Name under which the template is stored
     */
    public function getFullPath(string $name): ?string
    {
        if (is_null($filename = $this->getFilename($name)))
            return null;

        return Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $filename);
    }

    /**
     * Get all templates from the global store.
     * @return object The object containing all the templates as [name => filename]
     */
    public function listAll(): object
    {
        return $this->loadStore()->templates;
    }

    /**
     * Check if template is a bundle by filename
     * @param string $filename
     * @return boolean
     */
    public function isBundle(string $filename): bool
    {
        return (bool) preg_match('/^.+?(\.bundle\.).+?$/', $filename);
    }

    public function repairStore(): void
    {
        $store = $this->loadStore();

        foreach ($store->templates as $name => $filename) {
            if (!file_exists(Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $filename))) {
                unset($store->templates->{$name});
            }
        }

        $this->saveStore($store);
    }

    /**
     * Create and save the global store
     * @return void
     */
    private function createStore(): void
    {
        $store = (object) [
            'templates' => (object)[],
        ];

        $this->saveStore($store);
    }

    /**
     * Load the global store to an object
     * @return object
     */
    private function loadStore(): object
    {
        return json_decode(file_get_contents(Path::safe($this->store)));
    }

    /**
     * Save the store to a file
     * @return void
     * @param object $store
     */
    private function saveStore(object $store): void
    {
        file_put_contents(Path::safe($this->store), json_encode($store));
    }

    /**
     * Save an entry to the store object with `$name` as the object key and `$filename` as the value
     * @return object
     * @param object $store
     * @param string $name Name under which the template will be stored
     * @param string $filename
     */
    private function entry(object $store, string $name, string $filename): object
    {
        $store->templates->{$name} = $filename;
        return $store;
    }

    /**
     * Put the entry to the store object and save the store file.
     * @return void
     * @param string $name
     * @param string $filename
     */
    private function saveEntry(string $name, string $filename): void
    {
        $store = $this->entry($this->loadStore(), $name, $filename);
        $this->saveStore($store);
    }

    /**
     * Removes an entry from the store
     * @return string The filename of the removed template.
     * @param string $name
     */
    private function removeEntry(string $name): string
    {
        $store = $this->loadStore();

        $filename = $store->templates->{$name};
        unset($store->templates->{$name});
        $this->saveStore($store);

        return $filename;
    }
}
