<?php
namespace backend\models;

use yii;
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
class Product extends \yii\db\ActiveRecord
{
    public $num_rows;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id'], 'integer'],
            [['price', 'num_rows'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['crdproduct', 'amtload', 'imageid'], 'string', 'max' => 8],
            [['designref'], 'string', 'max' => 16],
            [['currcode'], 'string', 'max' => 3],
            [['limitsgroup', 'permsgroup', 'feesgroup', 'carrierref', 'lang', 'create_type',
                'sms_required', 'mail_or_sms', 'delv_method'], 'string', 'max' => 32],
            [['action'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'price' => 'Price',
            'name' => 'Product Name',
            'crdproduct' => 'CardDesign',
            'designref' => 'Productref',
            'currcode' => 'CurCode',
            'amtload' => 'LoadValue',
            'imageid' => 'ImageID',
            'limitsgroup' => 'LimitsGroup',
            'permsgroup' => 'PERMSGroup',
            'feesgroup' => 'FeeGroup',
            'carrierref' => 'CarrierType',
            'action' => 'Action',
            'company_id' => 'Company ID',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
            'lang' => 'Lang',
            'create_type' => 'CreateType',
            'sms_required' => 'SMS_Required',
            'mail_or_sms' => 'MailOrSMS',
            'delv_method' => 'DelvMethod',
            'num_rows' => 'No of orders',
        ];
    }

    public function getCompany() {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public static function getName($id)
    {
        $name = self::find()->where(['id' => $id])->one();
        if ($name) {
            return $name->name;
        }
        else false;
    }

    public static function getAvailableProducts($userId)
    {
        $role = AuthAssignment::find()->where(['user_id' => $userId])->one();

        if ($role->item_name != 'partner') {
            return Product::find()->all();
        } else {
            $user = User::findOne($userId);

            return Product::find()->where(['company_id' => $user->company_id])->all();
        }
    }

    public static function getIdByName($name)
    {
        $model = self::find()->where(['name' => $name])->one();

        return $model->id ?? '';
    }
}