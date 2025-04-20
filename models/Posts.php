<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property int|null $padre_id
 * @property string $contenido
 * @property int|null $age
 * @property int|null $genre
 * @property string|null $created_at
 * @property int $likes
 * @property int $dislikes
 * @property string|null $img_routes JSON con información de las imágenes adjuntas
 *
 * @property Posts $padre
 * @property Posts[] $posts
 * @property Usuarios $usuario
 */
class Posts extends \yii\db\ActiveRecord
{
    /**
     * @var array Imágenes temporales cargadas
     */
    public $imageFiles;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function() { return date('Y-m-d H:i:s'); },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'padre_id', 'age', 'genre', 'likes', 'dislikes'], 'integer'],
            [['likes'], 'default', 'value' => 0],
            [['dislikes'], 'default', 'value' => 0],
            [['contenido'], 'required'],
            [['contenido', 'img_routes'], 'string'],
            [['created_at'], 'safe'],
            [['padre_id'], 'exist', 'skipOnError' => true, 'targetClass' => Posts::class, 'targetAttribute' => ['padre_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::class, 'targetAttribute' => ['usuario_id' => 'id']],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'padre_id' => 'Padre ID',
            'contenido' => 'Contenido',
            'age' => 'Edad',
            'genre' => 'Género',
            'created_at' => 'Fecha',
            'likes' => 'Likes',
            'dislikes' => 'Dislikes',
            'img_routes' => 'Imágenes',
            'imageFiles' => 'Archivos de imagen',
        ];
    }

    /**
     * Gets query for [[Padre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPadre()
    {
        return $this->hasOne(Posts::class, ['id' => 'padre_id']);
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Posts::class, ['padre_id' => 'id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::class, ['id' => 'usuario_id']);
    }

    /**
     * Guarda las imágenes cargadas y actualiza el atributo img_routes
     * @return boolean Si se guardaron correctamente las imágenes
     */
    public function saveImages()
    {
        if ($this->imageFiles) {
            $imagenes = [];
            $timestamp = time();
            $uploadPath = Yii::getAlias('@webroot/uploads/posts/' . date('Y/m/'));
            
            // Crear directorio si no existe
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            foreach ($this->imageFiles as $index => $file) {
                $fileName = 'post_' . $this->id . '_' . $timestamp . '_' . $index . '.' . $file->extension;
                $filePath = $uploadPath . $fileName;
                
                if ($file->saveAs($filePath)) {
                    $imagenes[] = [
                        'file' => '/uploads/posts/' . date('Y/m/') . $fileName,
                        'name' => $fileName,
                        'type' => $file->type,
                        'size' => $file->size,
                    ];
                }
            }
            
            if (!empty($imagenes)) {
                $this->img_routes = json_encode($imagenes);
                return $this->save(false);
            }
        }
        
        return true;
    }

    /**
     * Obtiene las imágenes del post como array
     * @return array Array de imágenes
     */
    public function getImagesList()
    {
        if ($this->img_routes) {
            return json_decode($this->img_routes, true);
        }
        return [];
    }

    public function getSubcomentarios()
    {
        return $this->hasMany(Posts::class, ['padre_id' => 'id'])
                    ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * @var Posts[] array para almacenar subcomentarios cargados manualmente
     */
    private $_subcomentarios;

    /**
     * Establece los subcomentarios para este post
     * @param array $subcomentarios
     */
    public function setSubcomentarios($subcomentarios)
    {
        $this->_subcomentarios = $subcomentarios;
    }

    /**
     * Obtiene los subcomentarios cargados manualmente o desde la relación
     * @return Posts[]
     */
    public function getSubcomentariosRecursivos()
    {
        return $this->_subcomentarios !== null ? $this->_subcomentarios : $this->subcomentarios;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Asegurar que el contenido se guarde con los caracteres especiales
        if ($this->contenido !== null) {
            $this->contenido = mb_convert_encoding($this->contenido, 'UTF-8', 'UTF-8');
        }

        return true;
    }
}
