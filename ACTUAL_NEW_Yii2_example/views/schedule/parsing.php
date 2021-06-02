<?php
use app\components\base\Entity;
use app\widgets\Schedule\ScheduleAsset;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @var \app\models\reference\ParsingProject[] $parsingProjects
 */

$this->title = "Расписание парсинга";
$this->params['breadcrumbs'] = [
    [
        'url' => Url::to(['/parsing-project']),
        'label' =>  'Парсинг'
    ],
    $this->title
];

ScheduleAsset::register($this);

?>
<div class="tooltip top tooltip-light" role="tooltip" id="event-tooltip">
    <div class="tooltip-arrow"></div>
    <div class="tooltip-inner">
        <i class="fa fa-remove tooltip-close"></i>
        <div class="event-tooltip-time"></div>
        <div class="event-tooltip-title"></div>
        <div class="text-right">
            <a class="btn btn-primary btn-xs event-tooltip-make-daily"><i class="fa fa-history"></i> всю неделю</a>
            <a class="btn btn-danger btn-xs event-tooltip-delete"><i class="fa fa-trash-o"></i> удалить</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-3 flex-height" style="overflow: auto;">
        <h2 style="margin-top: 5px;">Проекты</h2>
        <div class=" white-bg">
            &nbsp;
            <input type="checkbox" checked="checked" class="schedule-toggle-all  pull-right" style="margin: 4px 6px;font-size: 140%;" />
        </div>
        <ul class="schedule-actors">
            <?php foreach ($parsingProjects as $parsingProject) { ?>
                <li class=" white-bg">
                    <div class="schedule-actor"
                         data-duration="<?=$parsingProject->getScheduleDuration()?>"
                         data-color="<?=$parsingProject->getScheduleColor()?>"
                         data-requester_id="<?=$parsingProject->id?>"
                         data-description="<?=$parsingProject->getScheduleDescription()?>"
                         data-title="<?=$parsingProject->getScheduleTitle()?>"
                    >
                        <i class="fa <?=$parsingProject->getPPRegions() ? 'fa-plus-square' : 'fa-circle'?> schedule-actor-expand-collapse" style="color: <?=$parsingProject->getScheduleColor()?>;"></i>
                         <?=$parsingProject?>
                    </div>
                    <span class="schedule-actor-count"><?= ($count = $parsingProject->schedules_count) ? "($count)" : "" ?></span>
                    <span class="fa fa-calendar-times-o schedule-actor-clear"></span>
                    <input type="checkbox" checked="checked" class="schedule-toggle" data-requester_id="<?=$parsingProject->id?>" />
                    <?php

                    if ($parsingProject->getPPRegions()) { ?>
                        <ul>
                        <?php foreach ($parsingProject->getPPRegions() as $region) {
                            $params = ['region_id' => $region['region_id']];?>
                            <li>
                                <div class="schedule-actor ui-widget-content ui-draggable ui-draggable-handle"
                                     data-duration="<?=$parsingProject->getScheduleDuration($params)?>"
                                     data-color="<?=$parsingProject->getScheduleColor($params)?>"
                                     data-description="<?=$parsingProject->getScheduleDescription($params)?>"
                                     data-requester_id="<?=$parsingProject->id?>"
                                     data-args='<?=Json::encode($params)?>'
                                     data-title="<?=$parsingProject->getScheduleTitle($params)?>"
                                >
                                    <i class="fa fa-circle"  style="color: <?=$parsingProject->getScheduleColor($params)?>;"></i>
                                    <?=$region['name']?>
                                </div>
                            </li>
                        <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="col-xs-9 flex-height  white-bg">
        <?= yii2fullcalendar\yii2fullcalendar::widget([
            'id'      => 'calendar',
            'options' => [
                'lang'                      => 'ru',
                'data-requester_entity_id'  => Entity::ParsingProject,
                'data-type'                 => 'weekly'
            ],
            'clientOptions' => [
                'defaultDate'   => date('Y-m-d H:i:s', 0),
                'defaultView'   => 'agendaWeek',
                'columnFormat'  => 'dddd',
                'slotDuration'  => '00:15:00',
                'snapDuration'  => '00:05:00',
                'scrollTime'    => '16:00:00',
                'slotLabelFormat' => 'HH:mm',
                'allDaySlot'    => false,
                'editable'      => true,
                'startEditable'         => true,
                'eventDurationEditable' => false,
                'handleWindowResize'    => false,
                'eventTextColor'    => '#000000',
                'height'        => 'parent',
                'timezone'      => false,
                'titleFormat'   => 'Расписание запусков',
                'header'        => [
                    'center'    =>'title',
                    'left'      =>'prev next',
                    'right'     =>'agendaWeek agendaDay',
                ],

                'droppable'     => true,
                'drop'          => new \yii\web\JsExpression("scheduleCalendar.drop"),
                'events'        => new \yii\web\JsExpression("scheduleCalendar.events"),
                'eventClick'    => new \yii\web\JsExpression("scheduleCalendar.eventClick"),
                'eventDrop'     => new \yii\web\JsExpression("scheduleCalendar.eventDrop"),
                'viewRender'    => new \yii\web\JsExpression("scheduleCalendar.viewRender"),
            ],
        ]);
        ?>
    </div>
</div>

