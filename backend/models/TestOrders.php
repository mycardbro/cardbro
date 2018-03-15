<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "Orders".
 *
 * @property integer $ID
 * @property integer $CUSTOMERS_ID
 * @property integer $PRODUCT_ID
 * @property string $INVOICE_ID
 * @property string $SEX
 * @property string $TITLE
 * @property string $FIRSTNAME
 * @property string $LASTNAME
 * @property string $COMPANY
 * @property string $ADDRESS
 * @property string $ZIPCODE
 * @property string $CITY
 * @property integer $COUNTRY_CODE
 * @property string $TELEPHONE
 * @property string $DOB
 * @property string $MAIL
 * @property string $REDIRECTED
 * @property string $TIME_STAMP
 * @property string $IP_STAMP
 * @property string $ACTIVATION_NUMBER
 * @property string $CARD_NAME
 * @property string $COMMENT
 * @property string $REQUESTED_AMTLOAD
 * @property string $STATUS_NAME
 * @property string $OWNER_ID
 */
class TestOrders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'token', 'customer_id', 'status_id',], 'integer'],
            [['order_date', 'pull_date', 'creation_date', 'activation_date', 'replacement_id',], 'safe'],
            [['invoice_id', 'card_name', 'comment', 'recid', 'comment'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recid' => 'Rec ID',
            'token' => 'Token',
            'invoice_id' => 'Invoice ID',
            'customer_id' => 'Customer ID',
            'status_id' => 'Status ID',
            'card_name' => 'Card Name',
            'comment' => 'Card Comments',
            'order_date' => 'Order Date',
            'pull_date' => 'Pull Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'replacement_id' => 'Replacement ID',
            'activation_date' => 'Activation Date'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'customer_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['code' => 'country_code']);
    }

    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    public function getOrderQuantityByToken()
    {
        return Orders::find()->where(['token' => $this->token])->count();
    }

    public static function getOrderNumberByInvoiceId($invoiceId)
    {
        return self::find()->where(['invoice_id' => $invoiceId])->count();
    }
}
