<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 04.03.18
 * Time: 15:38
 */

namespace backend\models;

use yii\db\ActiveRecord;

class Replenishment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'replenishment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','token','replenishment_at','amount','created_at'], 'safe'],
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
            'replenishment_at' => 'Replenishment Date',
            'amount' => 'Amount',
            'created_at' => 'Created At',
        ];
    }

    public function getOrder()
    {
        $query = $this->hasOne(Orders::className(), ['token' => 'token']);
        $query->andWhere(['not in', 'card.status_id', [4, 6]]);

        return $query;
    }
}