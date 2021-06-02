<?php
namespace app\widgets;

use app\components\base\BaseModel;
use app\components\StubForm;
use kartik\daterange\DateRangePicker;
use app\components\crud\actions\Action;
use kartik\form\ActiveForm;
use netis\crud\db\ActiveQuery;
use netis\crud\web\Formatter;
use yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveField;


class FormBuilder extends \netis\crud\widgets\FormBuilder
{
    const MODAL_MODE_NEW_RECORD = 1;
    const MODAL_MODE_EXISTING_RECORD = 2;

    /**
     * @param $view
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param bool $multiple true for multiple values inputs, usually used for search forms
     * @param string $customName
     * @return ActiveField
     */
    public static function relation($view, $model, $relation , $multiple = false , $customName = null)
    {


        $activeRelation = $model->getRelation(Html::getAttributeName($relation));

        $widgetOptions  = static::getRelationWidgetOptions($model, $relation, $activeRelation, $multiple);

        static::registerRelations($view);
        static::registerSelect($view);

        /** @var yii\bootstrap\ActiveForm $form */
        /** @var yii\base\Widget $widget */
        if ($customName !== null) {
            unset($widgetOptions['model']);
            unset($widgetOptions['attribute']);
            $widgetOptions['name'] = $customName;
        }
        $widget =  Yii::createObject($widgetOptions);
        return $widget->run();
    }

    /**
     * Registers JS code to help initialize Select2 widgets
     * with access to netis\crud\crud\ActiveController API.
     * @param \yii\web\View $view
     */
    public static function registerSelect($view)
    {

        $script = <<<JavaScript
(function (s2helper, $, undefined) {
    "use strict";
    s2helper.formatResult = function (result, container, query, escapeMarkup, depth) {
        if (typeof depth == 'undefined') {
            depth = 0;
        }
        var markup = [];
        window.Select2.util.markMatch(result._label, query.term, markup, escapeMarkup);
        return markup.join("");
    };

    s2helper.formatSelection = function (item) {
        return item._label;
    };

    // generates query params
    s2helper.data = function (fieldName) {
        return function(term, page) { 
            if (typeof select2AdditionalParams != "undefined") {
                return select2AdditionalParams(fieldName, { search: term, page: page });
            }
            return { search: term, page: page }; 
        };
    };

    // builds query results from ajax response
    s2helper.results = function (data, page) {
        return { results: data.items, more: page < data._meta.pageCount };
    };

    s2helper.getParams = function (element) {
        var primaryKey = element.data('relation-pk');
        if (typeof primaryKey === 'undefined' || primaryKey === null) {
            primaryKey = 'id';
        }
        var params = {search: {}};
        params.search[primaryKey] = element.val();
        return params;
    };

    s2helper.initSingle = function (element, callback) {
        $.getJSON(element.data('select2').opts.ajax.url, s2helper.getParams(element), function (data) {
            if (typeof data.items[0] != 'undefined') {
                callback(data.items[0]);
            }
        });
    };

    s2helper.initMulti = function (element, callback) {
        $.ajax({
            'url': element.data('select2').opts.ajax.url,
            'data': s2helper.getParams(element),
            'type': 'post',
            'dataType': 'json',
            'success': function (data) {callback(data.items)}
        });
    };
}( window.s2helper = window.s2helper || {}, jQuery ));
JavaScript;
        $view->registerJs($script, \yii\web\View::POS_END, 'netis.s2helper');
        \maddoger\widgets\Select2BootstrapAsset::register($view);
    }

    /**
     * Registers JS code for handling relations.
     * @param \yii\web\View $view
     * @return string modal widget to be embedded in a view
     */
    public static function registerRelations($view)
    {

        \netis\crud\assets\RelationsAsset::register($view);
        $options = \yii\helpers\Json::htmlEncode([
            'i18n'                  => [
                'loadingText' => Yii::t('app', 'Loading, please wait.'),
            ],
            'keysSeparator'         => \netis\crud\crud\Action::KEYS_SEPARATOR,
            'compositeKeySeparator' => \netis\crud\crud\Action::COMPOSITE_KEY_SEPARATOR,
        ]);
        $view->registerJs("netis.init($options)", \yii\web\View::POS_READY, 'netis.init');

        // init relation tools used in _relations subview
        // relations modal may contain a form and must be rendered outside ActiveForm
        return \yii\bootstrap\Modal::widget([
            'id'     => 'relationModal',
            'size'   => \yii\bootstrap\Modal::SIZE_LARGE,
            'header' => '<span class="modal-title"></span>',
            'footer' => implode('', [
                Html::button(Yii::t('app', 'Save'), [
                    'id'    => 'relationSave',
                    'class' => 'btn btn-primary',
                ]),
                Html::button(Yii::t('app', 'Cancel'), [
                    'class'        => 'btn btn-default',
                    'data-dismiss' => 'modal',
                    'aria-hidden'  => 'true',
                ]),
            ]),
        ]);
    }

    public static function getModelSearchRoute($modelClassName) {
        if (($route = Yii::$app->crudModelsMap[$modelClassName]) === null) {
            return null;
        }
        $indexRoute = [
            $route . '/index',
        ];
        return $indexRoute;
    }
    /**
     * @param \yii\db\ActiveRecord $model
     * @param \yii\db\ActiveRecord $relatedModel
     * @param \yii\db\ActiveQuery $relation
     * @return array array with three arrays: create, search and index routes
     */
    public static function getRelationRoutes($model, $relatedModel, $relation)
    {
        if (($route = Yii::$app->crudModelsMap[$relatedModel::className()]) === null) {
            return [null, null, null];
        }

        $allowCreate = Yii::$app->user->can($relatedModel::className().'.create');
        if ($allowCreate && $model->isNewRecord && $relation->multiple) {
            foreach ($relation->link as $left => $right) {
                if (!$relatedModel->getTableSchema()->getColumn($left)->allowNull) {
                    $allowCreate = false;
                    break;
                }
            }
        }

        if (!$allowCreate) {
            $createRoute = null;
        } else {
            $createRoute = [$route . '/update'];
            if ($relation->multiple) {
                $createRoute['hide'] = implode(',', array_keys($relation->link));
                $scope      = $relatedModel->formName();
                $primaryKey = $model->getPrimaryKey(true);
                foreach ($relation->link as $left => $right) {
                    if (!isset($primaryKey[$right])) {
                        continue;
                    }
                    $createRoute[$scope][$left] = $primaryKey[$right];
                }
            }
        }

        $parts = explode('\\', $relatedModel::className());
        $relatedModelClass = array_pop($parts);
        $relatedSearchModelClass = implode('\\', $parts) . '\\search\\' . $relatedModelClass;
        $searchRoute = !class_exists($relatedSearchModelClass) ? null : [
            $route . '/relation',
            'per-page' => 10,
            'relation' => $relation->inverseOf,
            'id'       => Action::exportKey($model->getPrimaryKey()),
            'multiple' => $relation->multiple ? 'true' : 'false',
        ];

        $indexRoute = [
            $route . '/index',
        ];

        return [$createRoute, $searchRoute, $indexRoute];
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param \yii\db\ActiveQuery $activeRelation
     * @return string
     * @throws Exception
     */
    protected static function getRelationValue($model, $relation, $activeRelation)
    {
        $foreignKeys = array_values($activeRelation->link);

        $relation = Html::getAttributeName($relation);

        if ($activeRelation->multiple) {
            if ( property_exists($model, $relation)) {
                // special case for search models, where there is a relation property defined that holds the keys
                $value = $model->$relation;
            } else {
                /** @var \yii\db\ActiveRecord $modelClass */
                $modelClass = $activeRelation->modelClass;
                if ($model->id = "") {
                    $model->id = null;
                }
                $value = array_map(
                    '\netis\crud\crud\Action::exportKey',
                    $activeRelation->select($modelClass::primaryKey())->asArray()->all()
                );
            }
            if (is_array($value)) {
                $value = Action::implodeEscaped(Action::KEYS_SEPARATOR, $value);
            }
            return $value;
        }
        // special case for search models, where fks holds array of keys
        $foreignKey = reset($foreignKeys);
        if (!is_array($model->getAttribute($foreignKey))) {
            return Action::exportKey($model->getAttributes($foreignKeys));
        }
        if (count($foreignKeys) > 1) {
            throw new Exception('Composite foreign keys are not supported for searching.');
        }
        return Action::implodeEscaped(Action::KEYS_SEPARATOR, $model->getAttribute($foreignKey));
    }

    /**
     * Get drop down list items using provided Query.
     *
     * __WARNING__: This method does not append authorized conditions to query and you need append those if needed.
     *
     * @param \yii\db\ActiveQuery $query
     *
     * @return array
     */
    public static function getDropDownItems($query)
    {
        if ($query instanceof ActiveQuery) {
            $query->defaultOrder();
        }

        /** @var \yii\db\ActiveRecord|\netis\rbac\AuthorizerBehavior $model */
        $model = new $query->modelClass;

        $fields = $model::primaryKey();
        if (($labelAttributes = $model->getBehavior('labels')->attributes) !== null) {
            $fields = array_merge($model::primaryKey(), $labelAttributes);
        }

        $flippedPrimaryKey = array_flip($model::primaryKey());
        return ArrayHelper::map(
            $query->from($model::tableName() . ' t')->all(),
            function ($item) use ($fields, $flippedPrimaryKey) {
                /** @var \netis\crud\db\ActiveRecord $item */
                return Action::exportKey(array_intersect_key($item->toArray($fields, []), $flippedPrimaryKey));
            },
            function ($item) use ($fields) {
                /** @var \netis\crud\db\ActiveRecord $item */
                $data = $item->toArray($fields, []);
                return $data['_label'];
            }
        );
    }

    /**
     * @param string $searchRoute
     * @param string $createRoute
     * @param string $jsPrimaryKey
     * @param string $label
     * @param string $relation
     * @return array holds ajaxResults JS callback and clientEvents array
     */
    protected static function getRelationAjaxOptions($searchRoute, $createRoute, $jsPrimaryKey, $label, $relation)
    {
        $searchLabel = Yii::t('app', 'Advanced search');
        $createLabel = Yii::t('app', 'Create new');
        $searchUrl = $searchRoute === null ? null : Url::toRoute($searchRoute);
        $createUrl = $createRoute === null ? null : Url::toRoute($createRoute);
        $createKey = 'create_item';
        $searchKey = 'search_item';
        $script = <<<JavaScript
function (data, page) {
    if (page !== 1) {
        //append search and create items on first page only
        return s2helper.results(data, page);
    }

    var keys = $jsPrimaryKey, values = {};
    if ('$searchUrl') {
        for (var i = 0; i < keys.length; i++) {
            values[keys[i]] = '$searchKey';
        }
        values._label = '-- $searchLabel --';
        data.items.unshift(values);
    }
    if ('$createUrl') {
        values = [];
        for (var i = 0; i < keys.length; i++) {
            values[keys[i]] = '$createKey';
        }
        values._label = '-- $createLabel --';
        data.items.unshift(values);
    }
    return s2helper.results(data, page);
}
JavaScript;
        $ajaxResults = new JsExpression($script);
        $script = <<<JavaScript
function (event) {
    var isSearch = true, isCreate = true;
    if (event.val != '$searchKey') {
        isSearch = false;
    }

    if (event.val != '$createKey') {
        isCreate = false;
    }

    if (!isSearch && !isCreate) {
        return true;
    }

    $(event.target).select2('close');
    $('#relationModal').data('target', $(event.target).attr('id'));
    $('#relationModal').data('title', '$label');
    $('#relationModal').data('relation', '$relation');
    $('#relationModal').data('pjax-url', isSearch ? '$searchUrl' : '$createUrl');
    $('#relationModal').modal('show');
    event.preventDefault();
    return false;
}
JavaScript;
        $clientEvents = [
            'select2-selecting' => new JsExpression($script),
        ];

        return [$ajaxResults, $clientEvents];
    }

    /**
     * Returns {@link \maddoger\widgets\Select2} widget options without ajax configuration.     *
     *
     * @param \yii\db\ActiveRecord $model
     * @param string               $relation
     * @param \yii\db\ActiveQuery  $activeRelation
     * @param bool|false           $multiple
     * @param null|array           $items
     *
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function getRelationWidgetStaticOptions($model, $relation, $activeRelation, $multiple = false, $items = null)
    {
        $isMany = $activeRelation->multiple;
        $foreignKey = array_values($activeRelation->link)[0];

        /** @var BaseModel $relModel */
        $relModel = new $activeRelation->modelClass;

        if ($items === null) {
            $checkedRelations = $relModel->getCheckedRelations(Yii::$app->user->id, $activeRelation->modelClass . '.read');
            $query = $relModel::find()->authorized($relModel, $checkedRelations, Yii::$app->user->getIdentity());
            $items = static::getDropDownItems($query);
        }

        //clone model so we could set value for attribute so we would have initialized value for static select2
        $model = clone $model;
        $attribute = $isMany ? $relation : $foreignKey;
        if (!$isMany) {
            $model->$attribute = count($items) <= 1 ? key($items)
                : static::getRelationValue($model, $relation, $activeRelation);
        }
        $dbColumn = $model->getTableSchema()->getColumn($foreignKey);
        $allowClear = $multiple || $isMany ? true : !$model->isAttributeRequired($foreignKey)
            && ($dbColumn === null || $dbColumn->allowNull);

        if (!$allowClear && empty($items)) {
            throw new InvalidConfigException("$foreignKey attribute in {$model::className()} is required but there are no available items");
        }

        if (!$allowClear && empty($items)) {
            Yii::warning("There are no items in control for $foreignKey attribute in {$model::className()}");
        }

        //we get prefix from $relation because it could be in format [3]relation and we need to have [3]foreign_key here
        $relationName = Html::getAttributeName($relation);
        $prefixedFk = str_replace($relationName, $foreignKey, $relation);
        return [
            'class' => \maddoger\widgets\Select2::className(),
            'model' => $model,
            'attribute' => $isMany ? $relation : $prefixedFk,
            'items' => $items,
            'clientOptions' => [
                'width' => '100%',
                'allowClear' => $allowClear,
                'closeOnSelect' => true,
                'minimumInputLength'    => ($relModel::isBigData()) ? 2 : 0,
            ],
            'options' => array_merge([
                'class' => 'select2',
                'prompt' => '',
                'placeholder' => static::getPrompt(),
            ], $multiple ? ['multiple' => 'multiple'] : []),
        ];
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param \yii\db\ActiveQuery $activeRelation
     * @param bool|false $multiple
     *
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public static function getRelationWidgetOptions($model, $relation, $activeRelation, $multiple = false)
    {
        /** @var BaseModel $relModel */
        $relModel = new $activeRelation->modelClass;
        $allowClear= true;
        $isMany = $activeRelation->multiple;
        $foreignKeys = array_values($activeRelation->link);
        $foreignKey = reset($foreignKeys);
        $dbColumns = $model->getTableSchema()->columns;
        $primaryKey = $relModel::primaryKey();
        $jsPrimaryKey = json_encode($primaryKey);
        //we get prefix from $relation because it could be in format [3]relation and we need to have [3]foreign_key here
        $relationName = Html::getAttributeName($relation);
        $prefixedFk = str_replace($relation, $foreignKey, $relationName);

        $fieldName = strtolower($model->formName()).'_'.$foreignKey;

        list($createRoute, $searchRoute, $indexRoute) = FormBuilder::getRelationRoutes(
            $model,
            $relModel,
            $activeRelation
        );


        if ($indexRoute === null) {
            return static::getRelationWidgetStaticOptions($model, $relation, $activeRelation, $multiple);
        }


        if (($labelAttributes = $relModel->getBehavior('labels')->attributes) !== null) {
            $fields = array_merge($primaryKey, $labelAttributes);
        } else {
            $fields = $primaryKey;
        }

        $value =  static::getRelationValue($model, $relation, $activeRelation);
        $allowClear = $multiple || $isMany ? true : !$model->isAttributeRequired($foreignKey)
            && (!isset($dbColumns[$foreignKey]) || $dbColumns[$foreignKey]->allowNull);


        $jsSeparator = \netis\crud\crud\Action::COMPOSITE_KEY_SEPARATOR;
        $jsId = <<<JavaScript
function(object){
    var keys = $jsPrimaryKey, values = [];
    for (var i = 0; i < keys.length; i++) {
         values.push(object[keys[i]]);
    }
    return netis.implodeEscaped('$jsSeparator', values);
}
JavaScript;

        // check if only one option is available and if yes - set it as selected value
        if (!$allowClear && trim($value) === '') {
            $checkedRelations = $relModel->getCheckedRelations(Yii::$app->user->id, $activeRelation->modelClass . '.read');
            $relQuery = $relModel::find()
                ->select($primaryKey)
                ->from($relModel::tableName() . ' t')
                ->authorized($relModel, $checkedRelations, Yii::$app->user->getIdentity())
                ->asArray();
            if ($relQuery->count() === 1) {
                $value = $relQuery->one();
                $value = Action::implodeEscaped(Action::KEYS_SEPARATOR, $value);
            }
        }

        $label = null;
        if ($model instanceof \netis\crud\db\ActiveRecord) {
            $label = $model->getAttributeLabel($relation) ? : $model->getRelationLabel($activeRelation, Html::getAttributeName($relation));
        }
        $ajaxResults = new JsExpression('s2helper.results');
        $clientEvents = null;
        if ($indexRoute !== null && ($searchRoute !== null || $createRoute !== null)) {
            list ($ajaxResults, $clientEvents) = static::getRelationAjaxOptions(
                $searchRoute,
                $createRoute,
                $jsPrimaryKey,
                $label,
                $relation
            );
        }


        return [
            'class'         => 'maddoger\widgets\Select2',
            'model'         => $model,
            'attribute'     => $isMany ? $relation : $prefixedFk,
            'clientOptions' => array_merge(
                [
                    'formatResult'          => new JsExpression('s2helper.formatResult'),
                    'formatSelection'       => new JsExpression('s2helper.formatSelection'),
                    'id'                    => new JsExpression($jsId),
                    'width'                 => '100%',
                    'allowClear'            => $allowClear,
                    'closeOnSelect'         => true,
                    'initSelection'         => new JsExpression($multiple ? 's2helper.initMulti' : 's2helper.initSingle'),
                    'minimumInputLength'    => ($relModel::isBigData()) ? 2 : 0,
                    'ajax' => [
                        'url' => new JsExpression('"'.Url::toRoute(array_merge($indexRoute ?: ['#none'], [
                                '_format' => 'json',
                                'fields' => $fields ? implode(',', $fields) : null,
                            ])).'"'),
                        'dataFormat'    => 'json',
                        'quietMillis'   => 300,
                        'data'          => new JsExpression('s2helper.data("'.$fieldName.'")'),
                        'results'       => $ajaxResults,
                    ],
                ],
                $multiple ? ['multiple' => true] : []
            ),
            'clientEvents' => $clientEvents,
            'options' => [
                'class'     => 'select2',
                'value'     => $value,
                'placeholder' => static::getPrompt(),
                //for now handle relations with single column primary keys
                'data-relation-pk' => count($primaryKey) === 1 ? reset($primaryKey) : null,
            ],
        ];
    }

    public static function renderSelect2($view, $modelClassName, $name, $value, $multiple = false, $minInput = 2, $placeholder = null) {

        if (is_array($value)) {
            $value = join(',', $value);
        }

        if ($view) {
            static::registerSelect($view);
        }

        $jsSeparator = \netis\crud\crud\Action::COMPOSITE_KEY_SEPARATOR;
        $jsId = <<<JavaScript
function(object){
    var keys = ["id"], values = [];
    for (var i = 0; i < keys.length; i++) {
         values.push(object[keys[i]]);
    }
    return values.join('$jsSeparator');
}
JavaScript;

        return \maddoger\widgets\Select2::widget([
            'name'          => $name,
            'value'         => $value,
            'clientOptions' => array_merge(
                [
                    'formatResult'      => new JsExpression('s2helper.formatResult'),
                    'formatSelection'   => new JsExpression('s2helper.formatSelection'),
                    'id'                => new JsExpression($jsId),
                    'width'             => '100%',
                    'allowClear'        => true,
                    'closeOnSelect'     => true,
                    'initSelection'     => new JsExpression($multiple ? 's2helper.initMulti' : 's2helper.initSingle'),
                    'minimumInputLength' => $minInput,
                    'ajax' => [
                        'url' => new JsExpression('"'.Url::toRoute(array_merge(self::getModelSearchRoute($modelClassName), [
                                '_format' => 'json',
                                'fields' => implode(',', ['id','name']),
                            ])).'"'),
                        'dataFormat'    => 'json',
                        'quietMillis'   => 300,
                        'delay'         => 300,
                        'data'          => new JsExpression('s2helper.data("'.$name.'")'),
                        'results'       => new JsExpression('s2helper.results'),
                        'cache'         => true,
                    ],
                ],
                $multiple ? ['multiple' => true] : []
            ),
            'clientEvents' => null,
            'options' => [
                'id'            => str_ireplace(['[',']','.'], '-' , $name),
                'class'         => 'select2',
                'value'         => $value,
                'placeholder'   => $placeholder ? $placeholder : static::getPrompt(),
                'data-relation-pk' => 'id',
            ],
        ]);
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param \yii\db\ActiveQuery $activeRelation
     * @param array $widgetOptions obtained from getRelationWidgetOptions()
     * @return ActiveField
     */
    public static function getRelationWidget($model, $relation, $activeRelation, $widgetOptions)
    {
        $label = null;

        if ($model instanceof \netis\crud\db\ActiveRecord) {
            $label = $model->getAttributeLabel($relation) ?: $model->getRelationLabel($activeRelation, Html::getAttributeName($relation));
        }

        $isMany         = $activeRelation->multiple;
        $foreignKeys    = array_values($activeRelation->link);
        $foreignKey     = reset($foreignKeys);

        $stubForm = new \stdClass();

        /** @var \yii\bootstrap\ActiveField $field */

        $field = new yii\widgets\ActiveField([
            'model'     => $model,
            'attribute' => $isMany ? $relation : $foreignKey,
            'parts' => [
                '{input}' => $widgetOptions,
            ],
        ]);
        return $field->label($label);
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param \yii\db\ActiveQuery $activeRelation
     * @param bool $multiple true for multiple values inputs, usually used for search forms
     * @return ActiveField
     */
    protected static function getHasOneRelationField($model, $relation, $activeRelation, $multiple = false)
    {
        $modelClass = $activeRelation->modelClass;
        if (method_exists($modelClass, 'crudAsRelationFilter')) {
            $widgetOptions = $modelClass::crudAsRelationFilter($model, $relation, $activeRelation, $multiple);
        } else {
            $widgetOptions = FormBuilder::getRelationWidgetOptions($model, $relation, $activeRelation, $multiple);
        }
        return FormBuilder::getRelationWidget($model, $relation, $activeRelation, $widgetOptions);
    }

    /**
     * To enable this, override and return getRelationWidget().
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param \yii\db\ActiveQuery $activeRelation
     * @return ActiveField
     */
    protected static function getHasManyRelationField($model, $relation, $activeRelation)
    {
        $modelClass = $activeRelation->modelClass;
        if (method_exists($modelClass, 'crudAsRelationFilter')) {
            $widgetOptions = $modelClass::crudAsRelationFilter($model, $relation, $activeRelation, true);
        } else {
            $widgetOptions = FormBuilder::getRelationWidgetOptions($model, $relation, $activeRelation, true);
        }
        if (!$widgetOptions) {
            return null;
        }
        return FormBuilder::getRelationWidget($model, $relation, $activeRelation, $widgetOptions);
    }

    /**
     * @param array $formFields
     * @param \yii\db\ActiveRecord $model
     * @param string $relation
     * @param array $hiddenAttributes
     * @param array $safeAttributes
     * @param bool $multiple true for multiple values inputs, usually used for search forms
     * @param $bothRelationsAndIds
     * @param $stubForm
     * @return array
     * @throws InvalidConfigException
     */
    protected static function addRelationField($formFields, $model, $relation, $hiddenAttributes, $safeAttributes, $multiple = false, $bothRelationsAndIds = false, $stubForm = null)
    {
        $activeRelation = $model->getRelation(Html::getAttributeName($relation));

        if (!$activeRelation->multiple) {

            // validate foreign keys only for hasOne relations
            $isHidden = false;
            foreach ($activeRelation->link as $left => $right) {

                if (!in_array($right, $safeAttributes)) {
                    return $formFields;
                }
                if (isset($hiddenAttributes[$right])) {
                    $formFields[$relation] = Html::activeHiddenInput($model, $right);
                    unset($hiddenAttributes[$right]);
                    $isHidden = true;
                }
            }
            if ($isHidden) {
                return $formFields;
            }
        }

        if (!Yii::$app->user->can($activeRelation->modelClass.'.read')) {
            return $formFields;
        }

        if (count($activeRelation->link) > 1) {
            return $formFields;
            //throw new InvalidConfigException('Composite key relations are not supported by '.get_called_class());
        }

        $keys = array_map(function ($foreignKey) {
            array_shift($foreignKey);
            $arr = array_keys($foreignKey);
            return array_shift($arr);
        }, $model->getTableSchema()->foreignKeys);


        if ($activeRelation->multiple) {
            if (($field = static::getHasManyRelationField($model, $relation, $activeRelation)) !== null) {
                if ($model->scenario != BaseModel::SCENARIO_SEARCH) {
                    $formFields[$relation] = $field;
                }
                // TODO: Множественные связи оключены в поиске
            }
        } else {
            if (($field = static::getHasOneRelationField($model, $relation, $activeRelation, $multiple)) !== null) {
                if (count($activeRelation->link) == 1) {
                    foreach ($activeRelation->link as $left => $right) {
                        if (in_array($right, $keys) && !$bothRelationsAndIds) {
                            $formFields[$right] = $field;
                        } else {
                            if (isset($formFields[$right]) && !$bothRelationsAndIds) {
                                $formFields[$right] = $field;
                            } else {
                                //if ($model->scenario != BaseModel::SCENARIO_SEARCH) {
                                    $formFields[$relation] = $field;
                                //}
                            }
                        }
                    }
                } else {
                    if ($model->scenario != BaseModel::SCENARIO_SEARCH) {
                        $formFields[$relation] = $field;
                    }
                }

            }
        }
        return $formFields;
    }

    /**
     * @param array $formFields
     * @param \netis\crud\db\ActiveRecord $model
     * @param string $attribute
     * @param array $hiddenAttributes
     * @param bool $multiple true for multiple values inputs, usually used for search forms
     * @return array
     * @throws InvalidConfigException
     */
    protected static function addFormField($formFields, $model, $attribute, $hiddenAttributes, $multiple = false, $stubForm = null)
    {
        $attributeName = Html::getAttributeName($attribute);

        if (isset($hiddenAttributes[$attributeName])) {
            $formFields[$attribute] = Html::activeHiddenInput($model, $attribute);
            return $formFields;
        }

        $formFields[$attribute] = static::createActiveField($model, $attribute, [], $multiple, $stubForm = null);
        return $formFields;
    }


    /**
     * @param BaseModel $model
     * @param string                      $attribute
     * @param array                       $options
     * @param bool                        $multiple
     * @param $stubForm
     * @return \yii\bootstrap\ActiveField
     * @throws InvalidConfigException
     */
    public static function createActiveField($model, $attribute, $options = [], $multiple = false, $stubForm = null)
    {
        /** @var Formatter $formatter */
        $formatter = Yii::$app->formatter;
        if (!$stubForm) {
            $stubForm = new \stdClass();
            $stubForm->layout = 'default';
            $stubForm->validationStateOn = false;
        }
        /** @var \yii\bootstrap\ActiveField $field */
        $field = Yii::createObject([
            'class'     => \yii\bootstrap\ActiveField::class,
            'model'     => $model,
            'attribute' => $attribute,
            // a workaround, because it's used in the ActiveField constructor (horizontal/vertical layout)
            'form'      => $stubForm,
        ]);

        $attributeName      = Html::getAttributeName($attribute);
        $attributeFormat    = $model->getAttributeFormat($attributeName);
        $format             = is_array($attributeFormat) ? $attributeFormat[0] : $attributeFormat;
        $value              = Html::getAttributeValue($model, $attribute);
        $column             = $model->getTableSchema()->getColumn($attributeName);

        switch ($format) {
            case 'boolean':
                if ($multiple) {
                    $field->inline()->dropDownList([
                        '' => Yii::t('app', 'Любой'),
                        '0' => $formatter->booleanFormat[0],
                        '1' => $formatter->booleanFormat[1],
                    ], $options);
                } else {
                    $field->checkbox($options);
                }
                break;
            case 'shortLength':
                if (!isset($options['value'])) {
                    $options['value'] = $value === null ? null : $formatter->asMultiplied($value, 1000);
                }
                $field->textInput($options);
                $field->inputTemplate = '<div class="input-group">{input}<span class="input-group-addon">m</span></div>';
                break;
            case 'shortWeight':
                if (!isset($options['value'])) {
                    $options['value'] = $value === null ? null : $formatter->asMultiplied($value, 1000);
                }
                $field->textInput($options);
                $field->inputTemplate = '<div class="input-group">{input}<span class="input-group-addon">kg</span></div>';
                break;
            case 'multiplied':
                if (!isset($options['value'])) {
                    $options['value'] = $value === null ? null : $formatter->asMultiplied($value, $attributeFormat[1]);
                }
                $field->textInput($options);
                break;
            case 'integer':
                if (!isset($options['value'])) {
                    $options['value'] = $value;
                    if (is_array($options['value'])) {
                        $options['value'] = join(',',$options['value'] );
                    }
                }
                $field->textInput($options);
                break;
            case 'time':
                if (!isset($options['value'])) {
                    $options['value'] = $value;
                }
                $field->textInput($options);
                break;
            case 'datetime':
            case 'date':
                if (!isset($options['value'])) {
                    if ($value) {
                        if (!$model->hasErrors($attribute) && $value !== null) {
                            //$value = $formatter->format($value, $format);
                        }
                        $options['value'] = $value;
                    }
                }
                if (!isset($options['class'])) {
                    $options['class'] = 'form-control';
                }

                $field->parts['{input}'] = [
                    'class'     => DateRangePicker::className(),
                    'model'     => $model,
                    'attribute' => $attributeName,
                    'value'     => is_array($model->$attributeName) ? join(' - ', $model->$attributeName) : $model->$attributeName,
                    'options'   => $options,
                    'language'  => 'ru',
                    'pluginOptions' => [
                        'autoUpdateInput' => false,
                        'locale'=> [
                            'format' =>      'DD.MM.YYYY',
                            'separator'=>   ' - ',
                        ],
                        'opens'=>'left'
                    ]
                ];


                break;
            case 'enum':
                $items = $formatter->getEnums()->get($attributeFormat[1]);
                if (!$items) {
                    if (!isset($options['value'])) {
                        $options['value'] = $value;
                    }
                    if ($column && $column->type === 'string' && $column->size !== null) {
                        $options['maxlength'] = $column->size;
                    }
                    $field->textInput($options);
                    break;
                }
                if ($multiple) {
                    $options = array_merge([
                        'class' => 'select2',
                        'placeholder' => static::getPrompt(),
                        'multiple' => 'multiple',
                    ], $options);
                    $field->parts['{input}'] = [
                        'class'     => \maddoger\widgets\Select2::className(),
                        'model'     => $model,
                        'attribute' => $attribute,
                        'items'     => $items,
                        'clientOptions' => [
                            'minimumInputLength'    => ($model::isBigData()) ? 2 : 0,
                            'allowClear' => true,
                            'closeOnSelect' => true,
                        ],
                        'options' => $options,
                    ];
                } else {
                    if ($column !== null && $column->allowNull) {
                        $options['prompt'] = static::getPrompt();
                    }
                    $field->dropDownList($items, $options);
                }
                break;
            case 'flags':
                throw new InvalidConfigException('Flags format is not supported by '.get_called_class());
            case 'paragraphs':
                if (!isset($options['value'])) {
                    $options['value'] = Html::encode($value);
                }

                if ($multiple) {
                    $field->textInput($options);
                } else {
                    $field->textarea(array_merge(['cols'  => '80', 'rows'  => '10'], $options));
                }
                break;
            case 'file':
                if (!isset($options['value'])) {
                    $options['value'] = $value;
                }
                $field->fileInput($options);
                break;
            default:
            case 'text':
                if (!isset($options['value'])) {
                    $options['value'] = $value;
                }
                if ($column && $column->type === 'string' && $column->size !== null) {
                    $options['maxlength'] = $column->size;
                }
                if (is_array( $options['value'] )) {
                    $options['value']  = join(',',  $options['value'] );
                }
                $field->textInput($options);
                break;
        }
        return $field;
    }


    /**
     * Retrieves form fields configuration. Fields can be config arrays, ActiveField objects or closures.
     *
     * @param \yii\base\Model|\netis\crud\db\ActiveRecord $model
     * @param array           $fields
     * @param bool            $multiple         true for multiple values inputs, usually used for search forms
     * @param array           $hiddenAttributes list of attribute names to render as hidden fields
     * @param bool           $bothRelationsAndIds
     * @param $stubForm
     * @return array form fields
     * @throws InvalidConfigException
     */
    public static function getFormFields($model, $fields, $multiple = false, $hiddenAttributes = [], $bothRelationsAndIds = false, $stubForm = null)
    {
        /** @var BaseModel $model */
        if (!$model instanceof \yii\db\ActiveRecord) {
            return $model->safeAttributes();
        }

        $keys = Action::getModelKeys($model, false);

        $hiddenAttributes = array_flip($hiddenAttributes);

        list($behaviorAttributes, $blameableAttributes) = Action::getModelBehaviorAttributes($model);
        $attributes = $model->safeAttributes();
        $relations = $model->relations();

        if (($versionAttribute = $model->optimisticLock()) !== null) {
            $hiddenAttributes[$versionAttribute] = true;
        }

        $formFields = [];

        foreach ($fields as $key => $field) {
            $attributeName = Html::getAttributeName($field);

            if ($model->scenario == BaseModel::SCENARIO_UPDATE) {
                if (in_array($attributeName, $model->excludeFieldsUpdate())) continue;
            }
            if ($model->scenario == BaseModel::SCENARIO_CREATE) {
                if (in_array($attributeName, $model->excludeFieldsCreate())) continue;
            }
            if (!in_array($attributeName, $relations) && in_array($attributeName, $attributes)) {
                if ($field instanceof ActiveField) {
                    $formFields[$key] = $field;
                    continue;
                } elseif (!is_string($field) && is_callable($field)) {
                    $formFields[$key] = call_user_func($field, $model);
                    if (!is_string($formFields[$key])) {
                        throw new InvalidConfigException('Field definition must be either an ActiveField or a callable.');
                    }
                    continue;
                } elseif (!is_string($field)) {
                    throw new InvalidConfigException('Field definition must be either an ActiveField or a callable.');
                }

                if (!$bothRelationsAndIds) {
                    if ((in_array($attributeName, $behaviorAttributes))) {
                        continue;
                    }
                }

                $formFields = static::addFormField(
                    $formFields, $model, $field,
                    $hiddenAttributes, $multiple, $stubForm
                );
            }
        }

        foreach ($fields as $key => $field) {
            $attributeName = Html::getAttributeName($field);
            if (in_array($attributeName, $relations)) {

                if ($field instanceof ActiveField) {
                    $formFields[$key] = $field;
                    continue;
                } elseif (!is_string($field) && is_callable($field)) {
                    $formFields[$key] = call_user_func($field, $model);
                    if (!is_string($formFields[$key])) {
                        throw new InvalidConfigException('Field definition must be either an ActiveField or a callable.');
                    }
                    continue;
                } elseif (!is_string($field)) {
                    throw new InvalidConfigException('Field definition must be either an ActiveField or a callable.');
                }

                $activeRelation = $model->getRelation($attributeName);

                if (!$activeRelation->multiple) {
                    $right = reset($activeRelation->link);
                    if ($model->scenario == BaseModel::SCENARIO_UPDATE) {
                        if (in_array($right, $model->excludeFieldsUpdate())) continue;
                    }
                    if ($model->scenario == BaseModel::SCENARIO_CREATE) {
                        if (in_array($right, $model->excludeFieldsCreate())) continue;
                    }
                }

                $formFields = static::addRelationField(
                    $formFields, $model, $field,
                    $hiddenAttributes, $attributes, $multiple, $bothRelationsAndIds, $stubForm
                );
            } else if (!in_array($attributeName, $attributes)) {
                list($relationName) = $model->getRelatedByAttributeName($attributeName);
                if ($relationName) {
                    $formFields = static::addFormField(
                        $formFields, $model, $attributeName,
                        $hiddenAttributes, $multiple, $stubForm
                    );
                }
            }
        }

        return $formFields;
    }

    /**
     * @param \yii\widgets\ActiveForm $form
     * @param \yii\widgets\ActiveField $field
     * @return string
     */
    public static function renderField($form, $field)
    {
        if (!$field instanceof ActiveField) {
            return (string)$field;
        }

        $field->form = $form;

        if (isset($field->parts['{input}']) && is_array($field->parts['{input}'])) {
            $class                   = $field->parts['{input}']['class'];
            /** @var yii\base\Widget $class */
            $field->parts['{input}'] = $class::widget($field->parts['{input}']);
        }
        return (string)$field;
    }

    /**
     * @param \yii\widgets\ActiveForm $form
     * @param array $fields
     * @param int $topColumnWidth
     * @return string
     */
    public static function renderRow($form, $fields, $topColumnWidth = 12)
    {
        if (empty($fields)) {
            return '';
        }
        $result = [];
        $oneColumn = false; // optionally: count($fields) == 1;
        $result[] = $oneColumn ? '' : '<div class="row">';
        $columnWidth = ceil($topColumnWidth / count($fields));
        foreach ($fields as $column) {
            $result[] = $oneColumn ? '' : '<div class="col-sm-' . $columnWidth . '">';
            if (!is_array($column)) {
                $result[] = static::renderField($form, $column);
            } else {
                foreach ($column as $row) {
                    if (!is_array($row)) {
                        $result[] = static::renderField($form, $row);
                    } else {
                        $result[] = static::renderRow($form, $row);
                    }
                }
            }
            $result[] = $oneColumn ? '' : '</div>';
        }
        $result[] = $oneColumn ? '' : '</div>';

        return implode('', $result);
    }

    /**
     * @param \yii\base\Model $model
     * @param array[]         $fields
     *
     * @return bool
     */
    public static function hasRequiredFields($model, $fields)
    {
        foreach ($fields as $column) {
            if (!is_array($column)) {
                if ($column instanceof ActiveField && $model->isAttributeRequired($column->attribute)) {
                    return true;
                }
                continue;
            }

            foreach ($column as $row) {
                if (!is_array($row)) {
                    if ($row instanceof ActiveField && $model->isAttributeRequired($row->attribute)) {
                        return true;
                    }
                    continue;
                }

                if (static::hasRequiredFields($model, $row)) {
                    return true;
                }
            }

        }
        return false;
    }

    public static function getPrompt()
    {
        $prompt = null;
        $formatter = Yii::$app->formatter;
        if ($formatter instanceof \netis\crud\web\Formatter) {
            $prompt = $formatter->dropDownPrompt;
        } else {
            $prompt = strip_tags($formatter->nullDisplay);
        }

        if (trim($prompt) === '') {
            throw new InvalidConfigException('Prompt value cannot be empty string!');
        }

        return trim($prompt);
    }


}
