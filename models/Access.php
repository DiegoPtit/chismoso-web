<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access".
 *
 * @property int $id
 * @property int $subs_id
 * @property string|null $accesos
 */
class Access extends \yii\db\ActiveRecord
{
    // Propiedades para mantener los valores decodificados
    private $_accesosArray = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accesos'], 'default', 'value' => null],
            [['subs_id'], 'required'],
            [['subs_id'], 'integer'],
            [['accesos'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subs_id' => Yii::t('app', 'Subs ID'),
            'accesos' => Yii::t('app', 'Accesos'),
        ];
    }

    /**
     * Obtiene los accesos como array
     * @return array|null
     */
    public function getAccesosArray()
    {
        if ($this->_accesosArray === null && $this->accesos !== null) {
            // Si ya es un array, usarlo directamente
            if (is_array($this->accesos)) {
                $this->_accesosArray = $this->accesos;
                return $this->_accesosArray;
            }
            
            // Intento 1: Decodificación directa
            $this->_accesosArray = json_decode($this->accesos, true);
            
            // Intento 2: Si falla, limpiar caracteres de escape
            if ($this->_accesosArray === null || !is_array($this->_accesosArray)) {
                $accesosFix = str_replace('\"', '"', $this->accesos);
                $this->_accesosArray = json_decode($accesosFix, true);
            }
            
            // Intento 3: Usar expresión regular para extraer valores
            if ($this->_accesosArray === null || !is_array($this->_accesosArray)) {
                $pattern = '/numero_imagenes"?\s*:\s*(\d+).+max_caracteres"?\s*:\s*(\d+)/s';
                if (preg_match($pattern, $this->accesos, $matches) && count($matches) >= 3) {
                    $this->_accesosArray = [
                        'numero_imagenes' => (int)$matches[1],
                        'max_caracteres' => (int)$matches[2]
                    ];
                }
            }
            
            // Si todo falla, usar un array vacío
            if ($this->_accesosArray === null) {
                $this->_accesosArray = [];
            }
        }
        
        return $this->_accesosArray;
    }
    
    /**
     * Obtiene el número máximo de imágenes permitidas
     * @return int
     */
    public function getNumeroImagenes()
    {
        $accesos = $this->getAccesosArray();
        return isset($accesos['numero_imagenes']) ? (int)$accesos['numero_imagenes'] : 0;
    }
    
    /**
     * Obtiene el número máximo de caracteres permitidos
     * @return int
     */
    public function getMaxCaracteres()
    {
        $accesos = $this->getAccesosArray();
        return isset($accesos['max_caracteres']) ? (int)$accesos['max_caracteres'] : 480;
    }
}
