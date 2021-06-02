<?php

use yii\db\Migration;

/**
 * Создание таблицы `user`.
 */
class m181101_134941_create_user_tables extends Migration
{
    private const USER_TABLE = 'user';

    private const TOKEN_TABLE = 'token';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::USER_TABLE, [
            'id' => $this->primaryKey(),
            'phone' => $this->bigInteger(12)->notNull()->unique()->comment('Номер телефона'),
            'first_name' => $this->string()->notNull()->comment('Имя'),
            'middle_name' => $this->string()->comment('Отчество'),
            'last_name' => $this->string()->notNull()->comment('Фамилия'),
            'email' => $this->string()->notNull()->comment('Почта'),
            'password_hash' => $this->string()->notNull()->comment('Хеш пароля'),
            'auth_key' => $this->string(32)->notNull()->comment('Ключ авторизации'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->notNull()->comment('Дата обновления'),
        ]);

        $this->createTable(self::TOKEN_TABLE, [
            'user_id' => $this->bigInteger()->notNull()->comment('Пользователь'),
            'code' => $this->string(128)->notNull()->comment('Токен'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'within' => $this->integer()->notNull()->defaultValue(0)->comment('Время действия'),
        ]);

        $this->addPrimaryKey('pk_token', self::TOKEN_TABLE, ['user_id', 'code']);
    //    $this->addForeignKey('user_id_token', self::TOKEN_TABLE, 'user_id', self::USER_TABLE, 'id', 'CASCADE', 'RESTRICT');
        $this->createIndex('ix_token_user_id', self::TOKEN_TABLE, 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::TOKEN_TABLE);
        $this->dropTable(self::USER_TABLE);
    }
}
