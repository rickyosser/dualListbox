<?php

declare(strict_types=1);

namespace Atk4\DualListbox\Demos;

use Atk4\Ui\App;

$isRootProject = file_exists(__DIR__ . '/../vendor/autoload.php');
/** @var ClassLoader $loader */
if($isRootProject) {
    $loader = require dirname(__DIR__, $isRootProject ? 1 : 4) . '/vendor/autoload.php';
}
if (!$isRootProject && !class_exists(ViewTest::class)) {
    throw new \Error('Demos can be run only if atk4/DualListbox is a root composer project or if dev files are autoloaded');
}


class myApp extends App {
    public $title = 'Dual Listbox - Demo';
    function __construct() {
        parent::__construct();

        return;
    }
}
