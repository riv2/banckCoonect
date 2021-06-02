<?php

use app\components\Schema;
use app\components\Migration;
use app\components\base\Entity;

class m200810_102824_proxy_until_field extends Migration
{
    const ENTITY_ID = 87;
    private $_permissionsToAdd = [
        'app\models\register\Proxy.read' => 'Просмотр прокси',
    ];

    public function safeUp()
    {
        $this->addColumn('{{%proxy}}', 'until', $this->timestamp());
        $this->addColumn('{{%proxy}}', 'status_id', $this->integer(1)->defaultValue(\app\models\enum\Status::STATUS_ACTIVE));
        $this->addFK('{{%vpn}}', 'status_id', '{{%status}}', 'id');

        $this->insert('{{%entity}}', [
            'id' => self::ENTITY_ID,
            'name' => 'Proxy',
            'alias' => 'Proxy',
            'class_name' => 'app\models\register\Proxy',
            'action' => 'proxy',
            'entity_type' => 'register',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();
        Yii::$app->cache->delete('#prc_entity#');

        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsToAdd as $name => $description) {
            $permission = new \yii\rbac\Permission([
                'name' => $name,
                'description' => $description
            ]);
            $auth->add($permission);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsToAdd as $name => $description) {
            $permission = $auth->getPermission($name);
            if ($permission) {
                $auth->remove($permission);
            }
        }

        $this->delete('{{%entity}}', ['id' => self::ENTITY_ID]);
        Yii::$app->cache->delete('#prc_entity#');

        $this->dropColumn('{{%proxy}}', 'until');
        $this->dropColumn('{{%proxy}}', 'status_id');
    }
}
