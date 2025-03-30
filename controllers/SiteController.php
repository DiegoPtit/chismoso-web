<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Posts;
use app\models\Usuarios;
use app\models\Notificaciones;
use app\models\ReportedPosts;
use app\models\ReportedUsers;
use app\models\BannedPosts;
use app\models\BannedUsuarios;

class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'ban-post', 'ban-user'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['ban-post', 'ban-user'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'like' => ['post'],
                    'dislike' => ['post'],
                    'ban-post' => ['post'],
                    'ban-user' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionError()
{
    $exception = Yii::$app->errorHandler->exception;

    if ($exception !== null) {
        if ($exception instanceof \yii\db\Exception) {
            return $this->render('saturacion', ['exception' => $exception]);
        }
        return $this->render('error', ['exception' => $exception]);
    }
}


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        try {
            $page = Yii::$app->request->get('page', 1);
            $perPage = 10; // Número de posts por página
            
            // Diccionario de motivos
            $motivos = [
                'HATE_LANG' => 'Lenguaje que incita al odio',
                'KIDS_HASSARAMENT' => 'Pedofilia',
                'SENSIBLE_CONTENT' => 'Contenido extremadamente sensible',
                'SCAM' => 'Estafa',
                'SPAM' => 'Spam',
                'RACIST_LANG' => 'Racismo o Xenofobia',
                'MODERATED' => 'Moderado a razón de un administrador'
            ];
            
            $query = Posts::find()
                ->where(['padre_id' => null])
                ->with([
                    'usuario',
                    'posts' => function($query) {
                        $query->with(['usuario'])
                              ->orderBy(['created_at' => SORT_DESC]);
                    }
                ])
                ->orderBy(['created_at' => SORT_DESC]);

            // Si es una solicitud AJAX, devolver solo los posts de la página solicitada
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                $totalPosts = $query->count();
                $totalPages = ceil($totalPosts / $perPage);
                
                // Asegurarse de que la página solicitada sea válida
                if ($page > $totalPages) {
                    return [
                        'success' => false,
                        'message' => 'No hay más posts disponibles',
                        'hasMore' => false
                    ];
                }
                
                $posts = $query->offset(($page - 1) * $perPage)
                              ->limit($perPage)
                              ->all();
                
                // Verificar posts baneados
                foreach ($posts as $post) {
                    $bannedPost = BannedPosts::findOne(['post_id' => $post->id]);
                    if ($bannedPost) {
                        $post->contenido = "Este post ha sido bloqueado debido a: " . $motivos[$bannedPost->motivo];
                    }
                }
                
                $html = '';
                foreach ($posts as $post) {
                    $html .= $this->renderPartial('_post', [
                        'post' => $post,
                        'modelComentario' => new Posts(),
                    ]);
                }
                
                return [
                    'success' => true,
                    'html' => $html,
                    'hasMore' => $page < $totalPages,
                    'totalPages' => $totalPages,
                    'currentPage' => $page,
                    'totalPosts' => $totalPosts
                ];
            }

            // Para la carga inicial, solo cargamos la primera página
            $posts = $query->limit($perPage)->all();
            
            // Verificar posts baneados
            foreach ($posts as $post) {
                $bannedPost = BannedPosts::findOne(['post_id' => $post->id]);
                if ($bannedPost) {
                    $post->contenido = "Este post ha sido bloqueado debido a: " . $motivos[$bannedPost->motivo];
                }
            }
            
            $modelComentario = new Posts();

            return $this->render('index', [
                'posts' => $posts,
                'modelComentario' => $modelComentario,
                'perPage' => $perPage,
                'totalPosts' => $query->count(),
            ]);
        } catch (\Exception $e) {
            Yii::error('Error en actionIndex: ' . $e->getMessage());
            Yii::$app->session->setFlash('error', 'Ha ocurrido un error al cargar los posts. Por favor, intente de nuevo.');
            return $this->render('index', [
                'posts' => [],
                'modelComentario' => new Posts(),
                'perPage' => 10,
                'totalPosts' => 0,
            ]);
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionGetPost($id)
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    // Buscar el post por ID
    $post = Posts::findOne($id);

    // Verificar si el post existe
    if ($post) {
        // Retornar la información del post
        return [
            'success' => true,
            'post' => [
                'id' => $post->id,
                'usuario_id' => $post->usuario_id,
                'contenido' => $post->contenido,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'likes' => $post->likes,
                'dislikes' => $post->dislikes,
                'comentarios' => $post->getComentarios() // Método para obtener los comentarios asociados, si existe
            ]
        ];
    } else {
        // Si no se encuentra el post, retornar error
        return [
            'success' => false,
            'message' => 'Post no encontrado'
        ];
    }
}



    public function actionLike($id)
{
    if (Yii::$app->user->isGuest) {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => false, 'message' => 'Debes estar registrado para hacer miles de cosas asombrosas!'];
        }
        Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
        return $this->redirect(['site/login']);
    }

    $post = \app\models\Posts::findOne($id);
    if ($post) {
        $post->updateCounters(['likes' => 1]);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'count' => $post->likes];
        }
    }
    
    $modalId = Yii::$app->request->get('modal');
    return $this->redirect(['index', 'modal' => $modalId]);
}

public function actionDislike($id)
{
    if (Yii::$app->user->isGuest) {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => false, 'message' => 'Debes estar registrado para hacer miles de cosas asombrosas!'];
        }
        Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
        return $this->redirect(['site/login']);
    }

    $post = \app\models\Posts::findOne($id);
    if ($post) {
        $post->updateCounters(['dislikes' => 1]);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'count' => $post->dislikes];
        }
    }
    
    $modalId = Yii::$app->request->get('modal');
    return $this->redirect(['index', 'modal' => $modalId]);
}

    public function actionComment($post_id)
    {
        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Debes estar registrado para comentar'];
            }
            Yii::$app->session->setFlash('error', 'Debes estar registrado para comentar');
            return $this->redirect(['site/login']);
        }

        $model = new Posts();
        $model->usuario_id = Yii::$app->user->id;
        $model->padre_id = $post_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Obtener el post original (raíz de la conversación)
            $originalPost = Posts::findOne($post_id);
            while ($originalPost->padre_id !== null) {
                $originalPost = $originalPost->padre;
            }

            // Notificar a cada autor en la cadena de comentarios (padres)
            $notified = [];
            $currentParent = Posts::findOne($post_id);
            while ($currentParent) {
                if ($currentParent->usuario_id != Yii::$app->user->id && !in_array($currentParent->usuario_id, $notified)) {
                    $notificacion = new Notificaciones();
                    $notificacion->receptor_id = $currentParent->usuario_id;
                    $notificacion->post_original_id = $originalPost->id;
                    $notificacion->comentario_id = $model->id;
                    $notificacion->save();
                    $notified[] = $currentParent->usuario_id;
                }
                if ($currentParent->padre_id) {
                    $currentParent = Posts::findOne($currentParent->padre_id);
                } else {
                    break;
                }
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                // Renderizar el nuevo comentario usando renderAjax
                $commentHtml = $this->renderPartial('_comentario', [
                    'comentario' => $model,
                    'modelComentario' => new Posts(),
                ]);
                
                Yii::debug('HTML del comentario generado: ' . $commentHtml);
                
                return [
                    'success' => true,
                    'message' => 'Comentario publicado exitosamente',
                    'commentHtml' => $commentHtml,
                    'isMainPost' => ($post_id === $originalPost->id)
                ];
            }

            return $this->redirect(['index', 'modal' => $originalPost->id]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => false,
                'message' => 'Error al publicar el comentario',
                'errors' => $model->errors
            ];
        }

        return $this->redirect(['index']);
    }

    public function actionReportar($post_id = null, $usuario_id = null)
    {
        return $this->render('reportar', [
            'post_id' => $post_id,
            'usuario_id' => $usuario_id,
        ]);
    }

    public function actionApiLogs()
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $logs = \app\models\Logs::find()->orderBy(['fecha_hora' => SORT_DESC])->all();
    $data = [];
    foreach ($logs as $log) {
        $data[] = [
            'id'         => $log->id,
            'ip'         => $log->ip,
            'ubicacion'  => $log->ubicacion,
            'accion'     => $log->accion,
            'status'     => $log->status,
            'fecha_hora' => $log->fecha_hora,
            'useragent'  => $log->useragent,
            'usuario'    => $log->usuario ? [
                'id' => $log->usuario->id,
                'user' => $log->usuario->user,
                'rol_id' => $log->usuario->rol_id,
            ] : null,
        ];
    }    
    return $data;
}


    /**
     * Acción para procesar el reporte de un post/comentario.
     * Se espera recibir por POST: post_id y motivo.
     */
    public function actionCreateReportedPosts()
{
    $request = Yii::$app->request;
    if ($request->isPost) {
        $post_id = $request->post('post_id');
        $motivo = $request->post('motivo');

        if (!$post_id || !$motivo) {
            Yii::$app->session->setFlash('error', 'Faltan datos para reportar el post.');
            return $this->redirect(['reportar', 'post_id' => $post_id]);
        }

        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Debes iniciar sesión para reportar.');
            return $this->redirect(['site/login']);
        }

        $existing = ReportedPosts::find()
            ->where(['post_id' => $post_id, 'reporter_id' => Yii::$app->user->id])
            ->one();

        if ($existing !== null) {
            Yii::$app->session->setFlash('error', 'Ya has reportado este post.');
            return $this->redirect(['index']);
        }

        $model = new ReportedPosts();
        $model->post_id = $post_id;
        $model->motivo = $motivo;
        $model->reporter_id = Yii::$app->user->id;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Reporte enviado exitosamente.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al enviar el reporte.');
            return $this->redirect(['index']);
        }

        // Contar reportes totales para el post
        $reportCount = ReportedPosts::find()->where(['post_id' => $post_id])->count();

        if ($reportCount >= 10) {
            // Obtener motivo más frecuente
            $motivos = ReportedPosts::find()
                ->select(['motivo', 'COUNT(*) as count'])
                ->where(['post_id' => $post_id])
                ->groupBy('motivo')
                ->orderBy(['count' => SORT_DESC])
                ->asArray()
                ->all();

            $motivoMasFrecuente = $motivos[0]['motivo'] ?? 'Contenido inapropiado';
            
            // Diccionario de razones para el mensaje
            $motivosTexto = [
                'HATE_LANG' => 'Lenguaje que incita al odio',
                'KIDS_HASSARAMENT' => 'Pedofilia',
                'SENSIBLE_CONTENT' => 'Contenido inapropiado',
                'SCAM' => 'Estafa',
                'SPAM' => 'Spam',
                'RACIST_LANG' => 'Racismo o Xenofobia',
            ];

            $motivoTexto = $motivosTexto[$motivoMasFrecuente] ?? 'Contenido inapropiado';

            // Actualizar el post
            $post = Posts::findOne($post_id);
            if ($post) {
                $post->contenido = "Este mensaje ha sido reportado por la comunidad por: $motivoTexto";
                $post->save();
            }
        }
    }
    return $this->redirect(['index']);
}


public function actionLoadMore($offset)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $posts = Posts::find()
        ->orderBy(['created_at' => SORT_DESC])
        ->offset($offset)
        ->limit(20)
        ->all();

    if (empty($posts)) {
        return ['success' => false, 'message' => 'No hay más posts disponibles.'];
    }

    $html = '';
    foreach ($posts as $post) {
        $html .= $this->renderPartial('_post', ['post' => $post]);
    }

    return ['success' => true, 'html' => $html];
}




public function actionCreateReportedUsers()
{
    $request = Yii::$app->request;
    
    // 1. Verificar si la solicitud es POST
    if (!$request->isPost) {
        Yii::$app->session->setFlash('error', 'Método no permitido.');
        return $this->redirect(['index']);
    }
    
    // 2. Obtener usuario_id desde el formulario
    $usuario_id = $request->post('usuario_id');
    if (!$usuario_id) {
        Yii::$app->session->setFlash('error', 'Faltan datos para reportar el usuario.');
        return $this->redirect(['reportar']);
    }
    
    // 3. Verificar si el usuario ha iniciado sesión
    if (Yii::$app->user->isGuest) {
        Yii::$app->session->setFlash('error', 'Debes iniciar sesión para reportar.');
        return $this->redirect(['site/login']);
    }
    
    $currentUserId = Yii::$app->user->id;
    
    // 4. Evitar que el usuario se reporte a sí mismo
    if ($usuario_id == $currentUserId) {
        Yii::$app->session->setFlash('error', 'No puedes reportarte a ti mismo.');
        return $this->redirect(['index']);
    }
    
    // 5. Verificar si ya existe un reporte previo del mismo usuario
    $existingReport = \app\models\ReportedUsers::find()
        ->where(['usuario_id' => $usuario_id, 'reporter_id' => $currentUserId])
        ->exists();
    
    if ($existingReport) {
        Yii::$app->session->setFlash('error', 'Ya has reportado a este usuario.');
        return $this->redirect(['index']);
    }
    
    // 6. Guardar el nuevo reporte en ReportedUsers
    $reportedUser = new \app\models\ReportedUsers();
    $reportedUser->usuario_id = $usuario_id;
    $reportedUser->reporter_id = $currentUserId;
    
    if (!$reportedUser->save()) {
        Yii::$app->session->setFlash('error', 'Error al guardar el reporte.');
        Yii::error("Error al guardar reporte: " . print_r($reportedUser->errors, true));
        return $this->redirect(['index']);
    }
    
    Yii::$app->session->setFlash('success', 'Reporte enviado correctamente.');
    
    // 7. Contar cuántos reportes tiene el usuario reportado
    $reportCount = \app\models\ReportedUsers::find()
        ->where(['usuario_id' => $usuario_id])
        ->count();
    
    Yii::debug("El usuario $usuario_id ha sido reportado $reportCount veces.");
    
    // 8. Si el usuario tiene más de 10 reportes, añadirlo a BannedUsuarios
    if ($reportCount >= 10) {
        $alreadyBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => $usuario_id])
            ->exists();
        
        if (!$alreadyBanned) {
            $bannedUser = new \app\models\BannedUsuarios();
            $bannedUser->usuario_id = $usuario_id;
            $bannedUser->at_time = new \yii\db\Expression('NOW()'); // Asigna la fecha actual automáticamente
            
            if (!$bannedUser->save()) {
                Yii::error("Error al banear usuario: " . print_r($bannedUser->errors, true));
            } else {
                //Correcto
            }
        }
    }
    
    return $this->redirect(['index']);
}


    public function actionComments($post_id)
    {
        $comments = Posts::find()
            ->where(['padre_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
            
        return $this->renderPartial('_comments', ['comments' => $comments]);
    }


    public function actionCreatePost()
{
    if (Yii::$app->user->isGuest) {
        Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
        return $this->redirect(['site/login']);
    }

    $modelPost = new Posts();
    $modelPost->usuario_id = Yii::$app->user->id; // ← Asignar usuario logueado

    if ($modelPost->load(Yii::$app->request->post())) {
        if ($modelPost->save()) {
            Yii::$app->session->setFlash('success', 'Post creado!');
            return $this->redirect(['index']);
        }
    }

    return $this->render('create-post', [
        'modelPost' => $modelPost,
    ]);
}

public function actionNotificaciones()
{
    if (Yii::$app->user->isGuest) {
        Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
        return $this->redirect(['site/login']);
    }

    $notificaciones = Notificaciones::find()
        ->where(['receptor_id' => Yii::$app->user->id])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();

    return $this->render('notificaciones', [
        'notificaciones' => $notificaciones
    ]);
}

// SiteController.php
public function actionRegister()
{
    $model = new Usuarios();

    if ($model->load(Yii::$app->request->post())) {
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Registro exitoso. Por favor inicie sesión.');
            return $this->redirect(['login']);
        } else {
            Yii::$app->session->setFlash('error', 'Error en el registro. Verifique los datos.');
        }
    }

    return $this->redirect(['login']);
}


    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionLogs()
    {
        return $this->render('logs');
    }

    public function actionLikeComment($id)
    {
        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Debes estar registrado para hacer miles de cosas asombrosas!'];
            }
            Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
            return $this->redirect(['site/login']);
        }

        $comment = \app\models\Posts::findOne($id);
        if ($comment) {
            $comment->updateCounters(['likes' => 1]);
            
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true, 'count' => $comment->likes];
            }
        }
        
        return $this->redirect(['index']);
    }

    public function actionDislikeComment($id)
    {
        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Debes estar registrado para hacer miles de cosas asombrosas!'];
            }
            Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
            return $this->redirect(['site/login']);
        }

        $comment = \app\models\Posts::findOne($id);
        if ($comment) {
            $comment->updateCounters(['dislikes' => 1]);
            
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true, 'count' => $comment->dislikes];
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * @param int $rolId
     * @return bool
     */
    protected function hasRole($rolId)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return Yii::$app->user->identity->rol_id == $rolId;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles especificados
     * @param array $rolIds
     * @return bool
     */
    protected function hasAnyRole($rolIds)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return in_array(Yii::$app->user->identity->rol_id, $rolIds);
    }

    /**
     * Acción para bloquear un post
     * @return \yii\web\Response
     */
    public function actionBanPost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $post_id = Yii::$app->request->post('post_id');
            
            // Validar que el post_id sea un número válido
            if (!is_numeric($post_id) || $post_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID de post inválido',
                    'type' => 'error'
                ];
            }

            if (!$this->hasAnyRole([1313, 1314, 1315])) {
                return [
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción',
                    'type' => 'error'
                ];
            }

            // Verificar si el post existe
            $post = Posts::findOne($post_id);
            if (!$post) {
                return [
                    'success' => false,
                    'message' => 'El post no existe',
                    'type' => 'error'
                ];
            }

            // Verificar si el usuario está intentando banear su propio post
            if ($post->usuario_id == Yii::$app->user->id) {
                return [
                    'success' => false,
                    'message' => 'No puedes bloquear tu propio post',
                    'type' => 'error'
                ];
            }

            // Verificar si el post ya está bloqueado
            if (BannedPosts::findOne(['post_id' => $post_id])) {
                return [
                    'success' => false,
                    'message' => 'Este post ya está bloqueado',
                    'type' => 'error'
                ];
            }

            $bannedPost = new BannedPosts();
            $bannedPost->post_id = $post_id;
            $bannedPost->motivo = 'MODERATED';
            $bannedPost->at_time = date('Y-m-d H:i:s');

            if ($bannedPost->save()) {
                return [
                    'success' => true,
                    'message' => 'Post bloqueado exitosamente',
                    'type' => 'success'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al bloquear el post: ' . implode(', ', $bannedPost->getErrorSummary(true)),
                'type' => 'error'
            ];
        } catch (\Exception $e) {
            Yii::error('Error en actionBanPost: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    /**
     * Acción para bloquear un usuario
     * @return \yii\web\Response
     */
    public function actionBanUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $usuario_id = Yii::$app->request->post('usuario_id');
            
            // Validar que el usuario_id sea un número válido
            if (!is_numeric($usuario_id) || $usuario_id <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID de usuario inválido',
                    'type' => 'error'
                ];
            }

            // Verificar si el usuario está intentando banearse a sí mismo
            if ($usuario_id == Yii::$app->user->id) {
                return [
                    'success' => false,
                    'message' => 'No puedes bloquear tu propia cuenta',
                    'type' => 'error'
                ];
            }

            if (!$this->hasAnyRole([1313, 1314, 1315])) {
                return [
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción',
                    'type' => 'error'
                ];
            }

            // Verificar si el usuario existe
            $usuario = Usuarios::findOne($usuario_id);
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'El usuario no existe',
                    'type' => 'error'
                ];
            }

            // Verificar si el usuario ya está bloqueado
            if (BannedUsuarios::findOne(['usuario_id' => $usuario_id])) {
                return [
                    'success' => false,
                    'message' => 'Este usuario ya está bloqueado',
                    'type' => 'error'
                ];
            }

            $bannedUser = new BannedUsuarios();
            $bannedUser->usuario_id = $usuario_id;
            $bannedUser->at_time = date('Y-m-d H:i:s');

            if ($bannedUser->save()) {
                return [
                    'success' => true,
                    'message' => 'Usuario bloqueado exitosamente',
                    'type' => 'success'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al bloquear el usuario: ' . implode(', ', $bannedUser->getErrorSummary(true)),
                'type' => 'error'
            ];
        } catch (\Exception $e) {
            Yii::error('Error en actionBanUser: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    /**
     * Acción para la gestión de contenido (posts y usuarios baneados)
     * @return string
     */
    public function actionGestionContenido()
    {
        if (!$this->hasAnyRole([1313, 1314, 1315])) {
            Yii::$app->session->setFlash('error', 'No tienes permisos para acceder a esta página.');
            return $this->redirect(['index']);
        }

        // Diccionario de motivos
        $motivos = [
            'HATE_LANG' => 'Lenguaje que incita al odio',
            'KIDS_HASSARAMENT' => 'Pedofilia',
            'SENSIBLE_CONTENT' => 'Contenido extremadamente sensible',
            'SCAM' => 'Estafa',
            'SPAM' => 'Spam',
            'RACIST_LANG' => 'Racismo o Xenofobia',
            'MODERATED' => 'Moderado a razón de un administrador'
        ];

        // Obtener posts baneados con información relacionada
        $postsBaneados = BannedPosts::find()
            ->with(['post.usuario'])
            ->all();

        // Obtener usuarios baneados con información relacionada
        $usuariosBaneados = BannedUsuarios::find()
            ->with(['usuario'])
            ->all();

        return $this->render('gestion-contenido', [
            'postsBaneados' => $postsBaneados,
            'usuariosBaneados' => $usuariosBaneados,
            'motivos' => $motivos
        ]);
    }

    /**
     * Acción para desbloquear un post
     * @return \yii\web\Response
     */
    public function actionDesbloquearPost()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Obtener el ID tanto de GET como de POST
            $id = Yii::$app->request->get('id') ?? Yii::$app->request->post('id');
            
            if (!$id) {
                return [
                    'success' => false,
                    'message' => 'ID no proporcionado',
                    'type' => 'error'
                ];
            }

            if (!$this->hasAnyRole([1313, 1314, 1315])) {
                return [
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción',
                    'type' => 'error'
                ];
            }

            $bannedPost = BannedPosts::findOne($id);
            if (!$bannedPost) {
                return [
                    'success' => false,
                    'message' => 'El post no está bloqueado',
                    'type' => 'error'
                ];
            }

            if ($bannedPost->delete()) {
                return [
                    'success' => true,
                    'message' => 'Post desbloqueado exitosamente',
                    'type' => 'success'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al desbloquear el post',
                'type' => 'error'
            ];
        } catch (\Exception $e) {
            Yii::error('Error en actionDesbloquearPost: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }

    /**
     * Acción para desbloquear un usuario
     * @return \yii\web\Response
     */
    public function actionDesbloquearUsuario()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            // Obtener el ID tanto de GET como de POST
            $id = Yii::$app->request->get('id') ?? Yii::$app->request->post('id');
            
            if (!$id) {
                return [
                    'success' => false,
                    'message' => 'ID no proporcionado',
                    'type' => 'error'
                ];
            }

            if (!$this->hasAnyRole([1313, 1314, 1315])) {
                return [
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción',
                    'type' => 'error'
                ];
            }

            $bannedUser = BannedUsuarios::findOne($id);
            if (!$bannedUser) {
                return [
                    'success' => false,
                    'message' => 'El usuario no está bloqueado',
                    'type' => 'error'
                ];
            }

            if ($bannedUser->delete()) {
                return [
                    'success' => true,
                    'message' => 'Usuario desbloqueado exitosamente',
                    'type' => 'success'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al desbloquear el usuario',
                'type' => 'error'
            ];
        } catch (\Exception $e) {
            Yii::error('Error en actionDesbloquearUsuario: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }
    }
}