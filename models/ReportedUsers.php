<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reported_users".
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $at_time
 */
class ReportedUsers extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reported_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id'], 'required'],
            [['usuario_id'], 'integer'],
            [['reporter_id'], 'integer'],
            [['at_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'at_time' => Yii::t('app', 'At Time'),
        ];
    }

}
