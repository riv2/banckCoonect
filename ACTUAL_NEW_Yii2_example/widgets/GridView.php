<?php
namespace app\widgets;

use app\widgets\GridView\assets\GridViewAsset;
use maddoger\widgets\Select2;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\web\JsExpression;
use yii\widgets\ActiveField;

/**
 * Class GridView
 * @package app\widgets
 *
 * @property ActiveDataProvider $dataProvider
 */
class GridView extends \yii\grid\GridView
{
    public $enabledColumns  = null;
    public $buttons         = [];
    public $dataColumnClass = 'app\widgets\GridView\columns\DataColumn';
    public $showSummary     = true;
    public $showPager       = true;
    public $showButtons     = true;
    public $shortPager      = false;

    public function run()
    {
        GridViewAsset::register($this->view);
        $this->layout = $this->layoutTemplate();
        if ($this->enabledColumns !== null) {
            $columns = [];
            foreach ($this->enabledColumns as $column) {
                $columns[] = $this->columns[$column];
            }
            $this->columns = $columns;
        }
        parent::run();
    }

    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        if (parent::renderSection($name) !== false) {
            return parent::renderSection($name);
        }
        switch ($name) {
            case '{buttons}':
                return $this->renderButtons();
            case '{lengthPicker}':
                return $this->renderLengthPicker();
            default:
                return false;
        }
    }

    /**
     * Renders toolbar buttons.
     * @return string the rendering result
     */
    public function renderButtons()
    {
        return implode("\n ", array_map(function ($button) {
            if (!isset($button['label'])) {
                return "";
            }
            $icon = isset($button['icon']) ? '<i class="' . $button['icon'] . '"></i> ' : '';
            return Html::a($icon . $button['label'], $button['url'], $button['options']);
        }, $this->buttons));

    }

    public function initColumns()
    {
        if ($this->filterModel) {
            $attributes = [];
            foreach ($this->columns as $columnName => $column) {
                $attributes[] = isset($column['attribute']) ? $column['attribute'] : $columnName;
            }
            $searchFields = FormBuilder::getFormFields($this->filterModel, $attributes, true, [], true);

            foreach ($this->columns as $columnName => $column) {
                $attribute = isset($column['attribute']) ? $column['attribute'] : $columnName;
                if ($attribute && strpos($attribute, '__') !== 0) {
                    if (isset($searchFields[$attribute]) && $searchFields[$attribute] instanceof ActiveField) {
                        $filter = $searchFields[$attribute]->parts['{input}'];
                        if (is_array($filter)) {
                            $class = $filter['class'];
                            if ($class === Select2::class) {
                                $filter['clientOptions']['width'] = '15em';
                            }
                            $this->columns[$columnName]['filter'] = $class::widget($filter);
                        } else {
                            $this->columns[$columnName]['filter'] = $filter;
                        }
                    } else {

                    }
                }
            }
        }

        foreach ($this->columns as $columnName => $column) {

        }

        parent::initColumns();
    }

    /**
     * Renders the page length picker.
     * @return string the rendering result
     */
    public function renderLengthPicker()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        $pagination->totalCount = $this->dataProvider->getTotalCount();
        $choices = [];
        foreach ([10, 25, 50, 100, 200] as $value) {
            $choices[$pagination->createUrl($pagination->getPage(), $value)] = 'по '.$value;
        }
        return Html::dropDownList('per-page', $pagination->createUrl($pagination->getPage(), $pagination->pageSize), $choices,[
            'onchange'  => new JsExpression("window.location.href = this.value;"),
            'class'     => 'form-control inline-control',
        ]);
    }

    private function layoutTemplate() {
        $head = '';

        if ($this->showButtons) {
            $head .= '<div class="pull-right text-right m-b-5">{buttons}</div>';
        }
        if ($this->showSummary ||  $this->showPager) {
            $head .= '<div>';
            if ($this->showSummary) {
                $head .= '<div class="pull-left from-inline">{summary}</div>';
            }
            if ($this->showPager) {
                $head .= ' {pager} {lengthPicker}';
            }
            $head .= '</div>';
        }

        if ($head) {
            $head = "<div>$head</div>";
        }

        return <<<HTML
$head
<div class="upper-scroll">
    <div class="upper-scroll-dummy">&nbsp;</div>
</div>
<div class="scroll-wrapper">
    {items}
</div>
HTML;
    }

}
