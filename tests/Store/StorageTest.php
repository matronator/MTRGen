<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Tests\Store;

require __DIR__ . '/../bootstrap.php';

use Matronator\Mtrgen\Store\Path;
use Matronator\Mtrgen\Store\Storage;
use Tester\Assert;
use Tester\TestCase;

class StorageTest extends TestCase
{
    /**
     * @var Storage
     */
    private $storage;
    
    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        $this->storage = new Storage();
    }

    public function testStorage()
    {
        Assert::true(file_exists($this->storage->store), 'Store file exists');
    }

    // TODO: Update tests to V2

    // /**
    //  * Test save method.
    //  */
    // public function testSave(): void
    // {
    //     $filePath = __DIR__ . '/../templates/js-template.mtr.js';
    //     Assert::true($this->storage->save($filePath));
    // }

    // /**
    //  * Test save method with alias.
    //  */
    // public function testSaveWithAlias(): void
    // {
    //     $filePath = __DIR__ . '/../templates/js-template.mtr.js';
    //     $alias = 'test-alias';
    //     Assert::true($this->storage->save($filePath, $alias));
    // }

    // /**
    //  * Test save method with bundle.
    //  */
    // public function testSaveWithBundle(): void
    // {
    //     $filePath = __DIR__ . '/../templates/test-bundle/test.template.yaml';
    //     $bundle = 'test-bundle';
    //     Assert::true($this->storage->save($filePath, null, $bundle));
    // }

    // /**
    //  * Test save bundle method.
    //  */
    // public function testSaveBundle(): void
    // {
    //     $bundle = (object) [
    //         'name' => 'test-bundle',
    //         'templates' => [
    //             (object) [
    //                 'name' => 'test.template.yaml',
    //                 'content' => 'test content'
    //             ]
    //         ]
    //     ];

    //     Assert::true($this->storage->saveBundle($bundle));
    // }

    // /**
    //  * Test load method.
    //  */
    // public function testLoad(): void
    // {
    //     $templateName = 'test.template.yaml';
    //     $expected = (object) [
    //         'filename' => "$templateName",
    //         'contents' => "name: test-template\nfilename: <%name%>Entity\npath: app/model/Database/Entity\nfile:\n    class:\n        name: <%name%>Entity\n"
    //     ];
    //     Assert::equal($expected, $this->storage->load("test-template"));
    // }

    // /**
    //  * Test get filename method.
    //  */
    // public function testGetFilename(): void
    // {
    //     $templateName = 'test.template.yaml';
    //     $expectedFilename = "$templateName";
    //     Assert::equal($expectedFilename, $this->storage->getFilename("test-template"));
    // }

    // /**
    //  * Test get full path method.
    //  */
    // public function testGetFullPath(): void
    // {
    //     $templateName = 'test.template.yaml';
    //     $expectedFullPath = Path::canonicalize('~/.mtrgen/templates/test.template.yaml');
    //     Assert::equal($expectedFullPath, $this->storage->getFullPath("test-template"));
    // }

    // /**
    //  * Test remove method.
    //  */
    // public function testRemove(): void
    // {
    //     Assert::true($this->storage->remove("test-template"));
    // }
}

(new StorageTest)->run();
