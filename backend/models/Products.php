<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "PRODUCTS".
 *
 * @property integer $ID
 * @property string $PRICE
 * @property string $NAME
 * @property string $GPS_CRDPRODUCT
 * @property string $GPS_DESIGNREF
 * @property string $GPS_CURRCODE
 * @property string $GPS_AMTLOAD
 * @property string $GPS_IMAGEID
 * @property string $GPS_LIMITSGROUP
 * @property string $GPS_PERMSGROUP
 * @property string $GPS_FEESGROUP
 * @property string $GPS_CARRIERREF
 * @property string $GPS_ACTION
 * @property string $PARTNER_NAME
 * @property string $PARTNER_COMPANY
 * @property string $PARTNER_ADDRL1
 * @property string $PARTNER_ADDRL2
 * @property string $PARTNER_ADDRL3
 * @property string $PARTNER_VAT
 * @property string $URL_CARDIMAGE
 * @property string $URL_TOS
 * @property integer $ORDERS_ACCESS_LEVEL
 * @property string $STOCK_ID
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PRODUCTS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'ORDERS_ACCESS_LEVEL'], 'integer'],
            [['PRICE'], 'number'],
            [['NAME'], 'string', 'max' => 128],
            [['GPS_CRDPRODUCT', 'GPS_AMTLOAD', 'GPS_IMAGEID'], 'string', 'max' => 8],
            [['GPS_DESIGNREF'], 'string', 'max' => 16],
            [['GPS_CURRCODE'], 'string', 'max' => 3],
            [['GPS_LIMITSGROUP', 'GPS_PERMSGROUP', 'GPS_FEESGROUP', 'GPS_CARRIERREF', 'PARTNER_VAT'], 'string', 'max' => 32],
            [['GPS_ACTION'], 'string', 'max' => 1],
            [['PARTNER_NAME', 'PARTNER_COMPANY', 'PARTNER_ADDRL1', 'PARTNER_ADDRL2', 'PARTNER_ADDRL3', 'STOCK_ID',], 'string', 'max' => 64],
            [['URL_CARDIMAGE', 'URL_TOS'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'PRICE' => 'Price',
            'NAME' => 'Product Name',
            'GPS_CRDPRODUCT' => 'Crdproduct',
            'GPS_DESIGNREF' => 'Designref',
            'GPS_CURRCODE' => 'Currcode',
            'GPS_AMTLOAD' => 'Amtload',
            'GPS_IMAGEID' => 'Imageid',
            'GPS_LIMITSGROUP' => 'Limitsgroup',
            'GPS_PERMSGROUP' => 'Permsgroup',
            'GPS_FEESGROUP' => 'Feesgroup',
            'GPS_CARRIERREF' => 'Carrierref',
            'GPS_ACTION' => 'Action',
            'PARTNER_NAME' => 'Partner Name',
            'PARTNER_COMPANY' => 'Partner Company',
            'PARTNER_ADDRL1' => 'Partner Addrl1',
            'PARTNER_ADDRL2' => 'Partner Addrl2',
            'PARTNER_ADDRL3' => 'Partner Addrl3',
            'PARTNER_VAT' => 'Partner Vat',
            'PARTNER_EMAIL' => 'Invoice Email',
            'URL_CARDIMAGE' => 'Url Cardimage',
            'URL_TOS' => 'Url Tos',
            'ORDERS_ACCESS_LEVEL' => 'Orders Access Level',
            'STOCK_ID' => 'Stock ID',
        ];
    }

	public static function getName($id)
	{
		$name = self::find()->where(['ID' => $id])->one();
		if ($name) {
			return $name->NAME;
		}
		else false;
	}

	public static function getAvailableProducts($userId)
    {
        $role = AuthAssignment::find()->where(['user_id' => $userId])->one();
        $products = [];
        if ($role->item_name != 'partner') {
            $products = Products::find()->all();
        } else {
            $productRelations = UserProductRelations::find()->where(['user_id' => $userId])->all();
            foreach ($productRelations as $productRelation) {
                $products[] = Products::findOne($productRelation->PRODUCT_ID);
            }
        }

        return $products;
    }


}
