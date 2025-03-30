<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $user
 * @property string $pwd
 * @property string $birthday
 * @property string|null $created_at
 * @property int|null $rol_id
 *
 * @property Notificaciones[] $notificaciones
 * @property Posts[] $posts
 * @property Roles $rol
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

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // No implementado para este ejemplo
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Genera auth_key antes de guardar
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            if ($this->isAttributeChanged('pwd')) {
                $this->pwd = Yii::$app->security->generatePasswordHash($this->pwd);
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user', 'pwd', 'birthday'], 'required'],
            [['pwd'], 'string'],
            [['birthday', 'created_at'], 'safe'],
            [['user'], 'string', 'max' => 180],
            [['auth_key'], 'string', 'max' => 32],
            [['rol_id'], 'integer'],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::class, 'targetAttribute' => ['rol_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'Usuario'),
            'pwd' => Yii::t('app', 'Contraseña'),
            'birthday' => Yii::t('app', 'Fecha de Nacimiento'),
            'created_at' => Yii::t('app', 'Created At'),
            'rol_id' => Yii::t('app', 'Rol'),
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
     * Gets query for [[Rol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Roles::class, ['id' => 'rol_id']);
    }

}
