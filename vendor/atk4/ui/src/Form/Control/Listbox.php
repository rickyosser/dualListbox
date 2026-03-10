<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\View;
use Atk4\Ui\View\ModelTrait;
use Atk4\Ui\Js\Jquery;

class Listbox extends Input
{
    use HookTrait;
    use ModelTrait;

    public const HOOK_BEFORE_ROW = self::class . '@beforeRow';
    public const HOOK_AFTER_ROW = self::class . '@afterRow';

    public $defaultTemplate = 'form/control/listbox.html';

    public string $inputType = 'hidden';

    /** @var string Label of Listbox */
    public $label = '';

    /** @var string Label alignment */
    public $label_align = 'center';

    /** @var string Label weight */
    public $label_weight = 'bold';

    /** @var int Number of rows in Listbox */
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
     * Listbox repeats part of it's template. This property will contain
     * the repeating part. Clones from {row}. If your template does not
     * have {row} tag, then entire template will be repeated.
     *
     * @var HtmlTemplate
     */
    public $tRow;

    /** @var HtmlTemplate|null Lister use this part of template in case there are no elements in it. */
    public $tEmpty;

    /** Current row entity */
    public ?Model $currentRow = null;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        //atk4_print_r($this->template);
        // empty row template
        if ($this->template->hasTag('empty')) {
            $this->tEmpty = $this->template->cloneRegion('empty');
            $this->template->del('empty');
        }

        // data row template
        if ($this->template->hasTag('row')) {
            $this->tRow = $this->template->cloneRegion('row');
            $this->template->del('rows');
        } else {
            $this->tRow = clone $this->template;
            $this->template->del('_top');
        }
    }

    #[\Override]
    public function getInputValue(): ?string
    {
        // dropdown input tag accepts CSV formatted list of IDs
        return $this->entityField !== null
            ? ($this->multiple && $this->entityField->getField()->type === 'json' && is_array($this->entityField->get())
                ? implode(',', $this->entityField->get())
                : $this->getApp()->uiPersistence->typecastAttributeSaveField($this->entityField->getField(), $this->entityField->get()))
            : parent::getInputValue();
    }

    #[\Override]
    public function setInputValue(string $value): void
    {
        if ($this->entityField !== null && $this->multiple && $this->entityField->getField()->type === 'json') {
            $value = $this->getApp()->encodeJson(explode(',', $value));
        }

        parent::setInputValue($value);
    }

    /** @var int This will count how many rows are rendered. Needed for JsPaginator for example. */
    protected $_renderedRowsCount = 0;

    #[\Override]
    protected function renderView(): void
    {
        if (!$this->template) {
            throw new Exception('Listbox requires you to specify template explicitly');
        }

        // if no model is set, don't show anything
        if ($this->model === null) {
            parent::renderView();

            return;
        }

        // Setup the Listbox
        if($this->template->hasTag('name')) {
            $this->template->set('name', $this->name);
        }

        if($this->template->hasTag('label')) {
            $this->template->set('label', $this->label);
        }

        if($this->template->hasTag('label-weight')) {
            $this->template->set('label-weight', $this->label_weight);
        }

        if($this->template->hasTag('label-align')) {
            $this->template->set('label-align', $this->label_align);
        }
        if($this->template->hasTag('size') && $this->size) {
            $this->template->set('size', (string) $this->size);
        }

        if($this->template->hasTag('multiple') && $this->multiple) {
            $this->template->set('multiple', 'multiple');
        }

        // iterate data rows
        $this->_renderedRowsCount = 0;
        $this->values = [];
        $tRowBackup = $this->tRow;
        try {
            foreach ($this->model as $entity) {
                $this->currentRow = $entity;

                array_push($this->values, $entity->get('id'));
                $this->tRow = clone $tRowBackup;

                if ($this->hook(self::HOOK_BEFORE_ROW) === false) {
                    continue;
                }

                $this->renderRow();
                ++$this->_renderedRowsCount;
            }
        } finally {
            $this->tRow = $tRowBackup;
            $this->currentRow = null;
        }
        $this->setInputValue(implode(',', $this->values));
        
        // empty message
        if ($this->_renderedRowsCount === 0) {
            $empty = $this->tEmpty !== null ? $this->tEmpty->renderToHtml() : '';
            if ($this->template->hasTag('rows')) {
                $this->template->dangerouslyAppendHtml('rows', $empty);
            } else {
                $this->template->dangerouslyAppendHtml('_top', $empty);
            }
        }

        parent::renderView();
    }
    protected function renderTRow(): void
    {
        $this->tRow->trySet($this->getApp()->uiPersistence->typecastSaveRow($this->currentRow, $this->currentRow->get()));

        if ($this->tRow->hasTag('title')) {
            $this->tRow->set('title', $this->currentRow->getTitle());
        }

        if(array_key_exists('selected', $this->currentRow->get())) {
            $this->tRow->set('selected', 'selected');
        }

        $idStr = $this->getApp()->uiPersistence->typecastAttributeSaveField($this->currentRow->getIdField(), $this->currentRow->getId());
        if ($this->tRow->hasTag('value')) {
            $this->tRow->set('value', $idStr);
        }
        $this->tRow->trySet('row_id', $this->name . '-' . $idStr);
    }

    /**
     * Render individual row. Override this method if you want to do more
     * decoration.
     */
    public function renderRow(): void
    {
        $this->renderTRow();

        $html = $this->tRow->renderToHtml();
        if ($this->template->hasTag('rows')) {
            $this->template->dangerouslyAppendHtml('rows', $html);
        } else {
            $this->template->dangerouslyAppendHtml('_top', $html);
        }
    }
}
