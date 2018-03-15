<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_config`.
 */
class m170221_074718_create_config_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%site_config}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(45)->notNull(),
            'value' => $this->text(),
            'name' => $this->string(),
            'description' => $this->text(),
        ], $tableOptions);

	    $this->batchInsert('{{%site_config}}', ['id', 'key', 'value', 'name', 'description'], [
	    	[1, 'site_name', 'CardCompact', 'Site Name', 'This site name will be shown on all site pages'],
		    [2, 'admin_email', 'sergey@outsoft.com', 'User\'s e-mail', 'General e-mail for administrative purposes (reports etc.)',],
	        [3, 'contact_email', 'sergey@outsoft.com', 'Contact e-mail', 'General e-mail for Contact page and to show on site',]
	    ]);
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%site_config}}');
    }
}
