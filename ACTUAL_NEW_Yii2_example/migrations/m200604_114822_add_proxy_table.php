<?php

use app\components\Migration;

class m200604_114822_add_proxy_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%proxy}}', [
            'id' => $this->string()->notNull(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp(),
            'created_user_id' => $this->integer(),
            'updated_user_id' => $this->integer(),
        ]);
        $this->addPk('{{%proxy}}', ['id']);

        $this->createTable('{{%parsing_project_proxy}}', [
            'id' => $this->uuidpk()->notNull(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'proxy_id' => $this->string()->notNull(),
            'created_at' => $this->timestamp(),
        ]);
        $this->addFK('{{%parsing_project_proxy}}', 'parsing_project_id', '{{%parsing_project}}', 'id');
        $this->addFK('{{%parsing_project_proxy}}', 'proxy_id', '{{%proxy}}', 'id');
        $this->db->createCommand("create unique index ci_prc_proxy_project_proxy_uindex on prc_proxy_project (parsing_project_id, proxy_id);");

        $this->createTable('{{%parsing_project_proxy_ban}}', [
            'id' => $this->uuid()->notNull(),
            'parsing_project_id' => $this->uuid()->notNull(),
            'proxy_id' => $this->string()->notNull(),
            'banned_at' => $this->dateTime()->notNull(),
        ]);
        $this->addPk('{{%parsing_project_proxy_ban}}', ['id']);
        $this->addFK('{{%parsing_project_proxy_ban}}', 'parsing_project_id', '{{%parsing_project}}', 'id');
        $this->addFK('{{%parsing_project_proxy_ban}}', 'proxy_id', '{{%proxy}}', 'id');

        $this->addColumn('{{%parsing_project}}', 'proxy_bantime', $this->integer()->defaultValue(60 * 24 * 3)); // 3 дня в минутах

        $proxiesToInsert = explode("\n", (new \yii\db\Query())
            ->select('string_agg(proxies, CHR(10))')
            ->from('{{%robot}}')
            ->scalar());

        $parsingProjectProxylist = (new \yii\db\Query())
            ->select(['id', 'proxies'])
            ->from('{{%parsing_project}}')
            ->andWhere([
                'status_id' => 0,
            ])
            ->andWhere("proxies IS NOT NULL AND trim(proxies) != ''")
            ->all();

        foreach ($parsingProjectProxylist as $parsingProject) {
            $proxiesToInsert = array_merge($proxiesToInsert, explode("\n", $parsingProject['proxies']));
        }
        $proxiesToInsert = array_map(function ($proxy) {return trim($proxy);}, $proxiesToInsert);
        $proxiesToInsert = array_unique(array_filter($proxiesToInsert));
        $proxiesToInsert = array_map(
            function($proxy) {
                return ['id' => trim($proxy)];
            },
            $proxiesToInsert
        );
        $this->db->createCommand()
            ->batchInsert('{{%proxy}}', ['id'], $proxiesToInsert)
            ->execute();

        $parsingProjectProxies = [];
        foreach ($parsingProjectProxylist as $parsingProject) {
            $proxies = array_unique(array_filter(explode("\n", $parsingProject['proxies'])));
            $parsingProjectId = $parsingProject['id'];
            foreach ($proxies as &$proxy) {
                $parsingProjectProxies[] = [
                    'id' => $this->db->createCommand("SELECT uuid_generate_v4();")->queryScalar(),
                    'proxy_id' => trim($proxy),
                    'parsing_project_id' => $parsingProjectId
                ];
            }
        }
        $this->db->createCommand()
            ->batchInsert('{{%parsing_project_proxy}}', ['id', 'proxy_id', 'parsing_project_id'], $parsingProjectProxies)
            ->execute();
    }

    public function safeDown()
    {
        $this->dropTable('{{%parsing_project_proxy_ban}}');
        $this->dropTable('{{%parsing_project_proxy}}');
        $this->dropTable('{{%proxy}}');
    }
}
