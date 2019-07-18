<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property string $passwordHash
 * @property string $passwordResetToken
 * @property string $email
 * @property string $authKey
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @var UserStatus
     */
    private $statusObject = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['name', 'email', 'passwordHash', 'passwordResetToken'], 'string', 'max' => 255],
            ['authKey', 'string', 'max' => 32],
            ['email', 'email'],
            [['email', 'passwordResetToken'], 'unique'],
            ['status', 'default', 'value' => UserStatusInactive::CODE],
            ['status', 'in', 'range' => UserStatus::$allStatusCodes],
            ['status', 'setStatusObjValidator'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'name' => 'Имя',
            'authKey' => 'Ключ авторизации',
            'passwordHash' => 'Хэш для пароля',
            'passwordResetToken' => 'Токен для сброса пароля',
            'email' => 'Эл. почта',
            'status' => 'Статус',
            'createdAt' => 'Создан',
            'updatedAt' => 'Обновлён',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        $this->setStatus($this->status);
    }

    public function setStatusObjValidator($attribute)
    {
        if ($attribute != 'status')
            $this->addError('status', 'Валидатор setStatusObjValidator применим только к полю статуса!');
        else
            $this->setStatus($this->status);
    }

    /**
     * @param String|UserStatus $status
     * @return bool
     */
    public function setStatus($status)
    {
        return $this->getStatusObject()::setNewStatus($this, $status);
    }

    /**
     * @param UserStatus $status
     */
    public function setStatusObject(UserStatus $status)
    {
        $this->statusObject = $status;
    }

    /**
     * @return UserStatus|bool
     */
    public function getStatusObject()
    {
        if (!$this->statusObject) {
            if (empty($this->status) || !in_array($this->status, UserStatus::$allStatusCodes)) {
                $this->addError('status', 'Ошибка статуса!');
                return false;
            }

            $statusClassName = '\common\models\UserStatus' . $this->status;
            $this->setStatusObject(new $statusClassName);
        }

        return $this->statusObject;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => UserStatusActive::CODE]);
    }

    /**
     * Finds user by name
     *
     * @param string $name
     * @return static|null
     */
    public static function findByName($name)
    {
        return static::findOne(['name' => $name, 'status' => UserStatusActive::CODE]);
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
            'passwordResetToken' => $token,
            'status' => UserStatusActive::CODE,
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
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
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
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
}
