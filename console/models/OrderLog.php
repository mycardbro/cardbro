<?php
namespace console\models;

use Yii;
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 5/17/17
 * Time: 7:42 PM
 */
class OrderLog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'ORDER_LOG';
    }
}