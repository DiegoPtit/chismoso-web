<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property int $rol_id
 * @property string $user
 * @property string $pwd
 * @property string $birthday
 * @property string|null $created_at
 * @property string $auth_key
 * @property int $subs_level
 *
 * @property Notificaciones[] $notificaciones
 * @property Posts[] $posts
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rol_id'], 'default', 'value' => 1316],
            [['subs_level'], 'default', 'value' => 4],
            [['rol_id', 'subs_level'], 'integer'],
            [['user', 'pwd', 'birthday', 'auth_key'], 'required'],
            [['birthday', 'created_at'], 'safe'],
            [['user', 'pwd'], 'string', 'max' => 180],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'rol_id' => Yii::t('app', 'Rol ID'),
            'user' => Yii::t('app', 'User'),
            'pwd' => Yii::t('app', 'Pwd'),
            'birthday' => Yii::t('app', 'Birthday'),
            'created_at' => Yii::t('app', 'Created At'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'subs_level' => Yii::t('app', 'Subs Level'),
        ];
    }

    /**
     * Gets query for [[Notificaciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificaciones()
    {
        return $this->hasMany(Notificaciones::class, ['receptor_id' => 'id']);
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Posts::class, ['usuario_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

}
