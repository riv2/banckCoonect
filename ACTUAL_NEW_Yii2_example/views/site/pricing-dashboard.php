<?php

/**
 * @var $this       netis\crud\web\View
 * @var Robot[]     $robotsIds
 * @var Parsing[]   $activeParsings
 */

use app\models\reference\Robot;
use app\models\register\Parsing;
use yii\helpers\Url;

\app\assets\ParsingDashboardAsset::register($this);

?>

    <table class="table">
        <thead>
        <tr>
            <th>
                Сервер очередей
            </th>
            <th>
                База данных
            </th>
            <th>
                Вебсервер
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <a href="http://<?=Yii::$app->params['amqp']['host']?>:15672/#/queues">http://<?=Yii::$app->params['amqp']['host']?>:15672/#/queues</a>
            </td>
            <td>
                <?=Yii::$app->params['db']['dsn']?>
            </td>
            <td>
                <a href="http://pricing.vseinstrumenti.ru/">http://pricing.vseinstrumenti.ru/</a>
            </td>
        </tr>
        </tbody>
    </table>
<?php

$droidsTabContent = <<<HTML

<div class="clearfix">
    <textarea id="swarms" style="display: none;">$swarms</textarea>
</div>
<table class="table">
    <thead>
        <tr>
            <td>Дроид</td>
            <td>Дата и время старта</td>
            <td>Название</td>
            <td>Всего</td>
            <td>Очередь</td>
            <td>Обраб</td>
            <td>Стр.</td>
            <td>Товар</td>
            <td>Налич.</td>
            <td>Ошибок</td>
            <td>Подключение</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <input type="text" id="parsingNameFilter" name="parsing-name" class="form-control" placeholder="Название">
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr id="droid-template"  style="display: none;">
            <td class="d-ip" width="1" style="white-space: nowrap;">

            </td>
            <td class="created_at"></td>
            <td width="50%">
                <div style="overflow: hidden;">
                    <div class="pull-right" style="font-weight: normal;">
                        <span class="d-usage"></span> | <span class="d-status"></span> |  <a data-href="#" class="d-stop">Стоп</a> <a data-href="#" class="d-cancel">Отменить</a>
                    </div>
                    <span class="d-parsingName"></span>
                    <span class="d-proxyName" style="width: 500px;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
display: inline-block;
height: 15px;"></span>
                </div>
                <div class="d-parsingId"></div>
                <div class="d-progress progress" style="padding: 0px; margin: 0;">
                    <div class="progress-bar progress-bar-striped active progress-bar-primary"
                         role="progressbar"
                         style="width:0%;">
                        <span class="d-lastParsedItem" ></span>
                    </div>
                </div>
            </td>
            <td class="d-i global_count"></td>
            <td class="d-i d-tasksInQueue"></td>
            <td class="d-i d-tasksProcessed"></td>
            <td class="d-i d-parsedChunksCount"></td>
            <td class="d-i "><a data-href="/crud-price-parsed?PriceParsed[parsing_project_id]={ppi}&PriceParsed[parsing_id]={pi}"  class="d-parsedItemsCount"></a></td>
            <td class="d-i" ><a data-href="/crud-price-parsed?PriceParsed[parsing_project_id]={ppi}&PriceParsed[parsing_id]={pi}&PriceParsed[out_of_stock]=0"  class="d-parsedStockCount"></a></td>
            <td class="d-i" ><a data-href="/crud-parsing-error?PriceParsed[parsing_project_id]={ppi}&ParsingError[parsing_id]={pi}"  class="d-taskErrorsCount"></a></td>
            <td class="d-i d-proxiesCount" style="white-space: nowrap">
                <span class="d-proxyValidCount">0</span> /
                <span class="d-proxyCheckedCount">0</span> /
                <span class="d-proxyTotalCount">0</span>
            </td>
            <td class="d-vpnName" style="display: none;word-break: break-word;max-width: 100px;"></td>
        </tr>
    </thead>
    <tbody id="droids" style="">

    </tbody>
</table>

HTML;

$parsingsTabContent = '';
$endTimes = Yii::$app->cache->get('active_parsings_data_times');
$endTime = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d 22:00:00'))->getTimestamp();

foreach ($activeParsings as $parsing) {
    $isLate = !isset($endTimes[$parsing['id']]) || $endTimes[$parsing['id']] > $endTime;
    $parsingsTabContent .= "
		<tr " . ($isLate ? "style='background-color: #ff000033'" : '') . ">
			<td><a href=\"/parsing-project/update?id=" . $parsing['parsing_project_id'] . "\" target=\"_blank\">" . $parsing['name'] . "</a>" . (isset($endTimes[$parsing['id']]) ? ('( до: ' . date('Y-m-d H:i:s', $endTimes[$parsing['id']]) . ')') : '') . "</td>
            <td class=\"d-i\">" . $parsing['global_count'] . "</td>
            <td class=\"d-i\">" . $parsing['page_count'] . "</td>
			<td class=\"d-i\"><a href=\"/crud-price-parsed?PriceParsed[parsing_project_id]=" . $parsing['parsing_project_id'] . "&PriceParsed[parsing_id]=" . $parsing['id'] . "\">" . $parsing['item_count'] . "</a></td>
            <td class=\"d-i\"><a href=\"/crud-price-parsed?PriceParsed[parsing_project_id]=" . $parsing['parsing_project_id'] . "&PriceParsed[parsing_id]=" . $parsing['id'] . "&PriceParsed[out_of_stock]=0\">" . $parsing['in_stock_count'] . "</a></td>
            <td class=\"d-i\"><a href=\"/crud-parsing-error?PriceParsed[parsing_project_id]=" . $parsing['parsing_project_id'] . "&ParsingError[parsing_id]=" . $parsing['id'] . "\">" . $parsing['errors_count'] . "</a></td>
		</tr>
	";
}

$parsingsTabContent = <<<HTML

<table class="table">
    <thead>
        <tr>
            <td>Наименование</td>
            <td>Всего урлов</td>
            <td>Страниц</td>
            <td>Товаров</td>
            <td>В наличии</td>
            <td>Ошибок</td>
        </tr>
    </thead>
    <tbody id="parsing-tab-body">
        $parsingsTabContent
    </tbody>
</table>

HTML;


echo yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => 'Дроиды',
            'content' => $droidsTabContent,
            'active' => true,
        ],
        [
            'label' => 'Парсинги',
            'content' => $parsingsTabContent,
        ],
    ]
]);