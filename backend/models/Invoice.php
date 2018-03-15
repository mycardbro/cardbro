<?php
namespace backend\models;

use yii;
use yii\db\ActiveRecord;

class Invoice extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['product_id', 'user_id'], 'number'],
            [['bill_amount', 'paid_amount'], 'number'],
            [['created_at', 'updated_at',], 'safe'],
            [['id'], 'string', 'max' => 9],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_amount' => 'Billed amount',
            'paid_amount' => 'Paid amount',
            'user_id' => 'User ID',
            'product_id' => 'Product ID'
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getProduct() {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public static function createReplacementInvoice($amount, $productId) {

        $invoice = new Invoice();

        $invoice->user_id = Yii::$app->user->id;
        $invoice->bill_amount = $amount;
        $invoice->id = self::generateInvoiceId();
        $invoice->paid_amount = 0;
        $invoice->product_id = $productId;

        if ($invoice->save()) {
            return $invoice->id;
        }
        return false;
    }

    public static function generateInvoiceId($length = 8) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (Invoices::findOne(['invoice_id' => $randomString])) self::generateInvoiceId();

        return $randomString;
    }
}