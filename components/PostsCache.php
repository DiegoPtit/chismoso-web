<?php

namespace app\components;

use Yii;
use app\models\Posts;
use yii\base\Component;

/**
 * Componente para gestionar el caché de datos de posts y comentarios
 * 
 * Este componente almacena información de posts y comentarios en caché
 * para permitir autocompletado y mejora de rendimiento.
 */
class PostsCache extends Component
{
    /**
     * Tiempo de expiración del caché en segundos (por defecto 1 hora)
     * @var integer
     */
    public $cacheDuration = 3600;
    
    /**
     * Prefijo para las claves de caché
     * @var string
     */
    public $cachePrefix = 'post_data_';
    
    /**
     * Obtiene los datos de usuario almacenados en caché
     * 
     * @param integer $userId ID del usuario
     * @return array|null Datos del usuario o null si no hay caché
     */
    public function getUserPostData($userId)
    {
        $cacheKey = $this->cachePrefix . 'user_' . $userId;
        return Yii::$app->cache->get($cacheKey);
    }
    
    /**
     * Guarda los datos de usuario en caché
     * 
     * @param integer $userId ID del usuario
     * @param array $data Datos a almacenar
     * @return boolean Resultado de la operación
     */
    public function setUserPostData($userId, $data)
    {
        $cacheKey = $this->cachePrefix . 'user_' . $userId;
        return Yii::$app->cache->set($cacheKey, $data, $this->cacheDuration);
    }
    
    /**
     * Almacena información de un post creado
     * 
     * @param Posts $post Modelo de post creado
     * @return boolean Resultado de la operación
     */
    public function storePostData($post)
    {
        $userId = $post->usuario_id;
        $userData = $this->getUserPostData($userId) ?: [];
        
        // Almacenar edad y género utilizados
        $userData['last_age'] = $post->age;
        $userData['last_genre'] = $post->genre;
        
        // Almacenar temas recientes (etiquetas o palabras clave del contenido)
        $keywords = $this->extractKeywords($post->contenido);
        
        if (!isset($userData['keywords'])) {
            $userData['keywords'] = [];
        }
        
        // Agregar nuevas palabras clave y mantener solo las últimas 20
        $userData['keywords'] = array_merge($keywords, $userData['keywords']);
        $userData['keywords'] = array_unique(array_slice($userData['keywords'], 0, 20));
        
        // Guardar el caché actualizado
        return $this->setUserPostData($userId, $userData);
    }
    
    /**
     * Almacena información de una respuesta/comentario
     * 
     * @param Posts $comment Modelo de comentario
     * @return boolean Resultado de la operación
     */
    public function storeCommentData($comment)
    {
        // Utiliza la misma función que para posts, ya que en este modelo son lo mismo
        return $this->storePostData($comment);
    }
    
    /**
     * Obtiene datos para autocompletar al crear nuevo post
     * 
     * @param integer $userId ID del usuario
     * @return array Datos para autocompletar
     */
    public function getAutocompleteData($userId)
    {
        $userData = $this->getUserPostData($userId) ?: [];
        
        return [
            'age' => $userData['last_age'] ?? null,
            'genre' => $userData['last_genre'] ?? null,
            'keywords' => $userData['keywords'] ?? []
        ];
    }
    
    /**
     * Extrae palabras clave de un texto
     * 
     * @param string $content Contenido del post
     * @return array Lista de palabras clave
     */
    protected function extractKeywords($content)
    {
        // Obtener palabras del contenido (simplificado)
        $words = preg_split('/\s+/', strtolower($content));
        
        // Filtrar palabras cortas y comunes
        $minLength = 4;
        $stopWords = ['para', 'como', 'pero', 'este', 'esta', 'esto', 'porque', 'cuando', 'donde', 'desde', 'hasta', 'sobre'];
        
        $keywords = [];
        foreach ($words as $word) {
            $word = preg_replace('/[^\p{L}\p{N}_]/u', '', $word);
            if (strlen($word) >= $minLength && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        
        return array_unique($keywords);
    }
    
    /**
     * Limpia el caché de un usuario
     * 
     * @param integer $userId ID del usuario
     * @return boolean Resultado de la operación
     */
    public function clearUserCache($userId)
    {
        $cacheKey = $this->cachePrefix . 'user_' . $userId;
        return Yii::$app->cache->delete($cacheKey);
    }
} 