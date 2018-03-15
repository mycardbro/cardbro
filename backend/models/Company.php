<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Company extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','name','email','vat','vat_id','address','postal_code','country','city','region'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'E-mail',
            'name' => 'Company name',
            'vat' => 'VAT, %',
            'vat_id' => 'VAT Reg No',
            'address' => 'Address',
            'postal_code' => 'Postal Code',
            'country' => 'Country',
            'city' => 'City',
            'region' => 'Region',
        ];
    }
}