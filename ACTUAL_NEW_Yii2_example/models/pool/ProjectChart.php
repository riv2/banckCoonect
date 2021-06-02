<?php

namespace app\models\pool;

use app\components\base\type\Pool;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\cross\CategoryItem;
use app\models\document\ProjectExecution;
use app\models\reference\Competitor;
use app\models\reference\Item;
use app\models\reference\ProjectCompetitor;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 *
 * @property string $project_id
 * @property string $project_execution_id
 * @property integer $type
 * @property string $data
 * @property string $date
 */
class ProjectChart extends Pool
{
    const TYPE_VI_COMPARE = 1;
    const TYPE_PRICE_DYNAMICS = 2;

    private $_options = null;
    private $_series = null;
    private $_dateIntervalParts = null;

    /**
     * @return string
     */
    public static function getSingularNominativeName(): string
    {
        return 'График проекта';
    }

    /**
     * @return string
     */
    public static function getPluralNominativeName(): string
    {
        return 'Графики проекта';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid(['project_id', 'project_execution_id']),
            ValidationRules::ruleDateTime('date'),
            [
                [['data'], 'string'],
                [['type'], 'integer'],
            ]
        );
    }

    public function load($data, $formName = null)
    {
        $load = parent::load($data, $formName);
        $dateIntervalParts = $this->getDateIntervalParts();
        if (count($dateIntervalParts) > 1) {
            if ($dateIntervalParts[0] === $dateIntervalParts[1]) {
                $date = $dateIntervalParts[0];
                $this->project_execution_id = ProjectExecution::find()
                    ->andWhere([
                        'AND',
                        ['project_id' => $this->project_id],
                        ['BETWEEN', 'created_at', $date . ' 00:00:00', $date . ' 23:59:59'],
                    ])
                    ->select('id')
                    ->orderBy('created_at DESC')
                    ->scalar()
                ;
            }
        }
        return $load;
    }

    public function getChartOptions()
    {
        if (!$this->_options) {
            $dateIntervalParts = $this->getDateIntervalParts();
            switch ($this->type) {
                case self::TYPE_VI_COMPARE:
                    if ($this->project_execution_id && $this->project_execution_id !== '') {
                        $projectCompetitorsNames = ProjectCompetitor::find()
                            ->andWhere(['project_id' => $this->project_id])
                            ->select('name')
                            ->orderBy('name');
                        if (\Yii::$app->request->get('competitor_id')) {
                            $projectCompetitorsNames
                                ->andWhere([
                                    'competitor_id' => \Yii::$app->request->get('competitor_id'),
                                ]);
                        }
                        $projectCompetitorsNames = $projectCompetitorsNames->column();
                        $this->_options = [
                            'chart' => [
                                'type' => 'bar',
                                'height' => 350,
                                'width' => 1000,
                                'events' => [
                                    'click' => new JsExpression("
                                        function(event, chartContext, w) {
                                            if (w.seriesIndex >= 0 && w.dataPointIndex >= 0) {
                                                var url = '" . Url::to([
                                                    '/crud-log-price-calculation',
                                                    'LogPriceCalculation[project_id]' => $this->project_id,
                                                    'chart_type' => self::TYPE_VI_COMPARE,
                                                ]) . "&chart_series_index=' + w.seriesIndex
                                                    + '&chart_date=" . date(DateTime::DB_DATE_FORMAT . ' 00:00:00') . "'
                                                    + '&chart_competitor_name=' + w.config.xaxis.categories[w.dataPointIndex];
                                                if (window.params.competitor_id) {
                                                    url += '&LogPriceCalculation[competitor_id]=' + window.params.competitor_id;
                                                }
                                                if (window.params.brand_id) {
                                                    url += '&LogPriceCalculation[brand_id]=' + window.params.brand_id;                           
                                                }
                                                //if (window.params.category_id) {
                                                //    url += '&category_id=' + window.params.category_id;                                                    
                                                //}
                                                var win = window.open(url, '_blank');
                                                win.focus();
                                            }
                                        }
                                    "),
                                ],
                            ],
                            'colors' => [
                                '#018ffb',
                                '#5ce497',
                                '#0067b9',
                                '#feb02a',
                                '#f6495f',
                            ],
                            'plotOptions' => [
                                'bar' => [
                                    'horizontal' => false,
                                    'columnWidth' => '70%',
                                    'endingShape' => 'rounded',
                                ],
                            ],
                            'dataLabels' => [
                                'enabled' => false
                            ],
                            'stroke' => [
                                'show' => true,
                                'width' => 1,
                                'colors' => ['transparent']
                            ],
                            'xaxis' => [
                                'categories' => $projectCompetitorsNames ? $projectCompetitorsNames : [],
                            ],
                            'fill' => [
                                'opacity' => 1
                            ],
                        ];
                    } else {
                        $minMaxDate = $this->getSeriesMaxDate();
                        $maxDate = $minMaxDate[0];
                        $minDate = min($minMaxDate[1], strtotime('-4 days', $maxDate));

                        $this->_options = [
                            'chart' => [
                                'type' => 'bar',
                                'height' => 350,
                                'width' => 1000,
                                'events' => [
                                    'click' => new JsExpression("
                                            function(event, chartContext, w) {
                                            if (w.seriesIndex >= 0 && w.dataPointIndex >= 0) {
                                                var url = '" . Url::to([
                                                    '/crud-log-price-calculation',
                                                    'LogPriceCalculation[project_id]' => $this->project_id,
                                                    'chart_type' => self::TYPE_VI_COMPARE,
                                                ]) . "&chart_series_index=' + w.seriesIndex
                                                    +  '&chart_date=' + w.config.series[w.seriesIndex].data[w.dataPointIndex][0];
                                                if (window.params.competitor_id) {
                                                    url += '&LogPriceCalculation[competitor_id]=' + window.params.competitor_id;
                                                }
                                                if (window.params.brand_id) {
                                                    url += '&LogPriceCalculation[brand_id]=' + window.params.brand_id;                           
                                                }
                                                //if (window.params.category_id) {
                                                //    url += '&category_id=' + window.params.category_id;                                                    
                                                //}
                                                var win = window.open(url, '_blank');
                                                win.focus();
                                            }
                                        }
                                    "),
                                ],
                            ],
                            'colors' => [
                                '#018ffb',
                                '#5ce497',
                                '#0067b9',
                                '#feb02a',
                                '#f6495f',
                            ],
                            'plotOptions' => [
                                'bar' => [
                                    'horizontal' => false,
                                    'columnWidth' => '70%',
                                    'endingShape' => 'rounded',
                                ],
                            ],
                            'dataLabels' => [
                                'enabled' => false
                            ],
                            'stroke' => [
                                'show' => true,
                                'width' => 1,
                                'colors' => ['transparent']
                            ],
                            'tooltip' => [
                                'custom' => new JsExpression("
                                    function({ series, seriesIndex, dataPointIndex, w }) {
                                        return '<div style=\'padding: 10px 4px\'>' +
                                            '<span>' + w.config.series[seriesIndex].data[dataPointIndex][2] + '</span>' +
                                            '</div>'
                                        ;
                                    }
                                "),
                            ],
                            'xaxis' => [
                                'type' => 'datetime',
                                'min' => $minDate * 1000,
                                'max' => strtotime('+1 day', $maxDate) * 1000,
                                'labels' => [
                                    'show' => true,
                                    'format' => 'dd-MM-yyyy',
                                ],
                            ],
                            'fill' => [
                                'opacity' => 1
                            ],
                        ];
                    }
                break;
                case self::TYPE_PRICE_DYNAMICS:
                    $minMaxDate = $this->getSeriesMaxDate();
                    $maxDate = $minMaxDate[0];
                    $minDate = min($minMaxDate[1], strtotime('-4 days', $maxDate));

                    $this->_options = [
                        'chart' => [
                            'type' => 'bar',
                            'height' => 400,
                            'width' => '100%',
                            'zoom' => [
                                'enabled' => false,
                            ],
                            'events' => [
                                'click' => new JsExpression("
                                        function(event, chartContext, w) {
                                            if (w.seriesIndex >= 0 && w.dataPointIndex >= 0) {
                                                var url = '"
                                                    . Url::to([
                                                        '/report-project-chart',
                                                        'project_id' => $this->project_id,
                                                        'type' => self::TYPE_PRICE_DYNAMICS,
                                                    ])
                                                    . "&series_index=' + w.seriesIndex
                                                    + '&date=' + w.config.series[w.seriesIndex].data[w.dataPointIndex][0];
                                                if (window.params.competitor_id) {
                                                    url += '&competitor_id=' + window.params.competitor_id;
                                                }
                                                if (window.params.brand_id) {
                                                    url += '&brand_id=' + window.params.brand_id;                           
                                                }
                                                //if (window.params.category_id) {
                                                //    url += '&category_id=' + window.params.category_id;                                                    
                                                //}
                                                var win = window.open(url, '_blank');
                                                win.focus();
                                            }
                                        }
                                    "),
                            ]
                        ],
                        'colors' => [
                            '#5ce497',
                            '#f6495f',
                            '#0067b9',
                            '#018ffb',
                        ],
                        'xaxis' => [
                            'type' => 'datetime',
                            'position' => 'top',
                            'min' => $minDate * 1000,
                            'max' => strtotime('+1 day', $maxDate) * 1000,
                            'labels' => [
                                'show' => true,
                                'format' => 'dd-MM-yyyy',
                            ],
                            'crosshairs' => [
                                'fill' => [
                                    'type' => 'gradient',
                                    'gradient' => [
                                        'colorFrom' => '#D8E3F0',
                                        'colorTo' => '#BED1E6',
                                        'stops' => [0, 100],
                                        'opacityFrom' => 0.4,
                                        'opacityTo' => 0.5,
                                    ]
                                ]
                            ],
                        ],
                        'plotOptions' => [
                            'bar' => [
                                'horizontal' => false,
                            ],
                        ],
                        'dataLabels' => [
                            'enabled' => false
                        ],
                        'stroke' => [
                            'show' => true,
                            'colors' => ['transparent']
                        ],
                        'legend' => [
                            'verticalAlign' => 'bottom',
                            'horizontalAlign' => 'left',
                        ],
                        'tooltip' => [
                            'custom' => new JsExpression("
                                    function({ series, seriesIndex, dataPointIndex, w }) {
                                        return '<div style=\'padding: 10px 4px\'>' +
                                            '<span>' + w.config.series[seriesIndex].data[dataPointIndex][2] + '</span>' +
                                            '</div>'
                                        ;
                                    }
                                "),
                        ],
                    ];
                break;
            }
        }
        return $this->_options;
    }

    public function generateChartSeries(): array
    {
        if (!$this->_series) {
            $dateIntervalParts = $this->getDateIntervalParts();
            if ($this->type == self::TYPE_VI_COMPARE) {
                if ($this->project_execution_id && $this->project_execution_id !== '') {
                    if (\Yii::$app->request->get('competitor_id')) {
                        $competitors = [\Yii::$app->request->get('competitor_id')];
                    } else {
                        $competitors = ProjectCompetitor::find()
                            ->andWhere(['project_id' => $this->project_id])
                            ->select('competitor_id')
                            ->orderBy('name')
                            ->column();
                    }
                    $brandId = \Yii::$app->request->get('brand_id');
                    $categoryId = \Yii::$app->request->get('category_id');
                    $itemId = \Yii::$app->request->get('item_id');
                    $result = [
                        [
                            'name' => '> 5%',
                            'data' => [],
                        ],
                        [
                            'name' => '+1% - +5%',
                            'data' => [],
                        ],
                        [
                            'name' => '+-1%',
                            'data' => [],
                        ],
                        [
                            'name' => '-1% - -5%',
                            'data' => [],
                        ],
                        [
                            'name' => '< -5%',
                            'data' => [],
                        ],
                    ];
                    foreach ($competitors as $competitorId) {
                        $data = LogPriceCalculation::find()
                            ->alias('t')
                            ->select([
                                '> 5%' => 'COUNT(
                                    CASE WHEN
                                        t.price_refined > t.price_default
                                        AND (((t.price_refined/t.price_default) * 100 - 100) > 5)
                                    THEN 1 ELSE null END
                                )',
                                '+1% - +5%' => 'COUNT(
                                    CASE WHEN
                                        t.price_refined > t.price_default
                                        AND 1 < ((t.price_refined/t.price_default) * 100 - 100)
                                        AND ((t.price_refined/t.price_default) * 100 - 100) < 5
                                    THEN 1 ELSE null END
                                )',
                                '+-1%' => 'COUNT(
                                    CASE WHEN
                                        -1 < ((t.price_refined/t.price_default) * 100 - 100)
                                        AND ((t.price_refined/t.price_default) * 100 - 100) < 1
                                    THEN 1 ELSE null END
                                )',
                                '-1% - -5%' => 'COUNT(
                                    CASE WHEN
                                        t.price_refined < t.price_default
                                        AND -1 > ((t.price_refined/t.price_default) * 100 - 100)
                                        AND ((t.price_refined/t.price_default) * 100 - 100) > -5
                                    THEN 1 ELSE null END
                                )',
                                '< -5%' => 'COUNT(
                                    CASE WHEN
                                        t.price_refined < t.price_default
                                        AND (((t.price_refined/t.price_default) * 100 - 100) < -5)
                                    THEN 1 ELSE null END
                                )',
                            ])
                            ->andWhere([
                                't.project_execution_id' => $this->project_execution_id,
                                't.competitor_id' => $competitorId,
                            ]);
                        if ($brandId) {
                            $data->andWhere(['t.brand_id' => $brandId]);
                        }
                        if ($categoryId) {
                            $data->leftJoin(
                                ['ci' => CategoryItem::tableName()],
                                'ci.item_id = t.item_id AND ci.is_top = true'
                            )->andWhere(['ci.category_id' => $categoryId]);
                        }
                        if ($itemId) {
                            $data->andWhere(['t.item_id' => $itemId]);
                        }
                        $data = $data->asArray()->one();
                        $result[0]['data'][] = $data['> 5%'];
                        $result[1]['data'][] = $data['+1% - +5%'];
                        $result[2]['data'][] = $data['+-1%'];
                        $result[3]['data'][] = $data['-1% - -5%'];
                        $result[4]['data'][] = $data['< -5%'];
                    }
                    $this->_series = array_values($result);
                } else {
                    $dataSet = ProjectChart::find()
                        ->andWhere([
                            'AND',
                            [
                                'project_id' => $this->project_id,
                                'type' => self::TYPE_VI_COMPARE
                            ],
                            ['BETWEEN', 'date', $dateIntervalParts[0] . ' 00:00:00', $dateIntervalParts[1] . ' 23:59:59']
                        ])
                        ->select(['data'])
                        ->indexBy('date')
                        ->orderBy('date')
                        ->asArray()
                        ->column()
                    ;
                    $brandId = \Yii::$app->request->get('brand_id');
                    $competitorId = \Yii::$app->request->get('competitor_id');
                    $categoryId = \Yii::$app->request->get('category_id');
                    $itemId = \Yii::$app->request->get('item_id');

                    $result = [
                        [
                            'name' => '> 5%',
                            'data' => [],
                        ],
                        [
                            'name' => '+1% - +5%',
                            'data' => [],
                        ],
                        [
                            'name' => '+-1%',
                            'data' => [],
                        ],
                        [
                            'name' => '-1% - -5%',
                            'data' => [],
                        ],
                        [
                            'name' => '< -5%',
                            'data' => [],
                        ],
                    ];
                    $competitorsNames = [];
                    foreach ($dataSet as $date => $data) {
                        $data = json_decode($data, true);
                        if ($competitorId) {
                            if (!isset($competitorsNames[$competitorId])) {
                                $competitorsNames[$competitorId] = Competitor::find()
                                    ->andWhere([
                                        'id' => $competitorId,
                                    ])
                                    ->select('name')
                                    ->scalar();
                            }
                            $competitorName = $competitorsNames[$competitorId];
                            if (isset($data[$competitorId])) {
                                for ($i = 0; $i < count($result); $i++) {
                                    if (is_array($data[$competitorId][$i])) {
                                        if ($brandId || $categoryId) {
                                            $filteredItems = Item::find()
                                                ->alias('t')
                                                ->andWhere([
                                                    't.id' => $itemId ? $itemId : $data[$competitorId][$i],
                                                ]);
                                            if ($brandId) {
                                                $filteredItems
                                                    ->andWhere([
                                                        't.brand_id' => $brandId,
                                                    ]);
                                            }
                                            if ($categoryId) {
                                                $filteredItems
                                                    ->leftJoin(
                                                        ['ci' => CategoryItem::tableName()],
                                                        'ci.item_id = t.id AND ci.is_top = true'
                                                    )
                                                    ->andWhere([
                                                        'ci.category_id' => $categoryId,
                                                    ]);
                                            }
                                            $count = $filteredItems->count();
                                            $result[$i]['data'][] = [$date, $count, $competitorName . ': ' . $count];
                                        } else {
                                            if ($itemId && in_array($itemId, $data[$competitorId][$i])) {
                                                $result[$i]['data'][] = [$date, 1, $competitorName . ': ' . 1];
                                            } else {
                                                $count = count($data[$competitorId][$i]);
                                                $result[$i]['data'][] = [$date, count($data[$competitorId][$i]), $competitorName . ': ' . $count];
                                            }
                                        }
                                    } else {
                                        if (!$brandId && !$categoryId && !$itemId) {
                                            $result[$i]['data'][] = [$date, $data[$competitorId][$i], $competitorName . ': ' . $data[$competitorId][$i]];
                                        }
                                    }
                                }
                            }
                        } else {
                            $tmpData = [[], [], [], [], []];
                            $tmpTooltips = ['','','','',''];
                            foreach ($data as $dataCompetitorId => $counts) {
                                if (!isset($competitorsNames[$dataCompetitorId])) {
                                    $competitorsNames[$dataCompetitorId] = Competitor::find()
                                        ->andWhere(['id' => $dataCompetitorId])
                                        ->select('name')
                                        ->scalar();
                                }
                                $competitorName = $competitorsNames[$dataCompetitorId];
                                for ($i = 0; $i < count($counts); $i++) {
                                    if (is_array($counts[$i])) {
                                        if ($brandId || $categoryId) {
                                            $filteredItems = Item::find()
                                                ->alias('t')
                                                ->andWhere([
                                                    't.id' => $itemId ? $itemId : $counts[$i],
                                                ]);
                                            if ($brandId) {
                                                $filteredItems
                                                    ->andWhere([
                                                        't.brand_id' => $brandId,
                                                    ]);
                                            }
                                            if ($categoryId) {
                                                $filteredItems
                                                    ->leftJoin(
                                                        ['ci' => CategoryItem::tableName()],
                                                        'ci.item_id = t.id AND ci.is_top = true'
                                                    )
                                                    ->andWhere([
                                                        'ci.category_id' => $categoryId,
                                                    ]);
                                            }
                                            $count = $filteredItems->count();
                                            $tmpData[$i][] = $count;
                                            $tmpTooltips[$i] .= $competitorName . ': ' . $count . '<br>';
                                        } else {
                                            if ($itemId && in_array($itemId, $counts[$i])) {
                                                $tmpData[$i][] = 1;
                                                $tmpTooltips[$i] .= $competitorName . ': 1<br>';
                                            } else {
                                                $count = count($counts[$i]);
                                                $tmpData[$i][] = $count;
                                                $tmpTooltips[$i] .= $competitorName . ': ' . $count . '<br>';
                                            }
                                        }
                                    } else {
                                        $tmpData[$i][] = $counts[$i];
                                        $tmpTooltips[$i] .= $competitorName . ': ' . $counts[$i] . '<br>';
                                    }
                                }
                            }
                            for ($i = 0; $i < count($result); $i++) {
                                $sum = array_sum($tmpData[$i]);
                                $result[$i]['data'][] = [
                                    $date,
                                    $sum,
                                    'Всего: ' . $sum . '<hr style="margin: 0px 0 15px 0;border-top-color: #999;">' . $tmpTooltips[$i],
                                ];
                            }
                        }
                    }

                    $this->_series = $result;
                }
            } else {
                $dataSet = self::find()
                    ->andWhere([
                        'AND',
                        [
                            'project_id' => $this->project_id,
                            'type' => self::TYPE_PRICE_DYNAMICS,
                        ],
                        ['BETWEEN', 'date', $dateIntervalParts[0] . ' 00:00:00', $dateIntervalParts[1] . ' 23:59:59']
                    ])
                    ->select(['data'])
                    ->indexBy('date')
                    ->orderBy('date')
                    ->asArray()
                    ->column();
                $brandId = \Yii::$app->request->get('brand_id');
                $competitorId = \Yii::$app->request->get('competitor_id');
                $categoryId = \Yii::$app->request->get('category_id');
                $itemId = \Yii::$app->request->get('item_id');

                $result = [[], [], [], []];
                $competitorsNames = [];
                foreach ($dataSet as $date => $data) {
                    $data = json_decode($data, true);
                    if ($competitorId) {
                        if (!isset($competitorsNames[$competitorId])) {
                            $competitorsNames[$competitorId] = Competitor::find()
                                ->andWhere(['id' => $competitorId])
                                ->select('name')
                                ->scalar();
                        }
                        $competitorName = $competitorsNames[$competitorId];
                        if (isset($data[$competitorId])) {
                            for ($i = 0; $i < count($data[$competitorId]); $i++) {
                                if (is_array($data[$competitorId][$i])) {
                                    if ($brandId || $categoryId) {
                                        $filteredItems = Item::find()
                                            ->alias('t')
                                            ->andWhere([
                                                't.id' => $itemId ? $itemId : $data[$competitorId][$i],
                                            ]);
                                        if ($brandId) {
                                            $filteredItems
                                                ->andWhere([
                                                    't.brand_id' => $brandId,
                                                ]);
                                        }
                                        if ($categoryId) {
                                            $filteredItems
                                                ->leftJoin(
                                                    ['ci' => CategoryItem::tableName()],
                                                    'ci.item_id = t.id AND ci.is_top = true'
                                                )
                                                ->andWhere([
                                                    'ci.category_id' => $categoryId,
                                                ]);
                                        }
                                        $count = $filteredItems->count();
                                        $result[$i][] = [$date, $count, $competitorName . ': ' . $count];
                                    } else {
                                        $count = count($data[$competitorId][$i]);
                                        if ($itemId) {
                                            $count = (int)in_array($itemId, $data[$competitorId][$i]);
                                        }
                                        if ($count > 0) {
                                            $result[$i][] = [$date, count($data[$competitorId][$i]), $competitorName . ': ' . $count];
                                        }
                                    }
                                } else {
                                    if (!$brandId && !$categoryId) {
                                        $result[$i][] = [$date, $data[$competitorId][$i], $competitorName . ': ' . $data[$competitorId][$i]];
                                    }
                                }
                            }
                        }
                    } else {
                        $tmpData = [[], [], [], []];
                        $tmpTooltips = ['','','',''];
                        foreach ($data as $dataCompetitorId => $counts) {
                            if (!isset($competitorsNames[$dataCompetitorId])) {
                                $competitorsNames[$dataCompetitorId] = Competitor::find()
                                    ->andWhere(['id' => $dataCompetitorId])
                                    ->select('name')
                                    ->scalar();
                            }
                            $competitorName = $competitorsNames[$dataCompetitorId];
                            for ($i = 0; $i < count($counts); $i++) {
                                if (is_array($counts[$i])) {
                                    if ($brandId || $categoryId) {
                                        $filteredItems = Item::find()
                                            ->alias('t')
                                            ->andWhere([
                                                't.id' => $itemId ? $itemId : $counts[$i],
                                            ]);
                                        if ($brandId) {
                                            $filteredItems
                                                ->andWhere([
                                                    't.brand_id' => $brandId,
                                                ]);
                                        }
                                        if ($categoryId) {
                                            $filteredItems
                                                ->leftJoin(
                                                    ['ci' => CategoryItem::tableName()],
                                                    'ci.item_id = t.id AND ci.is_top = true'
                                                )
                                                ->andWhere([
                                                    'ci.category_id' => $categoryId,
                                                ]);
                                        }
                                        $count = $filteredItems->count();
                                        $tmpData[$i][] = $count;
                                        $tmpTooltips[$i] .= $competitorName . ': ' . $count . '<br>';
                                    } else {
                                        $count = count($counts[$i]);
                                        if ($itemId) {
                                            $count = (int)in_array($itemId, $counts[$i]);
                                        }
                                        if ($count > 0) {
                                            $tmpData[$i][] = $count;
                                            $tmpTooltips[$i] .= $competitorName . ': ' . $count . '<br>';
                                        }
                                    }
                                } else {
                                    $tmpData[$i][] = $counts[$i];
                                    $tmpTooltips[$i] .= $competitorName . ': ' . $counts[$i] . '<br>';
                                }
                            }
                        }
                        for ($i = 0; $i < count($result); $i++) {
                            $sum = array_sum($tmpData[$i]);
                            $result[$i][] = [
                                $date,
                                $sum,
                                'Всего: ' . $sum . '<hr style="margin: 0px 0 15px 0;border-top-color: #999;">' . $tmpTooltips[$i],
                            ];
                        }
                    }
                }

                $this->_series = [
                    [
                        'name' => 'Подняли',
                        'data' => $result[0],
                    ],
                    [
                        'name' => 'Снизили',
                        'data' => $result[1],
                    ],
                    [
                        'name' => 'Нулевые',
                        'data' => $result[2],
                    ],
                    [
                        'name' => '+- 1%',
                        'data' => $result[3],
                    ],
                ];
            }
        }
        return $this->_series;
    }

    public function getSeriesMaxDate() {
        $series = $this->generateChartSeries();
        $maxDate = '1970-01-01 00:00:00';
        $minDate = '2099-01-01 00:00:00';
        foreach ($series as $seriesData) {
            $maxDate = max(array_merge(
                [$maxDate],
                array_map(function ($a) {return $a[0]; }, $seriesData['data'])
            ));
            $minDate = min(array_merge(
                [$minDate],
                array_map(function ($a) {return $a[0]; }, $seriesData['data'])
            ));
        }
        return [
            DateTime::createFromFormat(DateTime::DB_DATETIME_FORMAT, $maxDate)->getTimestamp(),
            DateTime::createFromFormat(DateTime::DB_DATETIME_FORMAT, $minDate)->getTimestamp(),
        ];
    }

    public function getDateIntervalParts() {
        if (!$this->_dateIntervalParts) {
            $dateIntervalParts = explode(' - ', \Yii::$app->request->get('date_interval'));
            if (count($dateIntervalParts) === 1) {
                $dateIntervalParts = [date(DateTime::DB_DATE_FORMAT), date(DateTime::DB_DATE_FORMAT, strtotime('-1 day'))];
            } else {
                foreach ($dateIntervalParts as $i => $date) {
                    $dateIntervalParts[$i] = DateTime::createFromFormat('d-m-Y', $date)->format(DateTime::DB_DATE_FORMAT);
                }
            }
            $this->_dateIntervalParts = $dateIntervalParts;
        }
        return $this->_dateIntervalParts;
    }
}