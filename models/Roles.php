<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $rol_desc
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rol_desc'], 'required'],
            [['rol_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'rol_desc' => Yii::t('app', 'Descripción del Rol'),
        ];
    }
} 