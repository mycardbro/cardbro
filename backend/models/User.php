<?php
namespace backend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $role write-only role
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    public $role;
    public $password;
    public $password_confirm;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key',
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],
	        'timestamp' => [
			    'class' => 'yii\behaviors\TimestampBehavior',
			    'attributes' => [
				    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
				    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
			    ],
		    ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'string', 'max' => 255],
            [['company_id', 'pagination'], 'integer'],
            /*['company', 'string', 'max' => 255],
            ['address', 'string', 'max' => 255],
            ['postal_code', 'string', 'max' => 15],
            ['country', 'string', 'max' => 63],
            ['city', 'string', 'max' => 63],
            ['region', 'string', 'max' => 63],
            ['vat_id', 'string', 'max' => 31],
            ['vat_perc', 'double'],*/
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\backend\models\User', 'message' => 'This email address has already been taken.'],

            /*['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6, 'message' => 'password should be at least 6 symbols'],
            ['password_confirm', 'required', 'when' => function($model) {
                return !empty($model->password);
            }],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],*/

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }
	public function attributeLabels()
	{
		return [
			'created_at' => 'Created on',
			'updated_at' => 'Updated on',
            /*'vat_id'     => 'VAT ID',
            'vat_perc'   => 'VAT Percentage',*/
		];
	}
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * returns status sting for views by code
    */
    public static function getStatus($status){
        switch ($status){
            case(0): return 'Deleted';
            case(1): return 'Active';
            case(2): return 'Inactive';
            default: return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public static function getRole($id){
	    $role = \Yii::$app->authManager->getRolesByUser($id);
	    foreach ($role as $key=>$values){
		    $role = $key;
	    }
	    return $role;
    }
    public static function getRoleName($id){
	    $role = \Yii::$app->authManager->getRolesByUser($id);
	    foreach ($role as $key=>$values){
		    $role = $values->description;
	    }
	    return $role;
    }
    
    public function getRoles() {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public function getOrders() {
        return $this->hasMany(Invoices::className(), ['USER_ID' => 'ID']);
    }

    public function getCompany() {
            return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public function uniqueEmail($email)
    {
        $emailExist = User::find()->where(['email' => $email])->one();

        if ($emailExist) $this->addError('email','Email already exists');
    }
}
