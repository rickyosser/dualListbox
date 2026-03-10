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
use Atk4\Ui\Form;
use Atk4\Ui\Form\Control\Listbox;

require_once __DIR__ . '/init-app.php';


$app = new myApp();


$app->initLayout([Layout\Maestro::class]);
$app->requireCss('../public/css/dualListbox.css');


$array1 = [
    ['id' => 'active1', 'title' => 'License 1', 'selected' => false],
    ['id' => 'active2', 'title' => 'License 2', 'selected' => true],
    ['id' => 'active3', 'title' => 'License 3', 'selected' => true],
];

$array2 = [
    ['id' => 'inactive1', 'title' => 'License 4'],
    ['id' => 'inactive2', 'title' => 'License 5'],
    ['id' => 'inactive3', 'title' => 'License 6'],
];

$array3 = [];
/*
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
Icon::addTo($c, ['arrows alternate horizontal'])->addClass('big')->setAttr(['style' => 'margin-top:150%;']);

$c = $columns->addColumn(5);
Header::addTo($c, ['Inactive'])->addClass('center aligned');
$seg = View::addTo($c, ["ui" => "raised segment"]);

Lister::addTo($seg, [
    'defaultTemplate' => 'lister.html',
    'ipp' => 2
])->setSource($array2);

*/
//print_r($array1);

$form = Form::addTo($app);

$box = $form->addControl(
    'Box2',
    [
        Listbox::class,
        'caption' => 'Tjena',
        //'label' => 'Box2',
        'size' => 4,
        'multiple' => true,
        //'values' => $array1
        'width' => 'two',
    ]
)->setSource($array1);

$form->onSubmit(function($form) {
    //atk4_print_r($form->entity->get());
    return;
});
