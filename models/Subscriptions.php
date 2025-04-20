<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscriptions".
 *
 * @property int $id
 * @property string $subs_name
 * @property float $price
 */
class Subscriptions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subs_name', 'price'], 'required'],
            [['price'], 'number'],
            [['subs_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subs_name' => Yii::t('app', 'Subs Name'),
            'price' => Yii::t('app', 'Price'),
        ];
    }

}
