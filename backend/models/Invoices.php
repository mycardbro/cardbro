<?php

namespace backend\models;

use Yii;
use backend\models\User;
/**
 * This is the model class for table "INVOICES".
 *
 * @property integer $ID
 * @property string $INVOICE_ID
 * @property string $BILLAMOUNT
 * @property string $PAIDAMOUNT
 */
class Invoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'INVOICES';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['INVOICE_ID'], 'required'],
            [['BILLAMOUNT', 'PAIDAMOUNT'], 'number'],
            [['INVOICE_ID'], 'string', 'max' => 9],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'INVOICE_ID' => 'Invoice ID',
            'BILLAMOUNT' => 'Billed amount',
            'PAIDAMOUNT' => 'Paid amount',
            'USER_ID' => 'User ID',
            'PRODUCT_ID' => 'Product ID'
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['ID' => 'USER_ID']);
    }

    public function getProduct() {
        return $this->hasOne(Products::className(), ['ID' => 'PRODUCT_ID']);
    }

    public static function createReplacementInvoice($amount) {

        $invoice = new Invoices();

        $invoice->USER_ID = Yii::$app->user->id;
        $invoice->BILLAMOUNT = $amount;
        $invoice->INVOICE_ID = self::generateInvoiceId();
        $invoice->PAIDAMOUNT = 0;

        if ($invoice->save()) {
            return $invoice->INVOICE_ID;
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
        if (Invoices::findOne(['INVOICE_ID' => $randomString])) self::generateInvoiceId();

        return $randomString;
    }
}
