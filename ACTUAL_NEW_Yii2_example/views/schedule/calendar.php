<?php
use app\components\base\ScheduleTrait;
use app\widgets\Schedule\ScheduleAsset;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @var ScheduleTrait[] $items
 * @var int $entityId
 */

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
    <div class="col-xs-3" style="overflow: auto;">
        <h2 style="margin-top: 5px;">Проекты</h2>
        <div class=" white-bg">
            &nbsp;
            <input type="checkbox" checked="checked" class="schedule-toggle-all  pull-right" style="margin: 4px 6px;font-size: 140%;" />
        </div>
        <ul class="schedule-actors">
            <?php foreach ($items as $item) { ?>
                <li class=" white-bg">
                    <div class="schedule-actor"
                         data-duration="<?=$item->getScheduleDuration()?>"
                         data-color="<?=$item->getScheduleColor()?>"
                         data-requester_id="<?=$item->id?>"
                         data-description="<?=$item->getScheduleDescription()?>"
                         data-title="<?=$item->getScheduleTitle()?>"
                    >
                         <?=$item->getScheduleTitle()?>
                    </div>
                    <span class="schedule-actor-count"><?= ($count = $item->getSchedule()->count()) ? "($count)" : "" ?></span>
                    <span class="fa fa-calendar-times-o schedule-actor-clear"></span>
                    <input type="checkbox" checked="checked" class="schedule-toggle" data-requester_id="<?=$item->id?>" />
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="col-xs-9 white-bg">
        <?= yii2fullcalendar\yii2fullcalendar::widget([
            'id'      => 'calendar',
            'options' => [
                'lang'                      => 'ru',
                'data-requester_entity_id'  => $entityId,
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
                'titleFormat'   => 'Расписание',
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

