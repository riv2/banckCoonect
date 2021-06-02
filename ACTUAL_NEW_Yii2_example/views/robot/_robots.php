<?php foreach ($robots as $robot): ?>
    <div class="robot robot-widget robot-stopped box" data-id="<?=$robot->id?>">
        <div class="box-header with-border">
            <div class="box-title">
                <i class="fa fa-android"></i> <?=$robot->name?> <small><?=$robot->id?></small>
            </div>
            <div class="box-tools pull-right">
                <a href="#" class="robot-start"><i class="fa fa-play"></i></a>
                <a href="#" class="robot-kill"><i class="fa fa-stop"></i></a>
                <a href="#" class="robot-restart"><i class="fa fa-refresh"></i></a>
                <a href="#" class="robot-update"><i class="fa fa-cloud-download"></i></a>
                <a href="/robot/update?id=<?=$robot->id?>"><i class="fa fa-wrench"></i></a>
            </div>
        </div>
        <table class="table box-body robot-parsings" style="display: none;">
            <thead>
            <tr>
                <th>Парсинг</th>
                <th>Статус</th>
                <th>П</th>
                <th>Соед.</th>
                <th>Спарс.</th>
                <th>Налич.</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
<?php endforeach; ?>