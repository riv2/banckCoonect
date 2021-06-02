<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ReportKpiProject;
use app\components\ValidationRules;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Status;
use app\models\reference\Competitor;
use app\models\reference\ParsingProject;
use app\models\reference\Project;
use yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class ReportKpi
 * @package app\models\pool
 *
 * @property DateTime $from_date [timestamp(0)]
 * @property DateTime $to_date [timestamp(0)]
 * @property string $project_id [uuid]
 * @property string $project_name [varchar(255)]
 * @property string $competitor_id [uuid]
 * @property string $competitor_name [varchar(255)]
 * @property string $project_execution_id [uuid]
 * @property int $total_competitor_sku [bigint]
 * @property int $total_parsed [bigint]
 * @property int $in_stock [bigint]
 * @property int $out_stock [bigint]
 * @property int $unparsed [bigint]
 * @property int $in_calculation [bigint]
 * @property string $avg_price_life [time(0)]
 * @property float $percent_missed [double precision]
 * @property array $regions [jsob]
 *
 * @property Project project
 * @property Competitor competitor
 */

class ReportKpi extends Pool
{
    public static function noCount()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Отчет KPI';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Отчет KPI';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleDateTime(['created_at','from_date','to_date']),
            [
                [['id','project_id','project_name','competitor_id','competitor_name','project_execution_id'], 'string'],
                [['avg_price_life','regions'], 'safe'],
                [['total_competitor_sku','total_parsed','in_stock','out_stock','unparsed','in_calculation','percent_missed'], 'number'],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'id' => 'id',

            'created_at' => 'Создание',
            'from_date'  => 'От',
            'to_date'    => 'Расчет проекта',

            'project_id'                => 'Проект ID',
            'project_name'              => 'Проект назв.',
            'project'                   => 'Проект',
            'competitor_id'             => 'Конкурент ID',
            'competitor_name'           => 'Конкурент назв.',
            'competitor'                => 'Конкурент',
            'project_execution_id'      => 'Расчет проекта',

            'avg_price_life'            => 'Жизнь цены',
            'total_competitor_sku'      => 'Известных SKU',
            'total_parsed'              => 'Собрано цен',
            'in_stock'                  => 'В наличии',
            'out_stock'                 => 'Не в наличии',
            'unparsed'                  => 'Не собрано',
            'in_calculation'            => 'Уч. при расчете',
            'percent_missed'            => 'Статус',
            'regions'                   => 'Регионы',
        ]);
    }

    public function crudIndexColumns()
    {
        return array_merge(parent::crudIndexColumns(),[
            'to_date',

            'project',
            'competitor',

            'avg_price_life' => [
                'attribute' => 'avg_price_life',
                'value' => function($model) {
                    return $model->avg_price_life ? explode(':',$model->avg_price_life)[0].' ч.' : null;
                }
            ],
            'total_competitor_sku' => [
                'attribute' => 'total_competitor_sku',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    return Html::a($model->total_competitor_sku, $model->getDetailsUrl([
                    ]),['target' => '_blank']);
                }
            ],
            'total_parsed' => [
                'attribute' => 'total_parsed',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    return Html::a($model->total_parsed, $model->getDetailsUrl([
                        'is_parsed' => 1,
                    ]),['target' => '_blank']);
                }
            ],
            'in_stock' => [
                'attribute' => 'in_stock',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    return Html::a($model->in_stock, $model->getDetailsUrl([
                        'is_parsed' => 1,
                        'out_of_stock' => 0
                    ]),['target' => '_blank']);
                }
            ],
            'in_calculation' => [
                'attribute' => 'in_calculation',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    return Html::a($model->in_calculation, $model->getDetailsUrl([
                        'is_parsed' => 1,
                        'is_used_in_calc' => 1
                    ]),['target' => '_blank']);
                }
            ],
            'out_stock' => [
                'attribute' => 'out_stock',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    return Html::a($model->out_stock, $model->getDetailsUrl([
                        'is_parsed' => 1,
                        'out_of_stock' => 1
                    ]),['target' => '_blank']);
                }
            ],
            'unparsed' => [
                'attribute' => 'unparsed',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    return Html::a($model->unparsed, $model->getDetailsUrl([
                        'is_parsed' => 0
                    ]),['target' => '_blank']);
                }
            ],
            'percent_missed' => [
                'attribute' => 'percent_missed',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportKpi $model */
                    $p = $model->percent_missed;
                    return Html::tag('span',$p.'%', [
                        'style' => 'color: '.self::getColorString($p)
                    ]);
                }
            ],
        ]);
    }

    private function getDetailsUrl($params = []) {
        return  Url::to([
            '/crud-log-kpi',
            'LogKpi' => array_merge([
                'project_execution_id'  => $this->project_execution_id,
                'competitor_id'         => $this->competitor_id,
            ],$params)
        ]);
    }

    private function getExtractedRange() {
        return $this->from_date->format('d.m.Y H:i:s') . ' - ' . $this->to_date->format('d.m.Y H:i:s');
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
//            'competitor',
//            'project',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'competitor',
            'project',
        ]);
    }

    public function getSort($config = [])
    {
        return parent::getSort(['defaultOrder' => ['to_date' => SORT_DESC,'project_name' => SORT_ASC,'competitor_name' => SORT_ASC]]);
    }

    public static function getColorString ($value, $alpha = 1)
    {
        $g = $value < 90 ? 50 + round($value) : 255;
        $r = round($value < 90 ? 250 - 200 * $value / 100 : 100 - $value);
        return "rgba($r, $g, 50, $alpha)";
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(Project::class, ['id' => 'project_id'])->cache(3600);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getCompetitor() {
        return $this->hasOne(Competitor::class, ['id' => 'competitor_id'])->cache(3600);
    }
}