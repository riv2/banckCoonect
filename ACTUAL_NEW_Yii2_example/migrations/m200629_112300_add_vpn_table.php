<?php

use app\components\Migration;

class m200629_112300_add_vpn_table extends Migration
{
    const VPN_ENTITY_ID = 86;
    const SETTING_NAME = 'vpn_error_limit';

    private $_permissionsToAdd = [
        'app\models\reference\Vpn.read' => 'Просмотр VPN-сервера',
    ];

    public function safeUp()
    {
        $this->createTable('{{%vpn}}', [
            'id'       => $this->uuidpk()->notNull(),
            'name'     => $this->string(32)->null(),
            'provider' => $this->string(32)->notNull(),
            'username' => $this->string(32),
            'password' => $this->string(32),
            'config'   => $this->text()->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
            'status_id' => $this->integer(1)->defaultValue(\app\models\enum\Status::STATUS_ACTIVE),
        ]);
        $this->addPk('{{%vpn}}', ['id']);
        $this->addFK('{{%vpn}}', 'status_id', '{{%status}}', 'id');

        $this->insert('{{%entity}}', [
            'id' => self::VPN_ENTITY_ID,
            'name' => 'VPN',
            'alias' => 'Vpn',
            'class_name' => 'app\models\reference\Vpn',
            'action' => 'vpn',
            'entity_type' => 'reference',
            'parent_id' => null,
            'is_logging' => false,
            'is_enabled' => true
        ]);
        $this->db->createCommand()->resetSequence('{{%entity}}')->execute();
        Yii::$app->cache->delete('#prc_entity#');

        $this->insert('{{%setting}}', [
            'id' => static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
            'name' => self::SETTING_NAME,
            'data' => '10',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        Yii::$app->cache->delete('#prc_setting#');

        $this->createTable('{{%vpn_competitor}}', [
            'id'               => $this->uuidpk()->notNull(),
            'vpn_id'           => $this->uuid()->notNull(),
            'competitor_id'    => $this->uuid()->notNull(),
            'parsing_id'       => $this->uuid()->notNull(),
            'created_at'       => $this->timestamp()->notNull(),
        ]);
        $this->addFK('{{%vpn_competitor}}', 'vpn_id', '{{%vpn}}', 'id');
        $this->addFK('{{%vpn_competitor}}', 'competitor_id', '{{%competitor}}', 'id');
        $this->addFK('{{%vpn_competitor}}', 'parsing_id', '{{%parsing}}', 'id');

        $projectVpns = (new \yii\db\Query())
            ->select([
                'provider' => 'max(vpn_provider)',
                'username' => 'max(vpn_username)',
                'password' => 'max(vpn_password)',
                'config'   => 'max(vpn_config)',
            ])
            ->from('{{%parsing_project}}')
            ->andWhere('vpn_config IS NOT NULL')
            ->andWhere('vpn_provider IS NOT NULL')
            ->andWhere('vpn_username IS NOT NULL')
            ->andWhere('vpn_password IS NOT NULL')
            ->andWhere(['!=', 'vpn_config', ''])
            ->andWhere(['!=', 'vpn_provider', ''])
            ->andWhere(['!=', 'vpn_username', ''])
            ->andWhere(['!=', 'vpn_password', ''])
            ->groupBy(new \yii\db\Expression("regexp_replace(vpn_config, E'[\\n\\r\\s]+', ' ', 'g')"))
            ->all();
        if (count($projectVpns) > 0) {
            $this->db->createCommand()
                ->batchInsert('{{%vpn}}', array_keys($projectVpns[0]), $projectVpns)
                ->execute();
        }

        $this->dropColumn('{{%parsing_project}}', 'vpn_provider');
        $this->dropColumn('{{%parsing_project}}', 'vpn_username');
        $this->dropColumn('{{%parsing_project}}', 'vpn_password');
        $this->dropColumn('{{%parsing_project}}', 'vpn_config');

        $this->addColumn('{{%parsing_project}}', 'vpn_type', $this->string());
        $this->addColumn('{{%parsing_project}}', 'vpns', $this->json()->defaultValue(new \yii\db\Expression("'[]'::jsonb")));

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

        $this->dropColumn('{{%parsing_project}}', 'vpn_type');
        $this->dropColumn('{{%parsing_project}}', 'vpns');

        $this->addColumn('{{%parsing_project}}', 'vpn_provider', $this->string());
        $this->addColumn('{{%parsing_project}}', 'vpn_username', $this->string());
        $this->addColumn('{{%parsing_project}}', 'vpn_password', $this->string());
        $this->addColumn('{{%parsing_project}}', 'vpn_config', $this->string());

        $this->dropTable('{{%vpn_competitor}}');
        $this->dropTable('{{%vpn}}');
        $this->delete('{{%setting}}', ['name' => self::SETTING_NAME]);
        $this->delete('{{%entity}}', ['id' => self::VPN_ENTITY_ID]);
        Yii::$app->cache->delete('#prc_setting#');
        Yii::$app->cache->delete('#prc_entity#');
    }
}
