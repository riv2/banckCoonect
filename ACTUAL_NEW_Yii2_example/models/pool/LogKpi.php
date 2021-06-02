<?php
namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\ParsingProject;
use app\models\reference\Project;
use app\models\document\ProjectExecution;
use app\models\reference\ProjectItem;
use app\validators\UuidValidator;
use yii\bootstrap\Html;

/**
 * Class LogKpi
 *
 * @package app\models\pool
 *
 * @property string id
 * @property string project_execution_id

 * @property string item_id
 * @property string competitor_id
 * @property string project_id
 * @property string price_refined_id
 * @property string parsing_id
 * @property string parsing_project_id

 * @property DateTime created_at
 * @property DateTime extracted_at
 * @property DateTime calculated_at

 * @property boolean is_parsed
 * @property boolean http404
 * @property boolean out_of_stock
 * @property boolean is_used_in_calc

 * @property float price
 * @property string url
 * @property int status_id
 *
 * @property Item               item                Товар
 * @property Competitor         competitor          Конкурнет
 * @property Project            project             Проект
 */

class LogKpi extends Pool
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Расчет проекта';
    }

    public static function noCount() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Расчет проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleDateTime('extracted_at'),
            ValidationRules::ruleDateTime('calculated_at'),
            ValidationRules::ruleUuid('item_id'),
            ValidationRules::ruleUuid('competitor_id'),
            ValidationRules::ruleUuid('project_execution_id'),
            ValidationRules::ruleUuid('project_id'),
            ValidationRules::ruleUuid('parsing_project_id'),
            ValidationRules::ruleUuid('parsing_id'),
            ValidationRules::ruleUuid('price_refined_id'),
            [
                [['price'], 'number'],
                [['url'], 'string'],
                [['out_of_stock','is_used_in_calc','is_parsed','http404'], 'boolean'],
            ],
            ValidationRules::ruleDefault('is_parsed', false),
            ValidationRules::ruleDefault('out_of_stock', false),
            ValidationRules::ruleDefault('http404', false),
            ValidationRules::ruleDefault('is_used_in_calc', false),
            ValidationRules::ruleEnum('status_id', Status::className()),
            []
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'id'                => 'ID',
                'project_execution_id' => 'Проект',

                'item_id'           => 'Товар',
                'competitor_id'     => 'Конкурент',
                'project_id'        => 'Проект',

                'item'           => 'Товар',
                'competitor'     => 'Конкурент',
                'project'        => 'Проект',
                'projectExecution' => 'Расчет',
                'parsing' => 'Парсинг',
                'parsingProject' => 'Проект парсинга',

                'created_at'        => 'Создание',
                'extracted_at'      => 'Спарсена в',
                'calculated_at'     => 'Участвовала в ',

                'is_parsed'         => 'Спарсено',
                'http404'           => '404',
                'out_of_stock'      => 'Отсутствует',
                'is_used_in_calc'   => 'В расчете',

                'price'             => 'Цена',
                'url'               => 'URL',
                'status_id'         => 'Статус',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
        ];
    }

    public function getSort($config = [])
    {
        $config['defaultOrder'] = ['created_at' => SORT_DESC];
        $config['attributes'] = [
            'item' => [
                'asc'   => ['item_name' => SORT_ASC],
                'desc'  => ['item_name' => SORT_DESC],
                'label' => 'Товар',
                'default' => SORT_ASC
            ],
        ];
        $sort = parent::getSort($config);
        return $sort;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'created_at',
            'project',
            'projectExecution',
            'competitor',
            'item',
            'is_parsed',
            'http404',
            'out_of_stock',
            'is_used_in_calc',
            'price',
            'url' => [
                'attribute' => 'url',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var LogKpi $model */
                    if (!$model->url) {
                        return null;
                    }
                    $urls = [];
                    if (strpos($model->url, '{') === 0) {
                        $urls = explode(',', substr($model->url,1,-1));
                    } else {
                        $urls = [$model->url];
                    }
                    $hrefs = array_map(function($url){
                        return \yii\helpers\Html::a($url, $url,['target' => '_blank']);
                    }, $urls);
                    return implode('<br/>',$hrefs);
                }
            ],
            'extracted_at',
            'calculated_at',
            'parsingProject',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'project',
            'item',
            'competitor',
            'projectExecution',
            'parsingProject',
            'status',
        ]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id'])->cache(3600);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExecution()
    {
        return $this->hasOne(ProjectExecution::className(), ['id' => 'project_execution_id','project_id' => 'project_id',])->cache(3600);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParsingProject()
    {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id'])->cache(3600);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompetitor()
    {
        return $this->hasOne(Competitor::className(), ['id' => 'competitor_id'])->cache(3600);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id'])->cache(3600);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id'])->cache(3600);;
    }


    /**
     * @inheritdoc
     */
    protected function addQuickSearchConditions(\yii\db\ActiveQuery $query)
    {
        $query = parent::addQuickSearchConditions($query);
        $query->leftJoin(['i' => Item::tableName()], 'i.id = t.item_id');
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function processSearchToken($token, array $attributes, $tablePrefix = null)
    {
        //$c = parent::processSearchToken($token, $attributes, $tablePrefix);
        if (UuidValidator::test($token)) {
            return ['i.id' => $token];
        }
        return ['ILIKE', 'i.name', $token];
    }
}