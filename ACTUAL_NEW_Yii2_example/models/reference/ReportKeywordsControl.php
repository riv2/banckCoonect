<?php
namespace app\models\reference;

use app\components\ValidationRules;
use app\models\document\ProjectExecution;
use app\models\enum\Status;
use app\models\pool\LogPriceCalculation;
use app\models\pool\LogProjectExecution;
use yii\bootstrap\Html;
use yii\db\Expression;
use yii\web\JsExpression;

/**
 * Class ReportKeywordsControl
 * @package app\models\register
 *
 * @property string item_name    Товар',
 * @property string item_brand_name    Бренд',
 * @property string item_ym_url    YM URL',
 * @property string item_ym_index    YM ID',
 * @property string item_pricing_keyword    КС',
 * @property string item_pricing_must_be    Must Be',
 * @property string item_pricing_dont_be    Dont Be',
 * @property int competitor_prices_count    Кол-во цен.',
 *
 * @property Item               item                Товар
 * @property ProjectExecution   projectExecution    Исполнение проекта
 */

class ReportKeywordsControl extends ProjectItem
{

    public static function tableName()
    {
        return ProjectItem::tableName();
    }
    public function getDefaultOrderColumns() {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return true;
    }

    public $name;
    public $project_execution_id;
    public $item_name;
    public $item_brand_name;

    public static function fileImportEnabled() {
        return false;
    }
    public static function fileExportEnabled() {
        return false;
    }
    public static function crudCreateEnabled() {
        return false;
    }
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Контроль КС';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Контроль КС';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleUuid('id'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('project_execution_id'),
            [
                [['name','item_name', 'item_brand_name'], 'string'],
                [['competitor_prices_count'], 'number'],
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'id'                            => 'ID',
            'competitorPricesCount'         => 'Кол-во цен',
            'item_name'                     => 'Товар',
            'item'                          => 'Товар',
            'item_brand_name'               => 'Бренд'
        ]);
    }

    protected $_lpcCount = false;
    public function getCompetitorPricesCount() {
        if ($this->_lpcCount === false) {
            $this->_lpcCount = LogPriceCalculation::find()->andWhere([
                'project_execution_id' => $this->project_execution_id,
                'item_id' => $this->item_id,
                'status_id' => Status::STATUS_ACTIVE
            ])
                ->count();
        }
        return $this->_lpcCount;
    }

    public function search($params = [])
    {
        $query = parent::search($params);

        $projectId =  ProjectExecution::find()
            ->andWhere([
                'id'  => $this->project_execution_id,
            ])
            ->select('project_id')
            ->scalar();

        if (!$projectId) {
            die('Отсутствует проект');
        }

        $query->andWhere([
            't.project_id' => $projectId
        ]);
        $query->select([
            'i.*',
            'i.id as item_id',
            'b.name as item_brand_name',
            'i.name as name',
            'i.name as item_name',
            new Expression("'{$this->project_execution_id}' as project_execution_id")
        ]);
        $query->andFilterWhere(['ILIKE','b.name',$this->item_brand_name]);
        $query->andFilterWhere(['ILIKE','o.name',$this->item_name]);
        return $query;
    }

    public static function find()
    {
        $query = parent::find();
        $query->alias('t');
        $query->leftJoin([
            'i' => Item::tableName()
        ], 't.item_id = i.id');

        $query->leftJoin([
            'b' => Brand::tableName()
        ], 'b.id = i.brand_id ');

        $query->groupBy([ 'i.id','b.id']);
        return $query;
    }

    public static function itemUpdateJs($param) {
        return new JsExpression(preg_replace('/\s+/', ' ',"setTimeout(
        (function(input){
            return function(){
                jQuery(input).animate({'opacity':0.5});
                jQuery.ajax({
                    'url'       :'/report/item-quick-update?id='+jQuery(input).attr('data-item_id')+'&Item[$param]='+encodeURIComponent(input.value), 
                    'type': 'get',
                    'dataType':'json',
                    'success'   :function(json){
                        jQuery(input).parents('tr').find('.rkc-ym-link').attr('href',json.Item.ym_url);
                        jQuery(input).stop().css('opacity',1);
                    }
                });
            };
        })(this),10);"));
    }


    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return [
            'item',
           // 'projectExecution',
        ];
    }
    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return [
            'id' => [
                'attribute'     => 'id',
                'headerOptions' => ['style' => 'min-width:150px;'],
            ],
            'item' => [
                'attribute'     => 'item',
            ],
            'item_brand_name'   => [
                'attribute'     => 'item_brand_name',
            ],
            'item_ym_index'                 => [
                'label' => 'YM ID',
                'attribute'     => 'item.ym_index',
                'format'=> 'raw',
                'value' => function($model) {
                    return Html::input('text', 'item_ym_index', $model->item->ym_index, [
                        'class'         => 'form-control',
                        'style'         => 'width: 100px;',
                        'data-item_id'  => $model->item_id,
                        'onkeydown'     => ReportKeywordsControl::itemUpdateJs('ym_index'),
                    ]);
                }
            ],
            'item_pricing_keyword'          => [
                'label' => 'КС',
                'format'=> 'raw',
                'attribute'     => 'item.pricing_keyword',
                'value' => function($model) {
                    return Html::input('text', 'item_pricing_keyword', $model->item->pricing_keyword, [
                        'class'         => 'form-control',
                        'style'         => 'width: 200px;',
                        'data-item_id'  => $model->item_id,
                        'onkeydown'     => ReportKeywordsControl::itemUpdateJs('pricing_keyword'),
                    ]);
                }
            ],
            'item_pricing_must_be'          => [
                'label' => 'Must Be',
                'format'=> 'raw',
                'attribute'     => 'item.pricing_must_be',
                'value' => function($model) {
                    return Html::input('text', 'item_pricing_must_be', $model->item->pricing_must_be, [
                        'class' => 'form-control',
                        'style' => 'width: 150px;',
                        'data-item_id'  => $model->item_id,
                        'onkeydown'     => ReportKeywordsControl::itemUpdateJs('pricing_must_be'),
                    ]);
                }
            ],
            'item_pricing_dont_be'          => [
                'label' => 'Dont Be',
                'format'=> 'raw',
                'attribute'     => 'item.pricing_dont_be',
                'value' => function($model) {
                    return Html::input('text', 'item_pricing_dont_be', $model->item->pricing_dont_be, [
                        'class' => 'form-control',
                        'style' => 'width: 150px;',
                        'data-item_id'  => $model->item_id,
                        'onkeydown'     => ReportKeywordsControl::itemUpdateJs('pricing_dont_be'),
                    ]);
                }
            ],
            'competitorPricesCount' => [
                'attribute' => 'competitorPricesCount',
            ],
            'item_ym_url' => [
                'label' => 'YM URL',
                'format'=> 'raw',
                'value' => function($model) {
                    $url = $model->item->ymUrl;
                    if (!$url) {
                        return null;
                    }
                    return '<a href="'.ANON_URL.$url.'" class="btn btn-default btn-xs rkc-ym-link" target="_blank"><span class="fa fa-link"></span> Перейти</a>';
                }
            ],
        ];
    }

    public function getSort($config = [])
    {
        $config['attributes'] = array_merge([
            'item' => [
                'asc'   => ['i.name' => SORT_ASC],
                'desc'  => ['i.name' => SORT_DESC],
                'label' => 'Товар',
                'default' => SORT_ASC
            ],
            'name' => [
                'asc'   => ['i.name' => SORT_ASC],
                'desc'  => ['i.name' => SORT_DESC],
                'label' => 'Товар',
                'default' => SORT_ASC
            ],
            'item_brand_name' => [
                'asc'   => ['b.name' => SORT_ASC],
                'desc'  => ['b.name' => SORT_DESC],
                'label' => 'Бренд',
                'default' => SORT_ASC
            ],
            'item.ym_index' => [
                'asc'   =>   ['i.ym_index' => SORT_ASC],
                'desc'  =>   ['i.ym_index' => SORT_DESC],
                'label' => 'YM ID',
                'default' => SORT_ASC
            ],
            'item.pricing_keyword' => [
                'asc'   =>   ['i.pricing_keyword' => SORT_ASC],
                'desc'  =>   ['i.pricing_keyword' => SORT_DESC],
                'label' => 'КС',
                'default' => SORT_ASC
            ],
            'item.pricing_must_be' => [
                'asc'   =>   ['i.pricing_must_be' => SORT_ASC],
                'desc'  =>   ['i.pricing_must_be' => SORT_DESC],
                'label' => 'must_be',
                'default' => SORT_ASC
            ],
            'item.pricing_dont_be' => [
                'asc'   =>   ['i.pricing_dont_be' => SORT_ASC],
                'desc'  =>   ['i.pricing_dont_be' => SORT_DESC],
                'label' => 'dont_be',
                'default' => SORT_ASC
            ],
//            'competitor_prices_count' => [
//                'asc'   =>   ['competitor_prices_count' => SORT_ASC],
//                'desc'  =>   ['competitor_prices_count' => SORT_DESC],
//                'default' => SORT_ASC
//            ],
        ], isset($config['attributes'])?$config['attributes']:[]);
        return parent::getSort($config);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExecution()
    {
        return $this->hasOne(ProjectExecution::className(), ['id' => 'project_execution_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->item_id;
    }

}