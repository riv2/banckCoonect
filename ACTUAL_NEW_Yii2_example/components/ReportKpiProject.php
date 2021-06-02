<?php

namespace app\components;

use app\models\document\ProjectExecution;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Status;
use app\models\pool\LogKpi;
use app\models\pool\LogPriceCalculation;
use app\models\pool\LogProjectExecution;
use app\models\pool\PriceParsed;
use app\models\pool\PriceRefined;
use app\models\pool\ReportKpi;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\ParsingProject;
use app\models\reference\Project;
use app\models\reference\ProjectItem;
use yii\base\BaseObject;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class ReportKpiProject
 * @package app\components
 *
 * @property ProjectExecution projectExecution
 * @property DateTime calculatedAt
 *
 * @property array totalParsedCounts
 * @property array inStockParsedCounts
 * @property array outStockParsedCounts
 * @property array totalCompetitorSkuCounts
 * @property array tookActionInCalculation
 *
 * @property Competitor[] competitors
 */
class ReportKpiProject extends BaseObject
{

    /**
     * @var Project
     */
    public $project = null;
    /**
     * @var ProjectExecution
     */
    public $projectExecution = null;

    private $_counts = [];

    public $createdAt = null;
    public $fromDate = null;
    public $toDate = null;

    public function calculate() {
        $this->createdAt = new DateTime();
        $this->fromDate = new DateTime();
        if ($this->projectExecution) {
            $this->toDate = $this->projectExecution->started_at;
        }
        if (!$this->toDate) {
            $this->toDate = new DateTime();
        }
        $span = $this->project->price_relevance_time_span ?? (24 * 3600);
        $this->fromDate->setTimestamp($this->toDate->getTimestamp() - $span);

        $this->_counts = LogKpi::find()
            ->andWhere([
                'project_execution_id' => $this->projectExecution->id
            ])
            ->select([
                'competitor_id' => 'competitor_id',
                new Expression('count(item_id) total_count'),
                new Expression('sum(is_parsed::int) parsed_count'),
                new Expression('sum(is_used_in_calc::int) calculated_count'),
                new Expression('sum(out_of_stock::int) out_of_stock_count'),
            ])
            ->groupBy([
                'competitor_id',
            ])
            ->indexBy('competitor_id')
            ->asArray()
            ->all();

        $avgs = LogKpi::find()
            ->andWhere([
                'project_execution_id' => $this->projectExecution->id,
                'is_used_in_calc' => true,
            ])
            ->select([
                'competitor_id' => 'competitor_id',
                new Expression('AVG(calculated_at - extracted_at) avg_price_life')
            ])
            ->groupBy([
                'competitor_id',
            ])
            ->indexBy('competitor_id')
            ->asArray()
            ->all();

        foreach ($avgs as $competitorId => $avg) {
            $this->_counts[$competitorId]['avg_price_life'] = $avg['avg_price_life'];
        }

    }

    public function getTotalCount($competitorId) {
        return $this->_counts[$competitorId]['total_count'] ?? 0;
    }

    public function getParsedCount($competitorId) {
        return $this->_counts[$competitorId]['parsed_count'] ?? 0;
    }

    public function getUnparsedCount($competitorId) {
        return $this->getTotalCount($competitorId)  -  $this->getParsedCount($competitorId) ;
    }

    public function getOutOfStockCount($competitorId) {
        return $this->_counts[$competitorId]['out_of_stock_count'] ?? 0;
    }

    public function getInStockCount($competitorId) {
        return  $this->getParsedCount($competitorId) -  $this->getOutOfStockCount($competitorId);
    }

    public function getUsedInCalcCount($competitorId) {
        return  $this->_counts[$competitorId]['calculated_count'] ?? 0;
    }

    public function getAvgPriceLife($competitorId) {
        return  $this->_counts[$competitorId]['avg_price_life'] ?? 0;
    }

    public function getPercentMissed($competitorId) {
        $del = $this->getInStockCount($competitorId) + $this->getUnparsedCount($competitorId);
        // Уч. в расчете / ( В наличии. + Не собрано)
        return $del !== 0 ? round($this->getUsedInCalcCount($competitorId) / $del,2) * 100  : 0;
    }


    public function populate(ReportKpi $reportKpi, Competitor $competitor) {
        $key = $competitor->id;
        $reportKpi->total_competitor_sku = $this->getTotalCount($key);
        $reportKpi->in_calculation = $this->getUsedInCalcCount($key);
        $reportKpi->avg_price_life = $this->getAvgPriceLife($key);
        $reportKpi->total_parsed = $this->getParsedCount($key);
        $reportKpi->in_stock = $this->getInStockCount($key);
        $reportKpi->out_stock = $this->getOutOfStockCount($key);
        $reportKpi->percent_missed = $this->getPercentMissed($key);
        $reportKpi->competitor_name = $competitor->name;
        $reportKpi->unparsed = $reportKpi->total_competitor_sku - $reportKpi->total_parsed;
        $reportKpi->created_at = $this->createdAt;
        $reportKpi->from_date = $this->fromDate;
        $reportKpi->to_date = $this->toDate;
        $reportKpi->project_name = $this->project->name;
        if (strpos($reportKpi->avg_price_life, '.') !== false) {
            $reportKpi->avg_price_life = explode('.', $reportKpi->avg_price_life)[0];
        }
    }

}