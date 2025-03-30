<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banned_posts".
 *
 * @property int $id
 * @property int $post_id
 * @property string $motivo
 * @property string $at_time
 * @property Posts $post
 */
class BannedPosts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banned_posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'motivo', 'at_time'], 'required'],
            [['post_id'], 'integer'],
            [['at_time'], 'safe'],
            [['motivo'], 'string', 'max' => 255],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::class, 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'motivo' => Yii::t('app', 'Motivo'),
            'at_time' => Yii::t('app', 'At Time'),
        ];
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Posts::class, ['id' => 'post_id']);
    }

}
