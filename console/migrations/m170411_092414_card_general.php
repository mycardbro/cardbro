<?php

use yii\db\Migration;

class m170411_092414_card_general extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
	    $tables = Yii::$app->db->schema->getTableNames();
	    $dbType = $this->db->driverName;
	    $tableOptions_mysql = "CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB";
	    $tableOptions_mssql = "";
	    $tableOptions_pgsql = "";
	    $tableOptions_sqlite = "";

	    /* MYSQL */
	    if (!in_array('CARD_STOCKS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%CARD_STOCKS}}', [
				    'STOCK_ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`STOCK_ID`)',
				    'AMOUNT' => 'INT(11) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('CLAIMS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%CLAIMS}}', [
				    'ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`ID`)',
				    'TOKEN' => 'CHAR(9) NOT NULL',
				    'DUEDATE' => 'DATE NOT NULL',
				    'TYPE' => 'VARCHAR(64) NOT NULL',
				    'DEBIT' => 'DECIMAL(5,2) NULL',
				    'CLEARED' => 'DATE NULL',
				    'DUNLEVEL' => 'INT(11) NULL',
				    'LEVELUPDATEDATE' => 'DATE NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('CARDS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%CARDS}}', [
				    'ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`ID`)',
				    'PRODUCTID' => 'INT(11) NULL',
				    'TOKEN' => 'CHAR(9) NOT NULL',
				    'CREDIT' => 'DECIMAL(8,2) NULL',
				    'TERMINATION_REQUEST' => 'DATE NULL',
				    'TERMINATION_PAYMENT' => 'DATE NULL',
				    'ACTIVATION_DATE' => 'VARCHAR(32) NULL',
				    'TERMINATION_AMOUNT' => 'DECIMAL(8,2) NULL',
				    'BRANDNAME' => 'VARCHAR(64) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('CUSTOMERS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%CUSTOMERS}}', [
				    'ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`ID`)',
				    'TITLE' => 'VARCHAR(12) NULL',
				    'FIRSTNAME' => 'VARCHAR(64) NULL',
				    'LASTNAME' => 'VARCHAR(64) NULL',
				    'ADDRESS' => 'VARCHAR(64) NULL',
				    'CITY' => 'VARCHAR(64) NULL',
				    'POSTCODE' => 'VARCHAR(12) NULL',
				    'EMAIL' => 'VARCHAR(64) NULL',
				    'TELEPHONE' => 'VARCHAR(32) NULL',
				    'DOB' => 'VARCHAR(32) NULL',
				    'COUNTRY' => 'VARCHAR(128) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('CUSTOMERS_PRODUCTS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%CUSTOMERS_has_PRODUCTS}}', [
				    'CUSTOMERS_ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`CUSTOMERS_ID`)',
				    'PRODUCTS_ID' => 'INT(11) NOT NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('INVOICES', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%INVOICES}}', [
				    'INVOICE_ID' => 'CHAR(9) NOT NULL',
				    0 => 'PRIMARY KEY (`INVOICE_ID`)',
				    'BILLAMOUNT' => 'DECIMAL(8,2) NULL',
				    'PAIDAMOUNT' => 'DECIMAL(8,2) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('MAIL_QUEUE', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%MAIL_QUEUE}}', [
				    'MAIL_ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`MAIL_ID`)',
				    'ENTRY_ID' => 'INT(11) NULL',
				    'PRESET' => 'VARCHAR(128) NULL',
				    'SEND_DATE' => 'DATE NULL',
				    'TABLE_NAME' => 'VARCHAR(128) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('ORDERS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%ORDERS}}', [
				    'ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`ID`)',
				    'CUSTOMERS_ID' => 'INT(11) NOT NULL',
				    'PRODUCT_ID' => 'INT(11) NOT NULL',
				    'INVOICE_ID' => 'CHAR(9) NOT NULL',
				    'SEX' => 'CHAR(1) NULL',
				    'TITLE' => 'VARCHAR(32) NULL',
				    'FIRSTNAME' => 'VARCHAR(32) NULL',
				    'LASTNAME' => 'VARCHAR(32) NULL',
				    'COMPANY' => 'VARCHAR(32) NULL',
				    'ADDRESS' => 'VARCHAR(64) NULL',
				    'ZIPCODE' => 'VARCHAR(16) NULL',
				    'CITY' => 'VARCHAR(32) NULL',
				    'COUNTRY_CODE' => 'INT(11) NULL',
				    'TELEPHONE' => 'VARCHAR(64) NULL',
				    'DOB' => 'VARCHAR(10) NULL',
				    'MAIL' => 'VARCHAR(255) NULL',
				    'REDIRECTED' => 'DATE NULL',
				    'TIME_STAMP' => 'DATE NULL',
				    'IP_STAMP' => 'VARCHAR(32) NULL',
				    'ACTIVATION_NUMBER' => 'VARCHAR(6) NULL',
				    'CARD_NAME' => 'VARCHAR(19) NULL',
				    'COMMENT' => 'VARCHAR(256) NULL',
				    'REQUESTED_AMTLOAD' => 'DECIMAL(8,2) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('PAYMENTS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%PAYMENTS}}', [
				    'ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`ID`)',
				    'TOKEN' => 'CHAR(9) NULL',
				    'AMOUNT' => 'DECIMAL(8,2) NULL',
				    'VALUTA' => 'DATE NULL',
				    'USED' => 'CHAR(1) NULL',
				    'SETTLED' => 'DATE NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('PRODUCTS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%PRODUCTS}}', [
				    'ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`ID`)',
				    'PRICE' => 'DECIMAL(6,2) NULL',
				    'NAME' => 'VARCHAR(128) NULL',
				    'GPS_CRDPRODUCT' => 'VARCHAR(8) NULL',
				    'GPS_DESIGNREF' => 'VARCHAR(16) NULL',
				    'GPS_CURRCODE' => 'VARCHAR(3) NULL',
				    'GPS_AMTLOAD' => 'VARCHAR(8) NULL',
				    'GPS_IMAGEID' => 'VARCHAR(8) NULL',
				    'GPS_LIMITSGROUP' => 'VARCHAR(32) NULL',
				    'GPS_PERMSGROUP' => 'VARCHAR(32) NULL',
				    'GPS_FEESGROUP' => 'VARCHAR(32) NULL',
				    'GPS_CARRIERREF' => 'VARCHAR(32) NULL',
				    'GPS_ACTION' => 'VARCHAR(1) NULL',
				    'PARTNER_NAME' => 'VARCHAR(64) NULL',
				    'PARTNER_COMPANY' => 'VARCHAR(64) NULL',
				    'PARTNER_ADDRL1' => 'VARCHAR(64) NULL',
				    'PARTNER_ADDRL2' => 'VARCHAR(64) NULL',
				    'PARTNER_ADDRL3' => 'VARCHAR(64) NULL',
				    'PARTNER_VAT' => 'VARCHAR(32) NULL',
				    'URL_CARDIMAGE' => 'VARCHAR(256) NULL',
				    'URL_TOS' => 'VARCHAR(256) NULL',
				    'ORDERS_ACCESS_LEVEL' => 'INT(11) NOT NULL',
				    'STOCK_ID' => 'VARCHAR(64) NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('USERS_PRODUCTS', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%USERS_PRODUCTS}}', [
				    'USER_ID' => 'INT(11) NOT NULL',
				    0 => 'PRIMARY KEY (`USER_ID`)',
				    'PRODUCT_ID' => 'INT(11) NOT NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('lang', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%lang}}', [
				    'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
				    0 => 'PRIMARY KEY (`id`)',
				    'url' => 'VARCHAR(255) NOT NULL',
				    'local' => 'VARCHAR(255) NOT NULL',
				    'name' => 'VARCHAR(255) NOT NULL',
				    'default' => 'SMALLINT(6) NOT NULL',
				    'date_update' => 'INT(11) NOT NULL',
				    'date_create' => 'INT(11) NOT NULL',
			    ], $tableOptions_mysql);
		    }
	    }

	    /* MYSQL */
	    if (!in_array('site_config', $tables))  {
		    if ($dbType == "mysql") {
			    $this->createTable('{{%site_config}}', [
				    'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
				    0 => 'PRIMARY KEY (`id`)',
				    'key' => 'VARCHAR(45) NOT NULL',
				    'value' => 'TEXT NULL',
				    'name' => 'VARCHAR(255) NULL',
				    'description' => 'TEXT NULL',
			    ], $tableOptions_mysql);
		    }
	    }


	    $this->createIndex('idx_USER_ID_7452_00','USERS_PRODUCTS','USER_ID',0);
	    $this->createIndex('idx_PRODUCT_ID_7452_01','USERS_PRODUCTS','PRODUCT_ID',0);
    }

    public function safeDown()
    {
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `CARD_STOCKS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `CLAIMS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `CUSTOMERS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `CUSTOMERS_has_PRODUCTS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `INVOICES`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `MAIL_QUEUE`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `ORDERS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `PAYMENTS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `PRODUCTS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `USERS_PRODUCTS`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `lang`');
	    $this->execute('SET foreign_key_checks = 1;');
	    $this->execute('SET foreign_key_checks = 0');
	    $this->execute('DROP TABLE IF EXISTS `site_config`');
	    $this->execute('SET foreign_key_checks = 1;');
    }
}
