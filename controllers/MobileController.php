<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Posts;
use app\models\Logs;
use app\models\Usuarios;
use app\models\BannedPosts;
use app\models\BannedUsuarios;
use app\models\Notificaciones;
use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

class MobileController extends Controller
{
    public $layout = 'mobile/main';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['admin', 'logs', 'gestion-contenido', 'admin-usuarios', 'desbloquear-post', 
                          'desbloquear-usuario', 'cambiar-rol', 'eliminar-usuario'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['admin', 'logs', 'gestion-contenido', 'admin-usuarios', 'desbloquear-post', 
                                     'desbloquear-usuario', 'cambiar-rol', 'eliminar-usuario'],
                        'matchCallback' => function ($rule, $action) {
                            return !Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315]);
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
                    return $this->redirect(['mobile/index']);
                }
            ],
        ];
    }

    public function actionIndex()
    {
        // Ya no filtraremos los posts baneados, sino que los mostraremos todos
        $posts = Posts::find()
            ->where(['padre_id' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10) // Limitamos a 10 posts iniciales para la paginación
            ->all();
        return $this->render('index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Acción para mostrar las notificaciones del usuario
     * @return string
     */
    public function actionNotificaciones()
    {
        // Verificar si el usuario está autenticado
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Debes iniciar sesión para ver tus notificaciones.');
            return $this->redirect(['mobile/login']);
        }
        
        // Obtener las notificaciones del usuario actual
        $notificaciones = Notificaciones::find()
            ->where(['receptor_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('notificaciones', [
            'notificaciones' => $notificaciones,
        ]);
    }

    /**
     * Panel principal de administración
     * @return string
     */
    public function actionAdmin()
    {
        // Obtener estadísticas generales
        $stats = [
            'usuarios' => Usuarios::find()->count(),
            'posts' => Posts::find()->where(['padre_id' => null])->count(),
            'comentarios' => Posts::find()->where(['IS NOT', 'padre_id', null])->count(),
            'baneados' => BannedUsuarios::find()->count() + BannedPosts::find()->count(),
        ];
        
        return $this->render('admin', [
            'stats' => $stats,
        ]);
    }

    /**
     * Dashboard de logs de actividad
     * @return string
     */
    public function actionLogs()
    {
        // Filtros de tipo y fecha
        $accionFiltro = Yii::$app->request->get('accion', '');
        $fechaFiltro = Yii::$app->request->get('fecha', 'semana');
        
        // Construir la consulta base
        $query = Logs::find()
            ->orderBy(['fecha_hora' => SORT_DESC]);
        
        // Aplicar filtro de acción si existe
        if (!empty($accionFiltro)) {
            $query->andWhere(['accion' => $accionFiltro]);
        }
        
        // Aplicar filtro de fecha
        $fechaActual = date('Y-m-d H:i:s');
        switch ($fechaFiltro) {
            case 'hoy':
                $query->andWhere(['>=', 'fecha_hora', date('Y-m-d 00:00:00')]);
                break;
            case 'semana':
                $query->andWhere(['>=', 'fecha_hora', date('Y-m-d 00:00:00', strtotime('-7 days'))]);
                break;
            case 'mes':
                $query->andWhere(['>=', 'fecha_hora', date('Y-m-d 00:00:00', strtotime('-30 days'))]);
                break;
            // caso 'todo': no aplicamos filtro de fecha
        }
        
        // Paginación
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 15,
        ]);
        
        // Obtener logs según la paginación
        $logs = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        
        // Estadísticas para las tarjetas
        $stats = [
            'total_logs' => Logs::find()->count(),
            'logins_hoy' => Logs::find()
                ->where(['like', 'accion', 'login'])
                ->andWhere(['>=', 'fecha_hora', date('Y-m-d 00:00:00')])
                ->count(),
            'posts_hoy' => Logs::find()
                ->where(['like', 'accion', 'post'])
                ->andWhere(['>=', 'fecha_hora', date('Y-m-d 00:00:00')])
                ->count(),
            'comentarios_hoy' => Logs::find()
                ->where(['like', 'accion', 'comment'])
                ->andWhere(['>=', 'fecha_hora', date('Y-m-d 00:00:00')])
                ->count(),
        ];
        
        return $this->render('logs', [
            'logs' => $logs,
            'pagination' => $pagination,
            'stats' => $stats,
        ]);
    }

    /**
     * Gestión de contenido baneado
     * @return string
     */
    public function actionGestionContenido()
    {
        // Obtener todos los posts baneados con sus relaciones
        $postsBaneados = BannedPosts::find()
            ->with(['post', 'post.usuario'])
            ->orderBy(['at_time' => SORT_DESC])
            ->all();
        
        // Obtener todos los usuarios baneados con sus relaciones
        $usuariosBaneados = BannedUsuarios::find()
            ->with('usuario')
            ->orderBy(['at_time' => SORT_DESC])
            ->all();
        
        // Array de motivos para mostrar descripciones en lugar de códigos
        $motivos = [
            'HATE_LANG' => 'Lenguaje de odio',
            'KIDS_HASSARAMENT' => 'Acoso a menores',
            'SENSIBLE_CONTENT' => 'Contenido sensible',
            'SCAM' => 'Estafa',
            'SPAM' => 'Spam',
            'RACIST_LANG' => 'Racismo',
            'MODERATED' => 'Moderado por administración',
        ];
        
        return $this->render('gestion-contenido', [
            'postsBaneados' => $postsBaneados,
            'usuariosBaneados' => $usuariosBaneados,
            'motivos' => $motivos,
        ]);
    }

    /**
     * Administración de usuarios
     * @return string
     */
    public function actionAdminUsuarios()
    {
        // Obtener todos los usuarios
        $usuarios = Usuarios::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        
        return $this->render('admin-usuarios', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Acción para desbloquear un post baneado
     * @return array Respuesta JSON
     */
    public function actionDesbloquearPost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        // Obtener ID del registro a desbloquear
        $id = $request->post('id');
        
        if (!$id) {
            return ['success' => false, 'message' => 'ID no proporcionado.'];
        }
        
        // Buscar el registro de baneo
        $bannedPost = BannedPosts::findOne($id);
        
        if (!$bannedPost) {
            return ['success' => false, 'message' => 'Registro de baneo no encontrado.'];
        }
        
        // Intentar eliminar el registro de baneo
        if ($bannedPost->delete()) {
            // Registrar en el log
            $log = new Logs();
            $log->usuario_id = Yii::$app->user->id;
            $log->tipo = 'moderation';
            $log->accion = 'Desbloqueó el post ID: ' . $bannedPost->post_id;
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();
            
            return ['success' => true, 'message' => 'Post desbloqueado correctamente.'];
        } else {
            return ['success' => false, 'message' => 'No se pudo desbloquear el post.'];
        }
    }

    /**
     * Acción para desbloquear un usuario baneado
     * @return array Respuesta JSON
     */
    public function actionDesbloquearUsuario()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        // Obtener ID del registro a desbloquear
        $id = $request->post('id');
        
        if (!$id) {
            return ['success' => false, 'message' => 'ID no proporcionado.'];
        }
        
        // Buscar el registro de baneo
        $bannedUser = BannedUsuarios::findOne($id);
        
        if (!$bannedUser) {
            return ['success' => false, 'message' => 'Registro de baneo no encontrado.'];
        }
        
        // Guardar el usuario_id antes de eliminar el registro
        $usuarioId = $bannedUser->usuario_id;
        
        // Intentar eliminar el registro de baneo
        if ($bannedUser->delete()) {
            // Registrar en el log
            $log = new Logs();
            $log->usuario_id = Yii::$app->user->id;
            $log->tipo = 'moderation';
            $log->accion = 'Desbloqueó el usuario ID: ' . $usuarioId;
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();
            
            return ['success' => true, 'message' => 'Usuario desbloqueado correctamente.'];
        } else {
            return ['success' => false, 'message' => 'No se pudo desbloquear el usuario.'];
        }
    }

    /**
     * Acción para cambiar el rol de un usuario
     * @return array Respuesta JSON
     */
    public function actionCambiarRol()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        // Obtener parámetros
        $usuarioId = $request->post('usuario_id');
        $rolId = $request->post('rol_id');
        
        if (!$usuarioId || !$rolId) {
            return ['success' => false, 'message' => 'Parámetros incompletos.'];
        }
        
        // Validar que el rol existe
        $rolesPermitidos = [1313, 1314, 1315, 1316]; // SUPERSU, ADMIN, MOD, USER
        if (!in_array($rolId, $rolesPermitidos)) {
            return ['success' => false, 'message' => 'Rol no válido.'];
        }
        
        // Buscar el usuario
        $usuario = Usuarios::findOne($usuarioId);
        
        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }
        
        // Si el usuario es SUPERSU (1313) y el que hace el cambio no es SUPERSU, denegar
        if ($usuario->rol_id == 1313 && Yii::$app->user->identity->rol_id != 1313) {
            return ['success' => false, 'message' => 'No tienes permisos para cambiar el rol de un SUPERSU.'];
        }
        
        // Cambiar el rol
        $usuario->rol_id = $rolId;
        
        if ($usuario->save()) {
            // Registrar en el log
            $log = new Logs();
            $log->usuario_id = Yii::$app->user->id;
            $log->tipo = 'moderation';
            $log->accion = 'Cambió el rol del usuario ID: ' . $usuarioId . ' a ' . $rolId;
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();
            
            return ['success' => true, 'message' => 'Rol cambiado correctamente.'];
        } else {
            $errors = $usuario->getErrors();
            $errorMsg = 'No se pudo cambiar el rol: ';
            foreach ($errors as $attribute => $error) {
                $errorMsg .= implode(', ', $error);
            }
            
            return ['success' => false, 'message' => $errorMsg];
        }
    }

    /**
     * Acción para eliminar un usuario
     * @return array Respuesta JSON
     */
    public function actionEliminarUsuario()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        
        // Obtener ID del usuario a eliminar
        $usuarioId = $request->post('usuario_id');
        
        if (!$usuarioId) {
            return ['success' => false, 'message' => 'ID de usuario no proporcionado.'];
        }
        
        // Buscar el usuario
        $usuario = Usuarios::findOne($usuarioId);
        
        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }
        
        // Verificar si el usuario es SUPERSU (1313)
        if ($usuario->rol_id == 1313) {
            // Solo otro SUPERSU puede eliminar a un SUPERSU
            if (Yii::$app->user->identity->rol_id != 1313) {
                return ['success' => false, 'message' => 'No tienes permisos para eliminar a un SUPERSU.'];
            }
        }
        
        // Verificar si el usuario está intentando eliminarse a sí mismo
        if ($usuarioId == Yii::$app->user->id) {
            return ['success' => false, 'message' => 'No puedes eliminarte a ti mismo.'];
        }
        
        // Intentar eliminar el usuario
        try {
            $usuario->delete();
            
            // Registrar en el log
            $log = new Logs();
            $log->usuario_id = Yii::$app->user->id;
            $log->tipo = 'moderation';
            $log->accion = 'Eliminó al usuario ID: ' . $usuarioId;
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();
            
            return ['success' => true, 'message' => 'Usuario eliminado correctamente.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'No se pudo eliminar el usuario: ' . $e->getMessage()];
        }
    }

    public function actionCreatePost()
    {
        // Verificar si el usuario está autenticado
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Debes iniciar sesión para crear un post.');
            return $this->redirect(['mobile/login']);
        }
        
        // Verificar si el usuario está baneado
        $isBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->exists();
            
        if ($isBanned) {
            Yii::$app->session->setFlash('error', 'Tu cuenta ha sido baneada por violar las normas de la comunidad.');
            return $this->redirect(['mobile/index']);
        }
        
        $modelPost = new Posts();
        
        // Para peticiones AJAX
        if (Yii::$app->request->isAjax && $modelPost->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            // Asignar el usuario actual
            $modelPost->usuario_id = Yii::$app->user->id;
            
            // Establecer valores por defecto
            $modelPost->likes = 0;
            $modelPost->dislikes = 0;
            $modelPost->created_at = date('Y-m-d H:i:s');
            
            // Procesar las imágenes subidas
            $modelPost->imageFiles = UploadedFile::getInstances($modelPost, 'imageFiles');
            
            // Verificar límites de suscripción (máximo 1 imagen por defecto)
            $maxImages = 1;
            
            try {
                // Intentar obtener información de suscripción (si está disponible)
                $subscriptionData = Yii::$app->runAction('site/check-subscription');
                if (isset($subscriptionData['maxImages'])) {
                    $maxImages = $subscriptionData['maxImages'];
                }
            } catch (\Exception $e) {
                Yii::warning('No se pudo verificar la suscripción: ' . $e->getMessage(), 'app');
                // Mantener el valor predeterminado en caso de error
            }
            
            // Limitar el número de imágenes según la suscripción
            if (count($modelPost->imageFiles) > $maxImages) {
                $modelPost->imageFiles = array_slice($modelPost->imageFiles, 0, $maxImages);
            }
            
            // Primero guardamos el post para obtener el ID
            if ($modelPost->save()) {
                // Guardar las imágenes usando el método del modelo
                $imagesSaved = $modelPost->saveImages();
                
                if (!$imagesSaved) {
                    // Si falló la subida de imágenes, log de error
                    Yii::error('Error al guardar las imágenes para el post ID: ' . $modelPost->id, 'app');
                }
                
                // Guardar los datos en caché
                Yii::$app->postsCache->storePostData($modelPost);
                
                
                return [
                    'success' => true,
                    'message' => 'Tu chisme ha sido publicado con éxito.',
                    'redirectUrl' => Yii::$app->urlManager->createUrl(['mobile/index'])
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Hubo un error al publicar tu chisme. Por favor, intenta de nuevo.',
                    'errors' => $modelPost->getErrors()
                ];
            }
        }
        // Para solicitudes normales (no AJAX)
        else if ($modelPost->load(Yii::$app->request->post())) {
            // Asignar el usuario actual
            $modelPost->usuario_id = Yii::$app->user->id;
            
            // Establecer valores por defecto
            $modelPost->likes = 0;
            $modelPost->dislikes = 0;
            $modelPost->created_at = date('Y-m-d H:i:s');
            
            // Procesar las imágenes subidas
            $modelPost->imageFiles = UploadedFile::getInstances($modelPost, 'imageFiles');
            
            // Verificar límites de suscripción (máximo 1 imagen por defecto)
            $maxImages = 1;
            
            try {
                // Intentar obtener información de suscripción (si está disponible)
                $subscriptionData = Yii::$app->runAction('site/check-subscription');
                if (isset($subscriptionData['maxImages'])) {
                    $maxImages = $subscriptionData['maxImages'];
                }
            } catch (\Exception $e) {
                Yii::warning('No se pudo verificar la suscripción: ' . $e->getMessage(), 'app');
                // Mantener el valor predeterminado en caso de error
            }
            
            // Limitar el número de imágenes según la suscripción
            if (count($modelPost->imageFiles) > $maxImages) {
                $modelPost->imageFiles = array_slice($modelPost->imageFiles, 0, $maxImages);
            }
            
            // Primero guardamos el post para obtener el ID
            if ($modelPost->save()) {
                // Guardar las imágenes usando el método del modelo
                $imagesSaved = $modelPost->saveImages();
                
                if (!$imagesSaved) {
                    // Si falló la subida de imágenes, mostrar error
                    Yii::$app->session->setFlash('warning', 'El post se creó pero hubo un problema al guardar las imágenes.');
                }
                
                // Guardar los datos en caché
                Yii::$app->postsCache->storePostData($modelPost);
                
                // Registrar en logs
                $log = new Logs();
                $log->usuario_id = Yii::$app->user->id;
                $log->tipo = 'post';
                $log->accion = 'Creó un nuevo post ID: ' . $modelPost->id;
                $log->fecha_hora = date('Y-m-d H:i:s');
                $log->save();
                
                Yii::$app->session->setFlash('success', 'Tu chisme ha sido publicado con éxito.');
                return $this->redirect(['mobile/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Hubo un error al publicar tu chisme. Por favor, intenta de nuevo.');
            }
        } else {
            // Si es una carga inicial del formulario, intentar autocompletar
            if (!Yii::$app->user->isGuest) {
                $autocompleteData = Yii::$app->postsCache->getAutocompleteData(Yii::$app->user->id);
                
                if ($autocompleteData) {
                    $modelPost->age = $autocompleteData['age'];
                    $modelPost->genre = $autocompleteData['genre'];
                }
            }
        }
        
        return $this->render('create-post', [
            'modelPost' => $modelPost,
        ]);
    }
    
    /**
     * Login action para la versión móvil
     * 
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['mobile/index']);
        }

        $model = new \app\models\LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['mobile/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Register action para la versión móvil
     * 
     * @return \yii\web\Response
     */
    public function actionRegister()
    {
        $model = new Usuarios();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Registro exitoso. Por favor inicie sesión.');
                return $this->redirect(['mobile/login']);
            } else {
                Yii::$app->session->setFlash('error', 'Error en el registro. Verifique los datos.');
            }
        }

        return $this->redirect(['mobile/login']);
    }

    public function actionLike($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Verificar si el usuario está autenticado
        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Debes iniciar sesión para dar like.'];
        }
        
        // Verificar si el usuario está baneado
        $isBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->exists();
            
        if ($isBanned) {
            return ['success' => false, 'message' => 'Tu cuenta ha sido baneada por violar las normas de la comunidad.'];
        }
        
        $post = Posts::findOne($id);
        
        if (!$post) {
            return ['success' => false, 'message' => 'El post no existe.'];
        }
        
        // Verificar si el post está baneado
        $isPostBanned = \app\models\BannedPosts::find()
            ->where(['post_id' => $id])
            ->exists();
            
        if ($isPostBanned) {
            return ['success' => false, 'message' => 'Este post ha sido baneado por violar las normas de la comunidad.'];
        }
        
        // Incrementar el contador de likes
        $post->likes = ($post->likes ?? 0) + 1;
        
        if ($post->save()) {
            return ['success' => true, 'likes' => $post->likes];
        } else {
            return ['success' => false, 'message' => 'No se pudo procesar el like.'];
        }
    }

    public function actionDislike($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Verificar si el usuario está autenticado
        if (Yii::$app->user->isGuest) {
            return ['success' => false, 'message' => 'Debes iniciar sesión para dar dislike.'];
        }
        
        // Verificar si el usuario está baneado
        $isBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->exists();
            
        if ($isBanned) {
            return ['success' => false, 'message' => 'Tu cuenta ha sido baneada por violar las normas de la comunidad.'];
        }
        
        $post = Posts::findOne($id);
        
        if (!$post) {
            return ['success' => false, 'message' => 'El post no existe.'];
        }
        
        // Verificar si el post está baneado
        $isPostBanned = \app\models\BannedPosts::find()
            ->where(['post_id' => $id])
            ->exists();
            
        if ($isPostBanned) {
            return ['success' => false, 'message' => 'Este post ha sido baneado por violar las normas de la comunidad.'];
        }
        
        // Incrementar el contador de dislikes
        $post->dislikes = ($post->dislikes ?? 0) + 1;
        
        if ($post->save()) {
            return ['success' => true, 'dislikes' => $post->dislikes];
        } else {
            return ['success' => false, 'message' => 'No se pudo procesar el dislike.'];
        }
    }
    
    /**
     * Acción para mostrar la vista de comentarios de un post específico
     * @param integer $id ID del post
     * @return mixed
     * @throws NotFoundHttpException si el post no existe
     */
    public function actionComentarios($id)
    {
        $post = Posts::findOne($id);
        
        if (!$post) {
            throw new NotFoundHttpException('El post no existe o ha sido eliminado.');
        }
        
        // Ya no redireccionamos si el post está baneado, lo mostramos con un mensaje de baneo
        
        // Obtener todos los comentarios del post (sin filtrar los baneados)
        $comentarios = Posts::find()
            ->where(['padre_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        // Cargar todos los niveles de subcomentarios de forma recursiva
        $this->cargarSubcomentariosRecursivos($comentarios);
        
        return $this->render('comentarios', [
            'post' => $post,
            'comentarios' => $comentarios,
        ]);
    }
    
    /**
     * Carga recursivamente los subcomentarios para un conjunto de comentarios
     * @param array $comentarios Array de objetos Posts
     * @param int $nivel Nivel de recursión actual (opcional, para control interno)
     * @param int $maxNivel Nivel máximo de recursión permitido (opcional)
     */
    private function cargarSubcomentariosRecursivos(&$comentarios, $nivel = 1, $maxNivel = 10)
    {
        // Limitar la profundidad de la recursión para evitar problemas de rendimiento
        if ($nivel > $maxNivel || empty($comentarios)) {
            return;
        }
        
        foreach ($comentarios as $comentario) {
            // Cargar los subcomentarios sin filtrar los baneados
            $subcomentarios = Posts::find()
                ->where(['padre_id' => $comentario->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
            
            // Usar el método setter para asignar los subcomentarios
            $comentario->setSubcomentarios($subcomentarios);
            
            // Llamada recursiva para cargar los subcomentarios de nivel más profundo
            if (!empty($subcomentarios)) {
                $this->cargarSubcomentariosRecursivos($subcomentarios, $nivel + 1, $maxNivel);
            }
        }
    }
    
    /**
     * Acción para crear un nuevo comentario
     * @return mixed
     */
    public function actionCreateComment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Verificar si el usuario está autenticado
        if (Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'message' => 'Debes iniciar sesión para comentar.'
            ];
        }
        
        // Verificar si el usuario está baneado
        $isBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->exists();
            
        if ($isBanned) {
            return [
                'success' => false,
                'message' => 'Tu cuenta ha sido baneada por violar las normas de la comunidad.'
            ];
        }
        
        $model = new Posts();
        $postParams = Yii::$app->request->post('Posts');
        
        if ($postParams) {
            $model->usuario_id = Yii::$app->user->id;
            $model->padre_id = $postParams['padre_id'] ?? null;
            $model->contenido = $postParams['contenido'] ?? '';
            $model->age = $postParams['age'] ?? null;
            $model->genre = $postParams['genre'] ?? 0;
            $model->likes = 0;
            $model->dislikes = 0;
            $model->created_at = date('Y-m-d H:i:s');
            
            if ($model->save()) {
                // Guardar los datos en caché
                Yii::$app->postsCache->storeCommentData($model);
                
                // Obtener el post padre
                $postPadre = Posts::findOne($model->padre_id);
                
                if ($postPadre) {
                    // Notificaciones: notificar al autor del post padre
                    if ($postPadre->usuario_id != Yii::$app->user->id) {
                        $notificacion = new \app\models\Notificaciones();
                        $notificacion->receptor_id = $postPadre->usuario_id;
                        $notificacion->post_original_id = $postPadre->id;
                        $notificacion->comentario_id = $model->id;
                        $notificacion->save();
                    }
                }
                
                return [
                    'success' => true,
                    'message' => 'Comentario enviado correctamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Hubo un error al enviar el comentario. Por favor, intenta de nuevo más tarde.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Datos del comentario no válidos.'
            ];
        }
    }

    /**
     * Acción para banear un post
     * @param integer $id ID del post
     * @return array Respuesta JSON
     */
    public function actionBanPost($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Verificar si el usuario tiene permisos (roles 1313, 1314, 1315)
        if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
            return ['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'];
        }
        
        $post = Posts::findOne($id);
        
        if (!$post) {
            return ['success' => false, 'message' => 'El post no existe.'];
        }
        
        // Obtener el motivo del baneo, defaulteando a MODERATED si no se proporciona
        $motivo = Yii::$app->request->post('motivo', 'MODERATED');
        
        // Validar que el motivo sea uno de los permitidos
        $motivosPermitidos = ['HATE_LANG', 'KIDS_HASSARAMENT', 'SENSIBLE_CONTENT', 'SCAM', 'SPAM', 'RACIST_LANG', 'MODERATED'];
        if (!in_array($motivo, $motivosPermitidos)) {
            $motivo = 'MODERATED'; // Si no es un motivo válido, usar el default
        }
        
        // Crear el registro en la tabla de posts baneados
        $bannedPost = new \app\models\BannedPosts();
        $bannedPost->post_id = $post->id;
        $bannedPost->motivo = $motivo;
        $bannedPost->at_time = date('Y-m-d H:i:s');
        
        if ($bannedPost->save()) {
            return ['success' => true, 'message' => 'Post baneado correctamente.'];
        } else {
            $errors = $bannedPost->getErrors();
            $errorMsg = 'No se pudo banear el post: ';
            foreach ($errors as $attribute => $error) {
                $errorMsg .= implode(', ', $error);
            }
            
            return ['success' => false, 'message' => $errorMsg];
        }
    }
    
    /**
     * Acción para banear un comentario (tratado como post)
     * @param integer $id ID del comentario
     * @return array Respuesta JSON
     */
    public function actionBanComment($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Verificar si el usuario tiene permisos (roles 1313, 1314, 1315)
        if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
            return ['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'];
        }
        
        $comentario = Posts::findOne($id);
        
        if (!$comentario) {
            return ['success' => false, 'message' => 'El comentario no existe.'];
        }
        
        // Obtener el motivo del baneo, defaulteando a MODERATED si no se proporciona
        $motivo = Yii::$app->request->post('motivo', 'MODERATED');
        
        // Validar que el motivo sea uno de los permitidos
        $motivosPermitidos = ['HATE_LANG', 'KIDS_HASSARAMENT', 'SENSIBLE_CONTENT', 'SCAM', 'SPAM', 'RACIST_LANG', 'MODERATED'];
        if (!in_array($motivo, $motivosPermitidos)) {
            $motivo = 'MODERATED'; // Si no es un motivo válido, usar el default
        }
        
        // Crear el registro en la tabla de posts baneados (los comentarios son posts)
        $bannedPost = new \app\models\BannedPosts();
        $bannedPost->post_id = $comentario->id;
        $bannedPost->motivo = $motivo;
        $bannedPost->at_time = date('Y-m-d H:i:s');
        
        if ($bannedPost->save()) {
            return ['success' => true, 'message' => 'Comentario baneado correctamente.'];
        } else {
            $errors = $bannedPost->getErrors();
            $errorMsg = 'No se pudo banear el comentario: ';
            foreach ($errors as $attribute => $error) {
                $errorMsg .= implode(', ', $error);
            }
            
            return ['success' => false, 'message' => $errorMsg];
        }
    }
    
    /**
     * Acción para banear un usuario
     * @param integer $id ID del usuario
     * @return array Respuesta JSON
     */
    public function actionBanUser($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Verificar si el usuario tiene permisos (roles 1313, 1314, 1315)
        if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
            return ['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'];
        }
        
        // Verificar que el usuario a banear existe
        $usuario = \app\models\Usuarios::findOne($id);
        if (!$usuario) {
            return ['success' => false, 'message' => 'El usuario no existe.'];
        }
        
        // Verificar si el usuario está intentando banearse a sí mismo
        if ($id == Yii::$app->user->id) {
            return ['success' => false, 'message' => 'No puedes bloquear tu propia cuenta.'];
        }
        
        // Crear el registro en la tabla de usuarios baneados
        $bannedUser = new \app\models\BannedUsuarios();
        $bannedUser->usuario_id = $usuario->id;
        $bannedUser->at_time = date('Y-m-d H:i:s');
        
        if ($bannedUser->save()) {
            return ['success' => true, 'message' => 'Usuario baneado correctamente.'];
        } else {
            $errors = $bannedUser->getErrors();
            $errorMsg = 'No se pudo banear al usuario: ';
            foreach ($errors as $attribute => $error) {
                $errorMsg .= implode(', ', $error);
            }
            
            return ['success' => false, 'message' => $errorMsg];
        }
    }

    /**
     * Acción para cargar más posts mediante AJAX para el scroll infinito
     * @return json
     */
    public function actionLoadMorePosts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Log para depuración
        Yii::info('Ejecutando actionLoadMorePosts', 'app');
        
        // Aceptar tanto POST como GET
        $page = Yii::$app->request->get('page', Yii::$app->request->post('page', 1));
        $pageSize = 10; // Número de posts por página
        $offset = ($page - 1) * $pageSize;
        
        // Log para depuración
        Yii::info('Cargando página: ' . $page . ', offset: ' . $offset, 'app');
        
        $posts = Posts::find()
            ->where(['padre_id' => null])
            ->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($pageSize)
            ->all();
        
        // Log para depuración
        Yii::info('Posts encontrados: ' . count($posts), 'app');
        
        if (empty($posts)) {
            Yii::info('No se encontraron más posts', 'app');
            return [
                'success' => true,
                'posts' => '',
                'hasMore' => false
            ];
        }
        
        // Renderizar los posts directamente
        $postsHtml = $this->renderPartial('/mobile/_partials/_posts_list', [
            'posts' => $posts
        ]);
        
        // Log para depuración
        Yii::info('HTML generado: ' . strlen($postsHtml) . ' caracteres', 'app');
        
        return [
            'success' => true,
            'posts' => $postsHtml,
            'hasMore' => count($posts) === $pageSize
        ];
    }

    /**
     * API para obtener logs en formato JSON
     * @return array
     */
    public function actionApiLogs()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $accionFiltro = Yii::$app->request->get('accion', '');
        $fechaFiltro = Yii::$app->request->get('fecha', 'semana');
        $page = (int)Yii::$app->request->get('page', 0);
        $pageSize = (int)Yii::$app->request->get('pageSize', 15);
        
        // Base query
        $query = Logs::find()
            ->orderBy(['fecha_hora' => SORT_DESC]);
        
        // Filtro por acción
        if (!empty($accionFiltro)) {
            $query->andWhere(['like', 'accion', $accionFiltro]);
        }
        
        // Filtro por fecha
        $ahora = new \DateTime();
        $hoy = new \DateTime($ahora->format('Y-m-d'));
        
        switch ($fechaFiltro) {
            case 'hoy':
                $query->andWhere(['>=', 'fecha_hora', $hoy->format('Y-m-d H:i:s')]);
                break;
            case 'semana':
                $inicioSemana = new \DateTime($ahora->format('Y-m-d'));
                $inicioSemana->modify('-7 days');
                $query->andWhere(['>=', 'fecha_hora', $inicioSemana->format('Y-m-d H:i:s')]);
                break;
            case 'mes':
                $inicioMes = new \DateTime($ahora->format('Y-m-d'));
                $inicioMes->modify('-30 days');
                $query->andWhere(['>=', 'fecha_hora', $inicioMes->format('Y-m-d H:i:s')]);
                break;
            // Si es 'todo', no se aplica filtro
        }
        
        // Total de logs para paginación
        $totalLogs = $query->count();
        
        // Paginar resultados
        $logs = $query
            ->with(['usuario'])
            ->offset($page * $pageSize)
            ->limit($pageSize)
            ->all();
        
        // Obtener estadísticas
        $inicioHoy = $hoy->format('Y-m-d H:i:s');
        $finHoy = (new \DateTime($ahora->format('Y-m-d 23:59:59')))->format('Y-m-d H:i:s');
        
        $loginsHoy = Logs::find()
            ->where(['like', 'accion', 'login'])
            ->andWhere(['between', 'fecha_hora', $inicioHoy, $finHoy])
            ->count();
        
        $postsHoy = Logs::find()
            ->where(['like', 'accion', 'post'])
            ->andWhere(['between', 'fecha_hora', $inicioHoy, $finHoy])
            ->count();
        
        $comentariosHoy = Logs::find()
            ->where(['like', 'accion', 'comment'])
            ->andWhere(['between', 'fecha_hora', $inicioHoy, $finHoy])
            ->count();
        
        // Preparar array de logs para JSON
        $logsArray = [];
        foreach ($logs as $log) {
            $logItem = $log->toArray();
            
            // Añadir información de usuario si existe
            if ($log->usuario) {
                $logItem['usuario'] = [
                    'id' => $log->usuario->id,
                    'user' => $log->usuario->user,
                    'rol' => $log->usuario->rol
                ];
            }
            
            $logsArray[] = $logItem;
        }
        
        // Devolver respuesta JSON
        return [
            'success' => true,
            'logs' => $logsArray,
            'pagination' => [
                'totalItems' => $totalLogs,
                'totalPages' => ceil($totalLogs / $pageSize),
                'currentPage' => $page,
                'pageSize' => $pageSize
            ],
            'stats' => [
                'total_logs' => $totalLogs,
                'logins_hoy' => $loginsHoy,
                'posts_hoy' => $postsHoy,
                'comentarios_hoy' => $comentariosHoy
            ]
        ];
    }

    /**
     * Obtiene palabras clave para autocompletado (versión móvil)
     * 
     * @return array Respuesta JSON con palabras clave
     */
    public function actionGetAutocompleteKeywords()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'keywords' => []
            ];
        }
        
        $autocompleteData = Yii::$app->postsCache->getAutocompleteData(Yii::$app->user->id);
        
        return [
            'success' => true,
            'keywords' => $autocompleteData['keywords'] ?? []
        ];
    }

    /**
     * Acción para la página de descarga de la aplicación
     * @return string
     */
    public function actionDownloadApp()
    {
        return $this->render('download-app');
    }

    /**
     * Acción para la página de perfil del usuario
     * @return string|\yii\web\Response
     */
    public function actionPerfil()
    {
        // Verificar si el usuario está autenticado
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Debes iniciar sesión para ver tu perfil.');
            return $this->redirect(['mobile/login']);
        }
        
        // Obtener el usuario actual
        $usuario = Yii::$app->user->identity;
        
        return $this->render('perfil', [
            'usuario' => $usuario,
        ]);
    }
}