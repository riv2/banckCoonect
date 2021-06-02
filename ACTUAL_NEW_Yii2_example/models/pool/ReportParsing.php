<?php

namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\reference\Competitor;
use app\models\reference\ParsingProject;
use app\models\register\Parsing;
use yii\data\ArrayDataProvider;

/**
 *
 */
class ReportParsing extends Pool
{
    public $date_interval = null;

    /**
     * @return string
     */
    public static function getSingularNominativeName()
    {
        return 'Отчет парсингов по конкурентом';
    }

    /**
     * @return string
     */
    public static function getPluralNominativeName()
    {
        return 'Отчеты парсингов по конкурентом';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['date_interval'], 'safe']
            ]
        );
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date_interval' => 'Временной интервал',
        ]);
    }

    /**
     *
     */
    public function generateReport()
    {
        if (!$this->date_interval) {
            $this->date_interval = (new DateTime('00:00'))->format(DateTime::DB_DATETIME_FORMAT)
                . ' - ' . (new DateTime('23:59'))->format(DateTime::DB_DATETIME_FORMAT);
        }
        $dateFrom = null;
        $dateTo = null;
        $intervalParts = explode(' - ', $this->date_interval);
        if (count($intervalParts) > 1) {
            $dateFrom = $intervalParts[0];
            $dateTo = $intervalParts[1];
        }

        $dataQuery = Parsing::find()
            ->select([
                'p.id',
                'p.name',
                'p.created_at',
                'p.finished_at',
                'p.updated_at',
                'p.parsing_status_id',
                'p.global_count',
                'page_count' => 'p.parsed_count',
                'p.errors_count',
                'p.in_stock_count',
                'competitor' => 'c.name',
            ])
            ->alias('p')
            ->andWhere(['BETWEEN', 'p.created_at', $dateFrom, $dateTo])
            ->leftJoin(['pp' => ParsingProject::tableName()], 'pp.id = p.parsing_project_id')
            ->leftJoin(['c' => Competitor::tableName()], 'c.id = pp.competitor_id')
            ->orderBy('p.created_at DESC, p.name')
            ->asArray();
        $modelsByCompetitors = [];
        $competitorMetrics = [];
        $activeParsingsData = \yii\helpers\ArrayHelper::index(
            \Yii::$app->cache->exists('active_parsings_data')
                ? \Yii::$app->cache->get('active_parsings_data')
                : [],
            'id'
        );


        foreach ($dataQuery->each() as $data) {
            $data = array_merge(
                $data,
                isset($activeParsingsData[$data['id']])
                    ? $activeParsingsData[$data['id']]
                    : []
            );
            $modelsByCompetitors[$data['competitor']][] = $data;
            if (!isset($competitorMetrics[$data['competitor']])) {
                $competitorMetrics[$data['competitor']] = [
                    'global_count' => 0,
                    'page_count' => 0,
                    'errors_count' => 0,
                    'in_stock_count' => 0,
                ];
            }
            $competitorMetrics[$data['competitor']]['global_count']   += $data['global_count'];
            $competitorMetrics[$data['competitor']]['page_count']     += $data['page_count'];
            $competitorMetrics[$data['competitor']]['errors_count']   += $data['errors_count'];
            $competitorMetrics[$data['competitor']]['in_stock_count'] += $data['in_stock_count'];
        }

        return [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $modelsByCompetitors,
                'pagination' => false,
            ]),
            'competitorMetrics' => $competitorMetrics,
        ];
    }
}