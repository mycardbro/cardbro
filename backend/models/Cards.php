<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "CARDS".
 *
 * @property integer $ID
 * @property integer $PRODUCTID
 * @property string $TOKEN
 * @property string $CREDIT
 * @property string $TERMINATION_REQUEST
 * @property string $TERMINATION_PAYMENT
 * @property string $ACTIVATION_DATE
 * @property string $TERMINATION_AMOUNT
 * @property string $BRANDNAME
 */
class Cards extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'CARDS_NEW';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['PRODUCTID', 'ID', 'ORDER_ID'], 'integer'],
            [['TOKEN'], 'string', 'max' => 9],
            [['CREATED_DATE', 'ACTIVATION_DATE', 'STATUS_NAME'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'ORDER_ID' => 'Order ID',
            'PRODUCTID' => 'Product ID',
            'TOKEN' => 'Token',
            'CREATED_DATE' => 'Created Date',
            'ACTIVATION_DATE' => 'Activation  Date',
            'STATUS_NAME' => 'Status',
        ];
    }
}
