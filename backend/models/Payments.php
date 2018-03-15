<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "PAYMENTS".
 *
 * @property integer $ID
 * @property string $TOKEN
 * @property string $AMOUNT
 * @property string $VALUTA
 * @property string $USED
 * @property string $SETTLED
 */
class Payments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reminder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status_id',  'token', 'type_id'], 'integer'],
            [['bill_amount', 'paid_amount',], 'number'],
            [['created_at', 'updated_at', 'paid_at',], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'bill_amount' => 'Bill Amount',
            'paid_amount' => 'Paid Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'first_at' => 'First Reminder',
            'second_at' => 'Second Reminder',
            'collector_at' => 'Send to Collector',
            'paid_at' => 'Paid At',
            'status_id' => 'Status',
            'type_id' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    public function getOrder()
    {
        $query = $this->hasOne(Orders::className(), ['token' => 'token']);
        $query->andWhere(['not in', 'card.status_id', [4, 6]]);

        return $query;
    }

    public function getTypes()
    {
        return $this->hasOne(Types::className(), ['id' => 'type_id']);
    }

    public static function waitForCollector()
    {
        return self::find()->where(['status_id' => 15])->count();
    }
}
