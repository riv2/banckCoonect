<?php
namespace app\controllers;

use app\components\base\Entity;
use app\models\cross\ParsingProjectRegion;
use app\models\enum\Region;
use app\models\enum\Status;
use app\models\reference\ParsingProject;
use app\models\reference\Schedule;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii;
use yii2fullcalendar\models\Event;

class ScheduleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'weekly',
            'add'
        ];
    }

    public function actionDelete($id = null, $requester_id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        /** @var Schedule[] $schedules */
        if ($id) {
            $schedules = [Schedule::findOne($id)];
        }
        else if ($requester_id) {
            $schedules = Schedule::find()->andWhere(['requester_id' => $requester_id])->all();
        }

        if ($schedules) {
            foreach ($schedules as $schedule) {
                if ($schedule) {
                    $schedule->delete();
                }
            }
        }

        return [
            'success'           => true,
            'id'                => $id,
            'requester_i'       => $requester_id,
        ];
    }


    public function actionParsing() {
        Yii::$app->assetManager->forceCopy = true;
        return $this->render('parsing',[
            'parsingProjects' => ParsingProject::find()
                ->alias('p')
                ->andWhere([
                    'p.status_id' => Status::STATUS_ACTIVE
                ])
                ->leftJoin(['s' => Schedule::tableName()],'s.requester_entity_id = '.Entity::ParsingProject.' AND s.requester_id = p.id')
                ->orderBy([
                    'p.name'  => SORT_ASC,
                ])
                ->groupBy('p.id')
                ->select(['p.*', 'count(s.id) schedules_count'])
                ->all()
        ]);
    }

    public function actionPropagateToWeek($id) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        /** @var Schedule $schedule */
        $schedule = Schedule::findOne($id);
        $attributes = $schedule->getAttributes([
            'time', 'duration', 'day', 'description', 'function', 'args', 'name', 'requester_id', 'requester_entity_id'
        ]);
        unset($attributes['id']);
        unset($attributes['create']);
        $result = [];
        foreach ([1,2,3,4,5,6,7] as $day) {
            if ($day != $attributes['day']) {
                $newSchedule = new Schedule();
                $newSchedule->setAttributes($attributes);
                $newSchedule->day = $day;
                $newSchedule->save();
                $result[] = $newSchedule;
            }
        }
        return $result;
    }

    public function actionUpdate($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        /** @var Schedule $schedule */
        $schedule = Schedule::findOne($id);

        $schedule->load(Yii::$app->request->post());

        $schedule->save();

        if (count($schedule->errors) > 0) {
            return [
                'success'   => false,
                'errors'    => $schedule->errors
            ];
        }
        return [
            'success'   => true,
            'id'        => $schedule->id
        ];
    }

    public function actionAdd()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        /** @var Schedule $schedule */
        $schedule = new Schedule();

        $schedule->load(Yii::$app->request->post());

        $schedule->save();

        if (count($schedule->errors) > 0) {
            return [
                'success'   => false,
                'errors'    => $schedule->errors
            ];
        }
        return [
            'success'   => true,
            'id'        => $schedule->id
        ];
    }

    public function actionWeekly($type, $start = 0, $end = 0)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $events = [];

        /** @var Schedule[] $schedule */
        $schedule = Schedule::find()
            ->andWhere([
                'requester_entity_id' => $type
            ])
            ->all();

        foreach ($schedule AS $item){
            $Event                      = new Event();
            $Event->id                  = $item->id;
            $Event->title               = (string)$item;
            $Event->editable            = true;
            $Event->startEditable       = true;
            $Event->durationEditable    = false;
            $Event->source           = $item->requester_id;
            //$Event->description = $item->name;
            $days               = $item->day -1 ;
            $time               = $item->time;
            $startTime          = strtotime('midnight sunday this week +'.$days.' days', intval($start, 10)) + strtotime("1970-01-01 $time UTC");
            $endTime            = $startTime + strtotime("1970-01-01 {$item->duration} UTC");
            $Event->start       = date('Y-m-d H:i:s', $startTime);
            $Event->end         = date('Y-m-d H:i:s', $endTime);
            $events[]           = $Event;
        }

        return $events;
    }
}