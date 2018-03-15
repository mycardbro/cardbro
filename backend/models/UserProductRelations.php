<?php
namespace backend\models;


class UserProductRelations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'USERS_PRODUCTS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'USER_ID', 'PRODUCT_ID',], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'USER_ID' => 'User ID',
            'PRODUCT_ID' => 'Product ID',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['ID' => 'USER_ID']);
    }

    public function getProduct() {
        return $this->hasOne(Products::className(), ['ID' => 'PRODUCT_ID']);
    }
}