<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Customer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'E-mail',
            'pubtoken' => 'Public token',
        ];
    }
}