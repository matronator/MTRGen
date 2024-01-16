<?php

declare(strict_types=1);

namespace Matronator\Mtrgen\Tests;

require __DIR__ . '/../vendor/autoload.php';

\Tester\Environment::setup();

date_default_timezone_set('Europe/Prague');
const TMP_DIR = '/tmp/app-tests';
