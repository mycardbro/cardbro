<?php
namespace backend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table `order`
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property integer $customer_id
 * @property integer $status_id
 * @property string $card_name
 * @property string $comment
 * @property string $pull_date
 * @property string $activation_date
 */
class Order extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    /*public function rules()
    {
        return [
            [['id', 'invoice_id', 'customer_id', 'status_id',], 'required'],
            [['id', 'customer_id', 'status_id'], 'integer'],
            [['creation_date', 'activation_date'], 'safe'],
            [['invoice_id'], 'string', 'max' => 8],
            [['card_name'], 'string', 'max' => 32],
            [['comment'], 'string', 'max' => 256],
        ];
    }*/

    /**
     * @inheritdoc
     */
    /*public function attributeLabels()
    {
        return [
            'id' => 'Pubtoken',
            'invoice_id' => 'Invoice ID',
            'customer_id' => 'Customer ID',
            'status_id' => 'Status ID',
            'card_name' => 'Card Name',
            'comment' => 'Comment',
            'creation_date' => 'Creation Date',
            'activation_date' => 'Activation Date',
        ];
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }
}