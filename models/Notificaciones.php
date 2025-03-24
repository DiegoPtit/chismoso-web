<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notificaciones".
 *
 * @property int $id
 * @property int $receptor_id
 * @property int $post_original_id
 * @property int $comentario_id
 * @property string|null $created_at
 *
 * @property Posts $comentario
 * @property Posts $postOriginal
 * @property Usuarios $receptor
 */
class Notificaciones extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receptor_id', 'post_original_id', 'comentario_id'], 'required'],
            [['receptor_id', 'post_original_id', 'comentario_id'], 'integer'],
            [['created_at'], 'safe'],
            [['receptor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::class, 'targetAttribute' => ['receptor_id' => 'id']],
            [['post_original_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::class, 'targetAttribute' => ['post_original_id' => 'id']],
            [['comentario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::class, 'targetAttribute' => ['comentario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'receptor_id' => Yii::t('app', 'Receptor ID'),
            'post_original_id' => Yii::t('app', 'Post Original ID'),
            'comentario_id' => Yii::t('app', 'Comentario ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Comentario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentario()
    {
        return $this->hasOne(Posts::class, ['id' => 'comentario_id']);
    }

    /**
     * Gets query for [[PostOriginal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPostOriginal()
    {
        return $this->hasOne(Posts::class, ['id' => 'post_original_id']);
    }

    /**
     * Gets query for [[Receptor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceptor()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'receptor_id']);
    }

}
