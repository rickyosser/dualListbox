<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model;
use Atk4\Ui\View\ModelTrait;
use Atk4\Ui\HtmlTemplate;

class Listbox extends Input
{
    use ModelTrait;

    public $defaultTemplate = 'form/control/dropdown.html';

    public string $inputType = 'select';

    /** @var int Text area vertical size */
    public $size = 5;
    
    /**
     * Values need for the dropdown.
     * Note: Now possible to display icon with value in dropdown by passing the icon class with your values.
     * ex: 'values' => [
     *     'tag' => ['Tag', 'icon' => 'tag'],
     *     'globe' => ['Globe', 'icon' => 'globe'],
     *     'registered' => ['Registered', 'icon' => 'registered'],
     *     'file' => ['File', 'icon' => 'file'],
     * ].
     *
     * @var array<array-key, mixed>
     */
    public array $values;

    /**
     * Whether or not to accept multiple value.
     *   Multiple values are sent using a string with comma as value delimiter.
     *   ex: 'value1,value2,value3'.
     *
     * @var bool
     */
    public $multiple = false;

    /**
     * Here a custom function for creating the HTML of each dropdown option
     * can be defined. The function gets each row of the model/values property as first parameter.
     * if used with $values property, gets the key of this element as second parameter.
     * Must return an array with at least 'value' and 'title' elements set.
     * Use additional 'icon' element to add an icon to this row.
     *
     * Example 1 with Model: Title in Uppercase
     * function (Model $row) {
     *     return [
     *         'title' => mb_strtoupper($row->getTitle()),
     *     ];
     *  }
     *
     * Example 2 with Model: Add an icon
     * function (Model $row) {
     *     return [
     *         'title' => $row->getTitle(),
     *         'icon' => $row->get('amount') > 1000 ? 'money' : '',
     *     ];
     * }
     *
     * Example 3 with Model: Combine Title from model fields
     * function (Model $row) {
     *     return [
     *         'title' => $row->getTitle() . ' (' . $row->get('title2') . ')',
     *     ];
     * }
     *
     * Example 4 with $values property Array:
     * function (string $value, $key) {
     *     return [
     *        'value' => $key,
     *        'title' => mb_strtoupper($value),
     *        'icon' => str_contains($value, 'Month') ? 'calendar' : '',
     *     ];
     * }
     *
     * @var \Closure<T of Model>(T): array{title: mixed, icon?: mixed}|\Closure(mixed, array-key): array{value: mixed, title: mixed, icon?: mixed}
     */
    public ?\Closure $renderRowFunction = null;

    /** Subtemplate for a single select option. */
    protected HtmlTemplate $_tItem;

    /** Subtemplate for an icon for a single dropdown item. */
    protected HtmlTemplate $_tIcon;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->_tItem = $this->template->cloneRegion('Item');
        $this->template->del('Item');
        //$this->_tIcon = $this->_tItem->cloneRegion('Icon');
        //$this->_tItem->del('Icon');
    }

    #[\Override]
    public function getInputTag(): string
    {
        return $this->getApp()->getTag('select', array_merge([
            'name' => $this->shortName,
            'size' => $this->size,
            'multiple' => $this->multiple,
            'id' => $this->name . '_input',
            'disabled' => $this->disabled,
            //'readonly' => $this->readOnly && !$this->disabled,
        ], $this->inputAttr), $this->getInputValue() ?? '');
    }
    
    protected function htmlRenderValue(): void
    {
        // model set? use this, else values property
        if ($this->model !== null) {
            if ($this->renderRowFunction) {
                foreach ($this->model as $row) {
                    $this->_addCallBackRow($row);
                }
            } else {
                // for standard model rendering, only load ID and title field
                $this->model->setOnlyFields([$this->model->titleField, $this->model->idField]);
                $this->_renderItemsForModel();
            }
        } else {
            if ($this->renderRowFunction) {
                foreach ($this->values as $key => $value) {
                    $this->_addCallBackRow($value, $key);
                }
            } else {
                $this->_renderItemsForValues();
            }
        }
    }
    
    #[\Override]
    protected function renderView(): void
    {
        $this->htmlRenderValue();
        $this->getInputTag();

        parent::renderView();
    }
    
    /**
     * Sets the dropdown items from $this->values array.
     */
    protected function _renderItemsForValues(): void
    {
        foreach ($this->values as $key => $val) {
            $this->_tItem->set('value', (string) $key);
            if (is_array($val)) {
                if (array_key_exists('icon', $val)) {
                    $this->_tIcon->set('iconClass', $val['icon'] . ' icon');
                    $this->_tItem->dangerouslySetHtml('Icon', $this->_tIcon->renderToHtml());
                } else {
                    $this->_tItem->del('Icon');
                }
                //print_r($val);
                $this->_tItem->set('title', $val['title'] || is_numeric($val['title']) ? (string) $val['title'] : '');
            } else {
                $this->_tItem->set('title', $val || is_numeric($val) ? (string) $val : '');
            }

            // add item to template
            $this->template->dangerouslyAppendHtml('Item', $this->_tItem->renderToHtml());
        }
    }
}
