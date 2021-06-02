<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ReportKpiProject;
use app\components\ValidationRules;
use app\models\enum\HoradricCubeStatus;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Status;
use app\models\reference\Competitor;
use app\models\reference\ParsingProject;
use app\models\register\HoradricCube;
use app\models\register\Parsing;
use Cassandra\Date;
use netis\crud\db\ActiveQuery;
use yii;
use yii\db\ActiveRelationTrait;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class ReportMatching
 * @package app\models\pool
 *
 * @property string id

 * @property DateTime created_at дата расчета
 * @property DateTime parsed_from дата начала парсинга
 * @property DateTime parsed_to дата окончания парсинга

 * @property string parsing_project_id '                => 'Проект парсинга ID',
 * @property string parsing_project_name '              => 'Проект парсинганазв.',
 * @property string competitor_id '             => 'Конкурент ID',
 * @property string competitor_name '           => 'Конкурент назв.',
 * @property string parsing_id '      => 'Парсинг',
 * @property string parsing_name '      => 'Парсинг',

 * @property int parsed_total '              => 'Всего',
 * @property int parsed_in_stock '           => 'В налич',
 * @property int filtered_out_stock '        => 'Отф. по не налич',
 * @property int filtered_by_api '           => 'Отф. по апи',
 * @property int filtered_existing '         => 'Отф. существ',
 * @property int filtered_total '            => 'Отф. всего',
 * @property int matched_auto '              => 'Сопост',
 * @property int to_manual_matching '         => 'Сопост',
 * @property int matchedOk
 * @property int matchedWrong
 *
 * @property Competitor competitor
 * @property Parsing string parsing '      => 'Парсинг',
 * @property ParsingProject parsingProject'                   => 'Проект парсинга',
 */

class ReportMatching extends Pool
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
        return 'Отчет сопоставлению';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Отчет сопоставлению';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleDateTime(['created_at','parsed_from','parsed_to']),
            [
                [['id','parsing_project_id','parsing_project_name','competitor_id','competitor_name','parsing_id','parsing_name'], 'string'],
                [['parsed_total','parsed_in_stock','filtered_out_stock','filtered_by_api','filtered_existing','filtered_total','to_manual_matching','matched_auto'], 'number'],
            ]
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'id' => 'id',

            'parsed_from' => 'Старт парсинга',
            'parsed_to' => 'Конец парсинга',
            'created_at' => 'Дата расчета',

            'parsing_project_id'                => 'Проект парсинга ID',
            'parsing_project_name'              => 'Проект парсинганазв.',
            'parsingProject'                   => 'Проект парсинга',
            'competitor_id'             => 'Конкурент ID',
            'competitor_name'           => 'Конкурент назв.',
            'competitor'                => 'Конкурент',
            'parsing_id'                => 'Парсинг',
            'parsing'                           => 'Парсинг',
            'parsing_name'                      => 'Парсинг',

            'parsed_total'              => 'Всего',
            'parsed_in_stock'           => 'В налич',
            'filtered_out_stock'        => 'Отф. по не налич',
            'filtered_by_api'           => 'Отф. по API',
            'filtered_existing'         => 'Отф. существ',
            'filtered_total'            => 'Отф. всего',
            'to_manual_matching'        => 'На ручное',
            'matched_auto'              => 'Автоматом',
            'matchedOk'                 => 'Сопост.',
            'matchedWrong'              => 'Несоотв.',
        ]);
    }

    public static function create($ids, $parsing_project_id, $parallel_id) {
        $rm = self::find()->andWhere([
            'parsing_project_id' => $parsing_project_id,
            'parsing_id' => $parallel_id
        ])->one();
        if (!$rm) {
            $rm = new self();
        }
        $rm->parsing_id = $parallel_id;
        $rm->parsing_project_id = $parsing_project_id;
        $rm->calculate(explode(',', $ids));
        $rm->save(false);
        return $rm;
    }

    public function calculate($ids) {
        $this->created_at           = new DateTime();		
        $this->competitor_id        = $this->parsingProject->competitor_id;
        $this->parsing_name         = $this->parsingProject->name;
        $this->parsing_project_name = $this->parsingProject->name;
        $this->competitor_name      = $this->parsingProject->competitor->name;

        $statusCN = PriceParsedStatus::COLLECTING_NEW;
        $statusCF = PriceParsedStatus::COLLECTING_FILTERED_OUT;
        $statusCA = PriceParsedStatus::COLLECTING_API;
        $statusMF = PriceParsedStatus::MATCHING_FILTERED_OUT;
        $statusMM = PriceParsedStatus::MATCHING_TO_MANUAL;
        $statusMA = PriceParsedStatus::MATCHING_AUTOMATCHED;

        $counts = PriceParsed::find()
            ->andWhere([
                'parsing_project_id' => $this->parsing_project_id,
                'parsing_id' => $ids,
            ])
            ->asArray()
            ->select(new Expression("
                COUNT(id) as parsed_total,
                COUNT(id) filter (where out_of_stock = FALSE) as parsed_in_stock, 
                COUNT(id) filter (where out_of_stock = TRUE AND price_parsed_status_id = $statusCF) as filtered_out_stock,
                COUNT(id) filter (where out_of_stock = FALSE AND price_parsed_status_id = $statusCF) as filtered_existing,
                COUNT(id) filter (where price_parsed_status_id = $statusMF AND error_message LIKE 'API%') as filtered_by_api,
                COUNT(id) filter (where price_parsed_status_id = $statusMA) as matched_auto,
                COUNT(id) filter (where price_parsed_status_id = $statusMM) as to_manual_matching,
                MIN(started_at) as parsed_from,
                MAX(finished_at) as parsed_to
            "))
            ->one();

        $this->setAttributes($counts);
        //$this->filtered_existing -= $this->filtered_by_api;
        $this->filtered_total = $this->filtered_out_stock + $this->filtered_by_api + $this->filtered_existing;
    }

    public static function createFromProject(ParsingProject $parsingProject) {
        $rm = self::find()->andWhere([
            'AND',
            ['parsing_project_id' => $parsingProject->id],
            ['>', 'created_at', new DateTime('today')]
        ])->one();
        if (!$rm) {
            $rm = new self();
        }
        $rm->parsing_project_id = $parsingProject->id;
        $rm->calculateByProject();
        if ($rm->parsed_total > 0) {
            $rm->save(false);
        }
        return $rm;
    }

    public function calculateByProject() 
    {       
        $this->created_at           = new DateTime();        
        $this->competitor_id        = $this->parsingProject->competitor_id;
        $this->parsing_name         = $this->parsingProject->name;
        $this->parsing_project_name = $this->parsingProject->name;
        $this->competitor_name      = $this->parsingProject->competitor->name;

        $statusCN = PriceParsedStatus::COLLECTING_NEW;
        $statusCF = PriceParsedStatus::COLLECTING_FILTERED_OUT;
        $statusMF = PriceParsedStatus::MATCHING_FILTERED_OUT;
        $statusMM = PriceParsedStatus::MATCHING_TO_MANUAL;
        $statusMA = PriceParsedStatus::MATCHING_AUTOMATCHED;

        $counts = PriceParsed::find()
            ->andWhere([
                'AND',
                ['parsing_project_id' => $this->parsing_project_id],
                [
                    'BETWEEN', 'extracted_at',
                    (new DateTime('yesterday 00:00:00'))->format(DateTime::DB_DATETIME_FORMAT),
                    (new DateTime('yesterday 23:59:59'))->format(DateTime::DB_DATETIME_FORMAT),
                ],
            ])
            ->select(new Expression("
                COUNT(id) as parsed_total,
                COUNT(id) filter (where out_of_stock = FALSE) as parsed_in_stock, 
                COUNT(id) filter (where out_of_stock = TRUE AND price_parsed_status_id = $statusCF) as filtered_out_stock,
                COUNT(id) filter (where price_parsed_status_id = $statusMF) as filtered_existing,
                COUNT(id) filter (where price_parsed_status_id = $statusMF AND error_message LIKE 'API%') as filtered_by_api,
                COUNT(id) filter (where price_parsed_status_id = $statusMA) as matched_auto,
                COUNT(id) filter (where price_parsed_status_id = $statusMM) as to_manual_matching,
                MIN(started_at) as parsed_from,
                MAX(finished_at) as parsed_to
            "))
            ->asArray()
            ->one();

        $this->setAttributes($counts);
        $this->filtered_existing -= $this->filtered_by_api;
        $this->filtered_total = $this->filtered_out_stock + $this->filtered_by_api + $this->filtered_existing;
    }

    public function getPPUrl($params = []) {
        return Url::to([
            '/crud-price-parsed',
            'PriceParsed' => array_merge([
                'extracted_at' => $this->parsed_from->format("d.m.Y H:i:s") . ' - ' . $this->parsed_to->format("d.m.Y H:i:s"),
                'parsing_project_id' => $this->parsing_project_id,
            ], $params)
        ]);
    }

    public function crudIndexColumns()
    {

        return array_merge(parent::crudIndexColumns(),[
            'parsed_from',

            'parsingProject',
            'competitor',

            'created_at',

            'parsed_total' => [
                'attribute' => 'parsed_total',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->parsed_total, $model->getPPUrl(),['target' => '_blank']);
                }
            ],

            'parsed_in_stock' => [
                'attribute' => 'parsed_in_stock',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->parsed_in_stock, $model->getPPUrl([
                        'out_of_stock' => 0,
                    ]),['target' => '_blank']);
                }
            ],
            'filtered_out_stock' => [
                'attribute' => 'filtered_out_stock',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->filtered_out_stock, $model->getPPUrl([
                        'out_of_stock' => 1,
                        'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT
                    ]),['target' => '_blank']);
                }
            ],
            'filtered_by_api' => [
                'attribute' => 'filtered_by_api',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    /** @var ReportMatching $model */
                    return Html::a($model->filtered_by_api, $model->getPPUrl([
                        'price_parsed_status_id' => PriceParsedStatus::MATCHING_FILTERED_OUT
                    ]),['target' => '_blank']);
                }
            ],
            'filtered_existing' => [
                'attribute' => 'filtered_existing',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->filtered_existing, $model->getPPUrl([
                        'price_parsed_status_id' => PriceParsedStatus::MATCHING_FILTERED_OUT
                    ]),['target' => '_blank']);
                }
            ],
            'filtered_total' => [
                'attribute' => 'filtered_total',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->filtered_total, $model->getPPUrl([
                        'price_parsed_status_id' => PriceParsedStatus::COLLECTING_FILTERED_OUT.','.PriceParsedStatus::MATCHING_FILTERED_OUT
                    ]),['target' => '_blank']);
                }
            ],
            'matched_auto' => [
                'attribute' => 'matched_auto',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->matched_auto, $model->getPPUrl([
                        'price_parsed_status_id' => PriceParsedStatus::MATCHING_AUTOMATCHED
                    ]),['target' => '_blank']);
                }
            ],
            'to_manual_matching' => [
                'attribute' => 'to_manual_matching',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->to_manual_matching, $model->getPPUrl([
                        'price_parsed_status_id' => PriceParsedStatus::MATCHING_TO_MANUAL
                    ]),['target' => '_blank']);
                }
            ],
            'matchedOk' => [
                'label' => 'Сопоставлено',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->matchedOk, Url::to([
                        '/horadric-cube',
                        'HoradricCube' => [
                            'parsing_project_id' => $model->parsing_project_id,
                            'created_at' => $model->parsed_from->format("d.m.Y H:i:s") . ' - ' . (new DateTime())->format("d.m.Y H:i:s"),
                            'auto_match' => 0,
                            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_MATCHED,
                        ],
                    ]),['target' => '_blank']);
                }
            ],
            'matchedWrong' => [
                'label' => 'Несоотв',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var ReportMatching $model */
                    return Html::a($model->matchedWrong, Url::to([
                        '/horadric-cube',
                        'HoradricCube' => [
                            'parsing_project_id' => $model->parsing_project_id,
                            'created_at' => $model->parsed_from->format("d.m.Y H:i:s") . ' - ' . (new DateTime())->format("d.m.Y H:i:s"),
                            'auto_match' => 0,
                            'horadric_cube_status_id' => HoradricCubeStatus::STATUS_WRONG,
                        ]
                    ]),['target' => '_blank']);
                }
            ],
        ]);
    }

    private $matchedWrongCount = null;
    private $matchedOkCount = null;

    private function getMatchedCounts() {
        if ($this->matchedWrongCount === null) {
            $sm = HoradricCubeStatus::STATUS_MATCHED;
            $sw = HoradricCubeStatus::STATUS_WRONG;
            $counts = HoradricCube::find()
                ->andWhere([
                    'parsing_project_id' => $this->parsing_project_id,
                    'auto_match' => false,
                ])
                ->andWhere([
                    '>','created_at', $this->parsed_from
                ])
                ->asArray()
                ->select(new Expression("
            COUNT(id) filter (where horadric_cube_status_id = $sm) as matched_ok, 
            COUNT(id) filter (where horadric_cube_status_id = $sw) as matched_wrong"))
                ->one();
            $this->matchedOkCount = $counts['matched_ok'];
            $this->matchedWrongCount = $counts['matched_wrong'];
        }
    }
    public function getMatchedWrong() {
        $this->getMatchedCounts();
        return $this->matchedWrongCount;
    }

    public function getMatchedOk() {
        $this->getMatchedCounts();
        return $this->matchedOkCount;
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
            'parsingProject',
            'parsing',
        ]);
    }

    public function getSort($config = [])
    {
        return parent::getSort(['defaultOrder' => ['parsed_from' => SORT_DESC, 'parsing_project_name' => SORT_ASC, 'competitor_name' => SORT_ASC]]);
    }


    /**
     * @return yii\db\ActiveQuery
     */
    public function getParsing() {
        return $this->hasOne(Parsing::class, ['id' => 'parsing_id'])->cache(3600);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getParsingProject() {
        return $this->hasOne(ParsingProject::class, ['id' => 'parsing_project_id'])->cache(3600);
    }

    /**
     * @return yii\db\ActiveQuery
     */
    public function getCompetitor() {
        return $this->hasOne(Competitor::class, ['id' => 'competitor_id'])->cache(3600);
    }
}