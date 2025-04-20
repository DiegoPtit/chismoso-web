<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_suscriptions".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $suscription_id
 * @property string $fecha_inicio
 * @property string $fecha_fin
 * @property int $activo
 */
class UsersSuscriptions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_suscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'suscription_id', 'fecha_inicio', 'fecha_fin', 'activo'], 'required'],
            [['usuario_id', 'suscription_id', 'activo'], 'integer'],
            [['fecha_inicio', 'fecha_fin'], 'safe'],
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
            'suscription_id' => Yii::t('app', 'Suscription ID'),
            'fecha_inicio' => Yii::t('app', 'Fecha Inicio'),
            'fecha_fin' => Yii::t('app', 'Fecha Fin'),
            'activo' => Yii::t('app', 'Activo'),
        ];
    }

}
