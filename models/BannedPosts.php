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
            [['post_id', 'motivo'], 'required'],
            [['post_id'], 'integer'],
            [['at_time'], 'safe'],
            [['motivo'], 'string', 'max' => 255],
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

}
