<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Store;

use Matronator\Mtrgen\FileGenerator;
use Matronator\Mtrgen\Template\Generator;
use Matronator\Parsem\Parser;
use Nette\Utils\Finder;

class Storage
{
    public string $homeDir;
    public string $templateDir;
    public string $store;

    public function __construct()
    {
        $this->homeDir = Path::canonicalize('~/.mtrgen');
        $this->templateDir = Path::canonicalize('~/.mtrgen/templates');
        $this->store = Path::canonicalize('~/.mtrgen/templates.json');

        if (!FileGenerator::folderExist($this->homeDir)) {
            mkdir($this->homeDir, 0777, true);
        }
        if (!FileGenerator::folderExist($this->templateDir)) {
            mkdir($this->templateDir, 0777, true);
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
     */
    public function save(string $filename, ?string $alias = null): bool
    {
        $file = Path::canonicalize($filename);
        $basename = basename($file);

        if (!file_exists($file))
            return false;

        $this->saveEntry($alias ?? Generator::getName($file), $basename);
        copy($file, Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $basename));

        return true;
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
        if (!FileGenerator::folderExist($path))
            return null;

        $store = $this->loadStore();

        $files = Finder::findFiles('*.template.yaml', '*.template.json', '*.template.neon')->in($path);
        $added = 0;
        foreach ($files as $key => $file) {
            if (!Parser::isValid($key)) continue;

            $store = $this->entry($store, Generator::getName($key), $key);
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
    public function getContent(string $name): string
    {
        return file_get_contents(Path::safe($this->getFilename($name)));
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
     * Returns the filename of the template
     * @return string|null
     * @param string $name Name under which the template is stored
     */
    public function getFilename(string $name): ?string
    {
        $store = $this->loadStore();

        if (!isset($store->templates->{$name}))
            return null;

        $filename = $store->templates->{$name};

        return $filename;
    }

    /**
     * Returns the full canonicalized path of the template
     * @return string|null
     * @param string $name Name under which the template is stored
     */
    public function getFullPath(string $name): ?string
    {
        if ($this->getFilename($name) === null)
            return null;

        return Path::canonicalize($this->templateDir . DIRECTORY_SEPARATOR . $this->getFilename($name));
    }

    /**
     * Get all templates from the global store.
     * @return object The object containing all the templates as [name => filename]
     */
    public function listAll(): object
    {
        $store = $this->loadStore();
        return $store->templates;
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
