<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 08.01.18
 * Time: 14:29
 */

namespace backend\models;

use yii\db\ActiveRecord;

class ActionType extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id',], 'integer'],
            [['name',], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Action Type',
        ];
    }
}