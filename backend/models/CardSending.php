<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 05.02.18
 * Time: 21:26
 */

namespace backend\models;

use yii\db\ActiveRecord;

class CardSending extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'card_sending';
    }
}