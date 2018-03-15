<?php
namespace backend\models;

use yii\db\ActiveRecord;

class EmailLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','type','sender','recipient','created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'E-mail type',
            'sender' => 'Sender e-mail',
            'recipient' => 'Recipient e-mail',
            'created_at' => 'Datetime',
        ];
    }
}