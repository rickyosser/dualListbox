<?php

declare(strict_types=1);

namespace Atk4\DualListbox\Demos;

use Atk4\DuaListbox\App;
use Atk4\Ui\Layout;
use Atk4\Ui\Columns;
use Atk4\Ui\Lister;
use Atk4\Ui\View;
use Atk4\Ui\Icon;
use Atk4\Ui\Header;

require_once __DIR__ . '/init-app.php';


$app = new myApp($dsn);


$app->initLayout([Layout\Maestro::class]);
$app->requireCss('../public/css/dualListbox.css');


$array1 = [
    ['title' => 'License 1'],
    ['id' => 'active2', 'title' => 'License 2'],
    ['id' => 'active3', 'title' => 'License 3'],
];

$array2 = [
    ['id' => 'inactive1', 'title' => 'License 4'],
    ['id' => 'inactive2', 'title' => 'License 5'],
    ['id' => 'inactive3', 'title' => 'License 6'],
];

$columns = Columns::addTo($app, ['class.highlight' => true]);
$c = $columns->addColumn(5);
Header::addTo($c, ['Active'])->addClass('center aligned');
$seg = View::addTo($c, ["ui" => "raised segment"]);

Lister::addTo($seg, [
    'defaultTemplate' => 'lister.html',
    'ipp' => 2,
])->setSource($array1);

$c = $columns->addColumn(1);
Header::addTo($c, [''])->addClass('center aligned');
Icon::addTo($c, ['arrows alternate horizontal'])->addClass('big')->setAttr(['style' => 'margin-top:300%;']);

$c = $columns->addColumn(5);
Header::addTo($c, ['Inactive'])->addClass('center aligned');
$seg = View::addTo($c, ["ui" => "raised segment"]);

Lister::addTo($seg, [
    'defaultTemplate' => 'lister.html',
    'ipp' => 2
])->setSource($array2);
