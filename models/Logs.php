<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property int $id
 * @property string $ip
 * @property string $ubicacion
 * @property string $accion
 * @property int $status
 * @property string $osver
 * @property string $useragent
 */
class Logs extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip', 'ubicacion', 'accion', 'status', 'osver', 'useragent'], 'required'],
            [['status'], 'integer'],
            [['ip', 'ubicacion', 'accion', 'osver'], 'string', 'max' => 120],
            [['useragent'], 'string', 'max' => 455],
            [['fecha_hora'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'Ip'),
            'ubicacion' => Yii::t('app', 'Ubicacion'),
            'accion' => Yii::t('app', 'Accion'),
            'status' => Yii::t('app', 'Status'),
            'osver' => Yii::t('app', 'Osver'),
            'useragent' => Yii::t('app', 'Useragent'),
            'fecha_hora' => Yii::t('app', 'Fecha y Hora'),
        ];
    }

}
