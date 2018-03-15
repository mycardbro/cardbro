<?php

namespace backend\models;

use Yii;
use backend\models\Orders;
use yii\db\Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "CUSTOMERS".
 *
 * @property integer $ID
 * @property string $TITLE
 * @property string $FIRSTNAME
 * @property string $LASTNAME
 * @property string $ADDRESS
 * @property string $CITY
 * @property string $POSTCODE
 * @property string $EMAIL
 * @property string $TELEPHONE
 * @property string $DOB
 * @property string $COUNTRY
 */
class Customers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'corporatecard'], 'integer'],
            [['sex',], 'string', 'max' => 6],
            [['comments',], 'string', 'max' => 1024],
            [['title', 'zipcode'], 'string', 'max' => 12],
            [['firstname','lastname','company','address','city','country','telephone',
                'email', 'nationality', 'ip', 'email'], 'string', 'max' => 64],
            [['dob','created_at','updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sex' => 'Sex',
            'title' => 'Title',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'company' => 'Company Name',
            'address' => 'Address',
            'zipcode' => 'Zipcode',
            'city' => 'City',
            'country' => 'Country',
            'telephone' => 'Telephone',
            'dob' => 'Date of birth',
            'email' => 'Email',
            'nationality' => 'Nationality',
            'ip' => 'IP Address',
            'corporatecard' => 'Corporate Card',
            'comments' => 'Comments',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
        ];
    }

    public function getOrders()
    {
        //return $this->hasOne(Orders::className(), ['customer_id' => 'id']);
        return $this->hasMany(Orders::className(), ['customer_id' => 'id']);

    }

    public function getCountries()
    {
        return $this->hasOne(Country::className(), ['CODE_ID' => 'country']);
    }

    public static function getCustomerId($record, $createNew = false)
    {
        $customer = Customers::findOne(['email' => $record['mail']]);

        if (empty($customer) && $createNew) {
            $customer = new Customers();

            $customer->sex = $record['sex'];
            $customer->title = $record['title'];
            $customer->firstname = self::normalize($record['firstname']);
            $customer->lastname = self::normalize($record['lastname']);
            $customer->address = self::normalize($record['address']);
            $customer->zipcode = self::normalize($record['zipcode']);
            $customer->city = self::normalize($record['city']);
            $customer->country = $record['country'];
            $customer->telephone = $record['telephone'];
            $customer->dob = $record['dob'];
            $customer->email = $record['mail'];
            $customer->nationality = $record['nationality'];
            $customer->ip = $record['ipaddress'];
            $customer->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $customer->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
        } else {
            $customer->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $customer->sex = $record['sex'];
            $customer->title = $record['title'];
            $customer->firstname = self::normalize($record['firstname']);
            $customer->lastname = self::normalize($record['lastname']);
            $customer->address = self::normalize($record['address']);
            $customer->zipcode = self::normalize($record['zipcode']);
            $customer->city = self::normalize($record['city']);
            $customer->country = $record['country'];
            $customer->telephone = $record['telephone'];
            $customer->dob = $record['dob'];
            $customer->nationality = $record['nationality'];
            $customer->ip = $record['ipaddress'];
        }
        
        $customer->save();

        return $customer->id;
    }

    public function getOrderInfo() {
        $addColumns = Orders::find()->where('customer_id = ' . $this->id)->all();
        $res = [
            'title',
            'firstname',
            'lastname',
            'address',
            'city',
            'zipcode',
            'email',
            'nationality',
            'ip',
            'telephone',
            'dob',
            [
                    'attribute' => 'country',
                    'label' => 'Country',
                    'value' => function($model) {
                        $countryName = ($model->countries->NAME) ?? '';
                        return $countryName;
                    }
            ],
            'comments',
        ];

        foreach ($addColumns as $column) {
            $cardName = $column->invoice->product->name ?? '';
            $productionDate = $column->creation_date;
            $shippingDate = date($column->creation_date, strtotime("+3 days"));
            $activationDate = $column->activation_date;
            $status = $column->status->name;
            $comment = $column->comment;

            $res[] = [
                'label' => '',
                'value' => '',
            ];
            $res[] = [
                'label' => 'Product Name',
                'value' => $cardName,
            ];
            $res[] = [
                'label' => 'Production Date',
                'value' => $productionDate,
            ];
            $res[] = [
                'label' => 'Shipping Date',
                'value' => $shippingDate,
            ];
            $res[] = [
                'label' => 'Activation Date',
                'value' => $activationDate,
            ];
            $res[] = [
                'label' => 'Status',
                'value' => $status,
            ];
            $res[] = [
                'label' => 'Card Comments',
                'value' => $comment,
            ];
            $res[] = [
                'label' => '<div style="background:#ffffff;color:#000000;float:left;" class="btn btn-success blue replace" href="../orders/close?id=' . $column->id . '">Close Card</div><div style="float:left" class="btn btn-success blue replace" href="../orders/replace?id=' . $column->id . '">Replace Card</div>',
                'value' => '',
            ];
        }

        return $res;
    }

    public function addDayswithdate($date,$days){

        $date = strtotime("+".$days." days", strtotime($date));
        return  date("Y-m-d", $date);

    }

    public static function normalize($word){
        $chars = [
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Ã' => 'A',
            'Ä' => 'Ae',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'Oe',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'Ue',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Þ' => 'B',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'ae',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'oe',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'ue',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
        ];

        $word = mb_convert_encoding($word, 'utf-8');
        $word = trim($word);
        $word = strtr($word, $chars);
        return $word;
    }
}
