<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reported_posts".
 *
 * @property int $id
 * @property int $post_id
 * @property int $count
 * @property string $motivo
 */
class ReportedPosts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reported_posts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'count', 'motivo'], 'required'],
            [['post_id', 'count'], 'integer'],
            [['motivo'], 'string', 'max' => 480],
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
            'count' => Yii::t('app', 'Count'),
            'motivo' => Yii::t('app', 'Motivo'),
        ];
    }

}
