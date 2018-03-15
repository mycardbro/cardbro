<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "site_config".
 *
 * @property string $id
 * @property string $key
 * @property string $value
 * @property string $name
 * @property string $description
 */
class SiteConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'site_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value', 'description'], 'string'],
            [['key'], 'string', 'max' => 45],
            [['name'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * general method for receiving site settings values
     * @param $key
     * @return string
     */
    public static function option($key)
    {
        $option = SiteConfig::findOne([
            'key' => $key,
        ]);
        if (!$option){
        	return false;
        }
        return $option->value;
    }
}
