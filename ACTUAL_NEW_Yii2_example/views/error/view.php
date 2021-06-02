<?php
/**
 * @var \app\models\register\Error $model
 */

use app\components\base\Entity;
use app\models\enum\ErrorType; ?>
<p>
    <a href="/error" class="btn btn-primary">Назад</a>
</p>
<p>

</p>
<table class="table table-bordered table-primary table-hover table-striped">
    <thead>
        <tr>
            <th><?= $model->getAttributeLabel('message')?></th>
            <th><?= $model->message ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $model->getAttributeLabel('name')?></td>
            <td><?= $model->name ?></td>
        </tr>
        <tr>
            <td><?= $model->getAttributeLabel('created_at')?></td>
            <td><?= $model->created_at ?></td>
        </tr>
        <tr>
            <td><?= $model->getAttributeLabel('file')?></td>
            <td><?= $model->file ?></td>
        </tr>
        <tr>
            <td><?= $model->getAttributeLabel('line')?></td>
            <td><?= $model->line ?></td>
        </tr>
        <tr>
            <td><?= $model->getAttributeLabel('error_type_id')?></td>
            <td><?= ErrorType::getNameById($model->error_type_id) ?></td>
        </tr>
        <tr>
            <td colspan="2" ><pre  style="white-space: pre-wrap"><?= $model->backtrace ?></pre></td>
        </tr>
        <tr>
            <td><?= $model->getAttributeLabel('entity_type_id')?></td>
            <td><?= Entity::getNameById($model->entity_type_id) ?></td>
        </tr>
        <tr>
            <td><?= $model->getAttributeLabel('entity_row_id')?></td>
            <td><?= $model->entity_row_id ?></td>
        </tr>
        <tr>
            <td colspan="2"><pre style="white-space: pre-wrap"><?= $model->info ?></pre></td>
        </tr>
    </tbody>
</table>