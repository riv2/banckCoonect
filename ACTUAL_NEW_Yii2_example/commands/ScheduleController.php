<?php
namespace app\commands;

use app\components\DateTime;
use app\models\document\ProjectExecution;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\enum\TaskType;
use app\models\pool\ParsingError;
use app\models\reference\Project;
use app\models\reference\Schedule;
use app\models\register\Error;
use app\models\register\FileExchange;
use app\models\register\Parsing;
use app\models\pool\PriceParsed;
use app\models\pool\PriceRefined;
use app\models\register\Task;
use yii;
use yii\console\Controller;

class ScheduleController extends Controller
{

    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep $processName", $result);
        $count = count($result);
        return ceil($count);
    }

    public function actionIndex() {

        $dayOfWeek = intval(date("N"),10);

        $dayOfWeek++;

        if ($dayOfWeek == 8) {
            $dayOfWeek = 1;
        }


        /** @var Schedule[] $schedule */
        $schedule = Schedule::find()
            ->andWhere([
                'day'           => $dayOfWeek,
                'status_id'     => Status::STATUS_ACTIVE,
            ])
            ->andWhere(['BETWEEN', 'time', date('H:i:', strtotime('4 minutes ago')) . '00', date('H:i:').'00'])
            ->all();

        echo date("N").' '.date('H:i:').'00'.PHP_EOL;

        foreach ($schedule as $item) {
            echo $item.PHP_EOL;
            $requester = $item->requester;
            if ($requester) {
                $requester->schedule($item->params);
            }
        }
    }

    public function actionProject() {
        if ($this->processIsRun('schedule/project') > 2) {
            return;
        }

        $dayOfWeek = intval(date("N"),10);

        $dayOfWeek++;

        if ($dayOfWeek == 8) {
            $dayOfWeek = 1;
        }


        /** @var Project[] $projects */
        $projects = Project::find()
            ->andWhere([
                'scheduled_daily_time' => date('H:i:').'00',
                'status_id'            => Status::STATUS_ACTIVE,
            ])
            ->andWhere([
                'LIKE',
                'scheduled_weekdays',
                '%'.$dayOfWeek.'%',
                false
            ])
            ->all();

        foreach ($projects as $project) {
            $project->prepareProjectExecution(false);
        }
    }


//    public function actionCheckParsingFinished() {
//        $finishedIds = Parsing::find()
//            ->andWhere([
//                'status_id' => Status::STATUS_ACTIVE,
//            ])
//            ->andWhere([
//                'not',
//                ['parsing_status_id' => ParsingStatus::STATUS_DONE],
//            ])
//            ->select('id')
//            ->column();
//
//        foreach($finishedIds as  $parsingId) {
//            if (file_exists(Parsing::finishedDir().DIRECTORY_SEPARATOR.$parsingId.'.cdp')){
//                Parsing::parsingFinished($parsingId);
//            }
//        }
//
//        $hangedIds = Parsing::find()
//            ->andWhere([
//                'status_id' => Status::STATUS_ACTIVE,
//            ])
//            ->andWhere([
//                'parsing_status_id' => ParsingStatus::STATUS_PROCESSING
//            ])
//            ->andWhere([
//                '<', 'updated_at', new DateTime('-10 min')
//            ])
//            ->select('id')
//            ->column();
//
//        foreach($hangedIds as $parsingId) {
//            Parsing::parsingFinished($parsingId, ParsingStatus::STATUS_HANGED);
//        }
//    }

}