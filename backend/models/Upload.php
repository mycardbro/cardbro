<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 04.02.18
 * Time: 17:15
 */

namespace backend\models;

use yii\db\ActiveRecord;

class Upload extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'first_name', 'brand_name', 'token'], 'string'],
            [['email', 'first_name', 'brand_name', 'token'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Status',
        ];
    }

    /*$data = [];
    $data['firstName'] = $order->customer->firstname;
                            $data['brandName'] = $invoice->product->name;
                            $this->mandrill(
                                'no-reply@cardcompact.uk',
                                $order->customer->email,
                                $data['firstName'] . ', Ihre ' . $data['brandName'] . ' Mastercard ist unterwegs - your ' . $data['brandName'] . ' Mastercard is coming soon',
                                $this->sendUploadLetter($data)
                            );*/

    /*$data = [];
$data['firstName'] = $order->customer->firstname;
$data['brandName'] = $order->invoice->product->name;
$data['token'] = $order->token;
$this->mandrill(
    'no-reply@cardcompact.uk',
    $order->customer->email,
    $data['firstName'] . ', deine ' . $data['brandName'] . ' Mastercard wurde produziert - your ' . $data['brandName'] . '  Mastercard was produced.',
    $this->sendDownloadLetter($data)
);*/
}