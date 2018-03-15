<?php

namespace backend\models;
/**
 * This is the model class for table "INVOICES".
 *
 * @property integer $ID
 * @property string $INVOICE_ID
 * @property string $BILLAMOUNT
 * @property string $PAIDAMOUNT
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'COUNTRY';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['NAME', 'CODE_ID', 'PRIORITY'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CODE_ID' => 'Country Code',
            'NAME' => 'Country Name',
            'PRIORITY' => 'Priority',
        ];
    }

    public static function getAll()
    {
        return self::find()->orderBy([
            'PRIORITY' => SORT_DESC,
            'NAME' => SORT_ASC,
        ])->all();
    }
}
