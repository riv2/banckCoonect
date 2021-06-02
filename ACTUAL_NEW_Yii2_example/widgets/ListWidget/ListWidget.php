<?php
namespace app\widgets\ListWidget;

use yii;
use yii\bootstrap\Widget;

class ListWidget extends Widget
{
    /** @var string  */
    public $inputName = null;

    /** @var string  */
    public $attribute = 'name';

    /** @var string  */
    public $modelClass = null;

    /** @var array  */
    public $modelFields = null;

    /** @var array  */
    public $items = [];

    /** @var string  */
    public $title = null;

    /** @var string  */
    public $validateRegexp = null;

    /** @var bool  */
    public $relation = false;

    /** @var callable  */
    public $addOn = null;

    /** @var mixed  */
    public $addOnParam = null;

    /** @var mixed  */
    public $sortable = false;
    
    public function run()
    {
        if (!$this->modelClass) {
            throw new yii\base\InvalidParamException("modelClass no set");
        }

        $model = null;

        if ($this->modelClass) {
            $modelClass = $this->modelClass;
            if (!$this->inputName) {
                $this->inputName = (new \ReflectionClass($this->modelClass))->getShortName();
            }
            $model = new $modelClass;
        }

        if (!$this->inputName) {
            $this->inputName = 'List';
        }

        if ($this->modelFields === null) {
            $this->modelFields = ['name'];
        }

        echo $this->render('list', [
            'model'                     => $model,
            'widget'                    => $this,
            'widgetId'                  => $this->id,
        ]);
    }

}