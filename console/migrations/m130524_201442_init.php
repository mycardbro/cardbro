<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%USERS}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

	    $this->insert('{{%USERS}}', [
		    'id' => '1',
		    'username' => 'Administrator',
		    'auth_key' => '_dUIjsVnWnbij3fBVEkrQeqme1BvuY3D',
		    'password_hash' => '$2y$13$D7HuHhDG0KL7//BfeyB/p.9pyfz4HUZ05.ejU043SytnVlzjMF9c.', //outsoft_rulez
		    'password_reset_token' => null,
		    'email' => 'sergey@outsoft.com',
		    'status' => 1,
		    'created_at' => time(),
		    'updated_at' => time(),
	    ]);
    }

    public function down()
    {
        $this->dropTable('{{%USERS}}');
    }
}
