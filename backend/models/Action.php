<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 08.01.18
 * Time: 14:28
 */

namespace backend\models;

use yii\db\ActiveRecord;

class Action extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id','ip','type_id','product_id','amount', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'ip' => 'IP',
            'type_id' => 'Type',
            'product_id' => 'Product',
            'amount' => 'Amount',
            'created_at' => 'Created At',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getType() {
        return $this->hasOne(ActionType::className(), ['id' => 'type_id']);
    }

    public function getProduct() {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}