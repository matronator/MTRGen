<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Matronator\Mtrgen\Store\Storage;
use Tester\Assert;
use Tester\TestCase;

class StorageTest extends TestCase
{
    public function testStorage()
    {
        $storage = new Storage;

        Assert::true(file_exists($storage->store), 'Store file exists');
    }
}

(new StorageTest)->run();
