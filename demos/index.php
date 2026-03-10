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
use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsCallbackLoadableValue;

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

$form = Form::addTo($app);

$group = $form->addGroup(['width' => 'three']);
$group->addControl(
    'Box1',
    [
        Listbox::class,
        'caption' => 'Hej',
        'size' => 5,
        'multiple' => true,
        'width' => 'two',
    ]
)->setSource($array1);

$seg = View::addTo($group, ["ui" => "raised segment"])->setAttr('style', 'top: 12px;');

$leftButton = Button::addTo($seg, ['', 'icon' => 'left arrow', 'class.left attached' => true])->setAttr('style', 'top: 21px;');
$rightButton = Button::addTo($seg, ['', 'iconRight' => 'right arrow', 'class.right attached' => true])->setAttr('style', 'top: 21px;');

$group->addControl(
    'Box2',
    [
        Listbox::class,
        'caption' => 'Då',
        'size' => 5,
        'multiple' => true,
        'width' => 'two',
    ])->setSource($array2);

$form->on('click', static function(Jquery $j) {
    return new JsExpression('alert([])', [atk4_print_r($j->get())]);
}, ['item' => new JsCallbackLoadableValue(null, function ($v) {
    return $this->getApp()->uiPersistence->typecastAttributeLoadField(
        $this->model->getIdField(),
        $v
    );
})]);

$leftButton->on('mouseup', static function($button) {
    return new JsExpression('alert([])', ['Hej']);
});


$form->onSubmit(function($form) {
    print_r($form->entity->get());
    return;
});
