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
use yii\helpers\HtmlPurifier;
use yii\web\NotFoundHttpException;

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
     * @return string|json
     */
    public function actionIndex()
    {
        // Redireccionar a la vista móvil si es un dispositivo móvil
        if ($this->isMobile()) {
            return $this->redirect(['/mobile']);
        }
        
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
            
            // Construir consulta base para los posts
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
            
            // Obtener el número total de posts para la paginación
            $totalPosts = $query->count();
            $totalPages = ceil($totalPosts / $perPage);

            // Si es una solicitud AJAX, devolver solo los posts de la página solicitada
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::info('Solicitud AJAX recibida para cargar la página ' . $page, 'app');
                
                // Asegurarse de que la página solicitada sea válida
                if ($page > $totalPages) {
                    return [
                        'success' => false,
                        'message' => 'No hay más posts disponibles',
                        'hasMore' => false
                    ];
                }
                
                // Obtener los posts para la página solicitada
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
                
                // Renderizar los posts 
                $html = '';
                foreach ($posts as $post) {
                    $html .= $this->renderPartial('_partials/_post_card', [
                        'post' => $post
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
                'totalPosts' => $totalPosts,
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

        // Redireccionar a la vista móvil si es un dispositivo móvil
        if ($this->isMobile()) {
            return $this->redirect(['/mobile/login']);
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

    /**
     * Verifica el nivel de suscripción del usuario y devuelve los permisos correspondientes
     * 
     * @return Response JSON con información sobre permisos
     */
    public function actionCheckSubscription()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Valores predeterminados
        $maxChars = 480;  // Límite predeterminado de caracteres
        $maxImages = 1;   // Límite predeterminado de imágenes
        $debugInfo = [];
        
        if (Yii::$app->user->isGuest) {
            // Si el usuario no está autenticado, devolver límites básicos
            return [
                'success' => true,
                'maxChars' => $maxChars,
                'maxImages' => $maxImages,
                'debug' => 'Usuario no autenticado, usando valores predeterminados'
            ];
        }
        
        try {
            // Buscar información del usuario
            $userId = Yii::$app->user->id;
            $usuario = Usuarios::findOne($userId);
            
            if ($usuario) {
                // Verificar si tiene una suscripción activa
                $suscripcionActiva = false;
                $estadoSuscripcion = 0; // Por defecto, sin suscripción
                $suscripcion = \app\models\UsersSuscriptions::find()
                    ->where(['usuario_id' => $userId])
                    ->andWhere(['>=', 'fecha_fin', date('Y-m-d')]) // Verifica que no haya vencido
                    ->one();
                
                if ($suscripcion) {
                    $estadoSuscripcion = $suscripcion->activo; // 0: inactiva, 1: activa, 2: morosa
                    
                    if ($estadoSuscripcion == 1) {
                        $suscripcionActiva = true;
                        $debugInfo['suscripcion'] = 'Activa, ID: ' . $suscripcion->id;
                    } else if ($estadoSuscripcion == 2) {
                        $debugInfo['suscripcion'] = 'Morosa, ID: ' . $suscripcion->id;
                    } else {
                        $debugInfo['suscripcion'] = 'Inactiva, ID: ' . $suscripcion->id;
                    }
                    
                    $debugInfo['estado_suscripcion'] = $estadoSuscripcion;
                    $debugInfo['fecha_fin'] = $suscripcion->fecha_fin;
                } else {
                    $debugInfo['suscripcion'] = 'No encontrada';
                }
                
                // Obtener nivel de suscripción del usuario
                $subsLevel = $usuario->subs_level;
                $debugInfo['subs_level'] = $subsLevel;
                
                // Buscar accesos asociados a la suscripción
                $access = \app\models\Access::findOne(['subs_id' => $subsLevel]);
                
                if ($access) {
                    // Si tiene acceso y suscripción activa, usar límites completos
                    if ($suscripcionActiva) {
                        $maxChars = $access->getMaxCaracteres();
                        $maxImages = $access->getNumeroImagenes();
                        $debugInfo['origen_limites'] = 'Desde nivel de suscripción activa';
                    } else {
                        // Sin suscripción activa, usar límites básicos
                        $maxChars = 480;
                        $maxImages = 1;
                        $debugInfo['origen_limites'] = 'Suscripción inactiva, usando límites básicos';
                    }
                } else {
                    $debugInfo['access'] = 'No se encontraron accesos para el nivel: ' . $subsLevel;
                }
            } else {
                $debugInfo['usuario'] = 'No encontrado ID: ' . $userId;
            }
            
            return [
                'success' => true,
                'maxChars' => $maxChars,
                'maxImages' => $maxImages,
                'debug' => $debugInfo
            ];
            
        } catch (\Exception $e) {
            Yii::error('Error en checkSubscription: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al verificar la suscripción',
                'debug' => $e->getMessage()
            ];
        }
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
                return ['success' => false, 'message' => 'Debes iniciar sesión para dar like'];
            }
            Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
            return $this->redirect(['site/login']);
        }

        // Verificar si el usuario está baneado
        $isBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->exists();
            
        if ($isBanned) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Tu cuenta ha sido baneada por violar las normas de la comunidad'];
            }
            Yii::$app->session->setFlash('error', 'Tu cuenta ha sido baneada por violar las normas de la comunidad');
            return $this->redirect(['index']);
        }

        $post = \app\models\Posts::findOne($id);
        if (!$post) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'El post no existe'];
            }
            Yii::$app->session->setFlash('error', 'El post no existe');
            return $this->redirect(['index']);
        }

        // Verificar si el post está baneado
        $isPostBanned = \app\models\BannedPosts::find()
            ->where(['post_id' => $id])
            ->exists();
            
        if ($isPostBanned) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Este post ha sido baneado por violar las normas de la comunidad'];
            }
            Yii::$app->session->setFlash('error', 'Este post ha sido baneado por violar las normas de la comunidad');
            return $this->redirect(['index']);
        }
        
        $post->updateCounters(['likes' => 1]);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true, 
                'likes' => $post->likes,
                'count' => $post->likes,
                'id' => $post->id
            ];
        }
        
        $modalId = Yii::$app->request->get('modal');
        return $this->redirect(['index', 'modal' => $modalId]);
    }

    public function actionDislike($id)
    {
        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Debes iniciar sesión para dar dislike'];
            }
            Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
            return $this->redirect(['site/login']);
        }

        // Verificar si el usuario está baneado
        $isBanned = \app\models\BannedUsuarios::find()
            ->where(['usuario_id' => Yii::$app->user->id])
            ->exists();
            
        if ($isBanned) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Tu cuenta ha sido baneada por violar las normas de la comunidad'];
            }
            Yii::$app->session->setFlash('error', 'Tu cuenta ha sido baneada por violar las normas de la comunidad');
            return $this->redirect(['index']);
        }

        $post = \app\models\Posts::findOne($id);
        if (!$post) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'El post no existe'];
            }
            Yii::$app->session->setFlash('error', 'El post no existe');
            return $this->redirect(['index']);
        }

        // Verificar si el post está baneado
        $isPostBanned = \app\models\BannedPosts::find()
            ->where(['post_id' => $id])
            ->exists();
            
        if ($isPostBanned) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Este post ha sido baneado por violar las normas de la comunidad'];
            }
            Yii::$app->session->setFlash('error', 'Este post ha sido baneado por violar las normas de la comunidad');
            return $this->redirect(['index']);
        }
        
        $post->updateCounters(['dislikes' => 1]);
        
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true, 
                'dislikes' => $post->dislikes,
                'count' => $post->dislikes,
                'id' => $post->id
            ];
        }
        
        $modalId = Yii::$app->request->get('modal');
        return $this->redirect(['index', 'modal' => $modalId]);
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
    
    // Obtener parámetros de filtro
    $accion = \Yii::$app->request->get('accion', '');
    $fecha = \Yii::$app->request->get('fecha', 'semana');
    $page = (int)\Yii::$app->request->get('page', 1);
    $itemsPerPage = (int)\Yii::$app->request->get('items_per_page', 25);
    
    // Construir query base
    $query = \app\models\Logs::find()->orderBy(['fecha_hora' => SORT_DESC]);
    
    // Aplicar filtro de acción
    if (!empty($accion)) {
        $query->andWhere(['like', 'accion', $accion]);
    }
    
    // Aplicar filtro de fecha
    switch ($fecha) {
        case 'hoy':
            $query->andWhere('DATE(fecha_hora) = CURDATE()');
            break;
        case 'semana':
            $query->andWhere('fecha_hora >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
            break;
        case 'mes':
            $query->andWhere('fecha_hora >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
            break;
        // 'todo' no aplica filtro
    }
    
    // Contar total de registros para paginación
    $total = $query->count();
    
    // Aplicar paginación
    $offset = ($page - 1) * $itemsPerPage;
    $logs = $query->offset($offset)->limit($itemsPerPage)->all();
    
    // Preparar datos para la respuesta
    $data = [];
    foreach ($logs as $log) {
        $data[] = [
            'id'        => $log->id,
            'ip'        => $log->ip,
            'ubicacion' => $log->ubicacion,
            'accion'    => $log->accion,
            'status'    => $log->status,
            'fecha'     => $log->fecha_hora,
            'useragent' => $log->useragent,
            'usuario'   => $log->usuario ? $log->usuario->user : null
        ];
    }
    
    // Preparar estadísticas
    $stats = [
        'total' => $total,
        'logins_hoy' => \app\models\Logs::find()
            ->where(['like', 'accion', 'login'])
            ->andWhere('DATE(fecha_hora) = CURDATE()')
            ->count(),
        'posts_hoy' => \app\models\Logs::find()
            ->where(['like', 'accion', 'post'])
            ->andWhere('DATE(fecha_hora) = CURDATE()')
            ->count(),
        'comentarios_hoy' => \app\models\Logs::find()
            ->where(['like', 'accion', 'comment'])
            ->andWhere('DATE(fecha_hora) = CURDATE()')
            ->count()
    ];
    
    // Retornar respuesta completa
    return [
        'success' => true,
        'logs' => $data,
        'stats' => $stats,
        'total' => $total,
        'page' => $page
    ];
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


    /**
     * Crea un nuevo post
     * 
     * @return mixed Renderiza la vista en GET, devuelve JSON en POST
     */
    public function actionCreatePost()
    {
        // Si es una solicitud GET, mostrar el formulario
        if (Yii::$app->request->isGet) {
            $modelPost = new Posts();
            
            // Si el usuario está autenticado, intentar pre-llenar algunos campos
            if (!Yii::$app->user->isGuest) {
                $modelPost->usuario_id = Yii::$app->user->id;
            }
            
            return $this->render('create-post', [
                'modelPost' => $modelPost,
            ]);
        }
        
        // Si es una solicitud POST, procesar los datos del formulario
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modelPost = new Posts();
        $access = null;
        $maxImages = 0;
        $maxChars = 480; // Valor predeterminado de caracteres

        if (Yii::$app->request->isPost) {
            try {
                // Cargar datos del formulario
                $modelPost->load(Yii::$app->request->post());
                
                // Verificar permisos de usuario para imágenes si está autenticado
                if (!Yii::$app->user->isGuest) {
                    $usuario = Usuarios::findOne(Yii::$app->user->id);
                    $modelPost->usuario_id = $usuario->id;
                    
                    if ($usuario) {
                        // Primero verificamos si tiene una suscripción activa
                        $suscripcionActiva = false;
                        $estadoSuscripcion = 0; // Por defecto, sin suscripción
                        $suscripcion = \app\models\UsersSuscriptions::find()
                            ->where(['usuario_id' => $usuario->id])
                            ->andWhere(['>=', 'fecha_fin', date('Y-m-d')]) // Verifica que no haya vencido
                            ->one();
                            
                        if ($suscripcion) {
                            $estadoSuscripcion = $suscripcion->activo; // 0: inactiva, 1: activa, 2: morosa
                            if ($estadoSuscripcion == 1) {
                                $suscripcionActiva = true;
                            }
                        }
                        
                        // Obtener nivel de suscripción
                        $subsLevel = $usuario->subs_level;
                        
                        // Obtener accesos de la suscripción
                        $access = \app\models\Access::findOne(['subs_id' => $subsLevel]);
                        if ($access) {
                            // Si tiene suscripción activa, usa los límites completos
                            if ($suscripcionActiva) {
                                $maxImages = $access->getNumeroImagenes();
                                $maxChars = $access->getMaxCaracteres();
                            } else {
                                // Sin suscripción activa o con suscripción morosa, limitar a 480 caracteres y 1 imagen
                                $maxImages = 1;
                                $maxChars = 480;
                            }
                        } else {
                            // Por defecto, sin permisos especiales
                            $maxImages = 1;
                            $maxChars = 480;
                        }
                    }
                }
                
                // Validar longitud del contenido
                if (strlen($modelPost->contenido) > $maxChars) {
                    return [
                        'success' => false,
                        'message' => "El contenido no puede exceder los {$maxChars} caracteres con tu suscripción actual.",
                    ];
                }
                
                // Validar y guardar el post
                if ($modelPost->validate()) {
                    // Procesar imágenes si hay permisos
                    $modelPost->imageFiles = \yii\web\UploadedFile::getInstances($modelPost, 'imageFiles');
                    
                    // Verificar límite de imágenes
                    if (count($modelPost->imageFiles) > $maxImages) {
                        return [
                            'success' => false,
                            'message' => "Solo puedes adjuntar un máximo de {$maxImages} imágenes con tu suscripción actual.",
                        ];
                    }
                    
                    // Guardar el post
                    if ($modelPost->save()) {
                        // Guardar imágenes si hay
                        $modelPost->saveImages();
                        
                        return [
                            'success' => true,
                            'message' => 'Tu chisme se ha publicado correctamente',
                            'redirectUrl' => Yii::$app->urlManager->createUrl(['site/index']),
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Error al guardar el post: ' . implode(', ', $modelPost->getFirstErrors()),
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'message' => 'Por favor, corrige los errores: ' . implode(', ', $modelPost->getFirstErrors()),
                    ];
                }
            } catch (\Exception $e) {
                Yii::error('Error en la creación del post: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Método no permitido',
        ];
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

    /**
     * Marca todas las notificaciones del usuario como leídas
     * @return \yii\web\Response
     */
    public function actionMarcarNotificacionesLeidas()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
            return $this->redirect(['site/login']);
        }
        
        // Actualizar todas las notificaciones no leídas del usuario
        Notificaciones::updateAll(
            ['leido' => true],
            [
                'receptor_id' => Yii::$app->user->id,
                'leido' => false
            ]
        );
        
        Yii::$app->session->setFlash('success', 'Todas las notificaciones han sido marcadas como leídas');
        return $this->redirect(['notificaciones']);
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
     * @param integer $id ID del post a banear
     * @return \yii\web\Response
     */
    public function actionBanPost($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $post_id = $id;
            $motivo = Yii::$app->request->post('motivo', 'MODERATED');
            
            // Validar que el motivo sea uno de los permitidos
            $motivosPermitidos = ['HATE_LANG', 'KIDS_HASSARAMENT', 'SENSIBLE_CONTENT', 'SCAM', 'SPAM', 'RACIST_LANG', 'MODERATED'];
            if (!in_array($motivo, $motivosPermitidos)) {
                $motivo = 'MODERATED'; // Si no es un motivo válido, usar el default
            }
            
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
            $bannedPost->motivo = $motivo;
            $bannedPost->at_time = date('Y-m-d H:i:s');

            if ($bannedPost->save()) {
                // Registrar en el log
                $this->registrarLog('ban-post', 'Bloqueo de post #' . $post_id . ' con motivo: ' . $motivo);
                
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
     * @param integer $id ID del usuario a banear
     * @return \yii\web\Response
     */
    public function actionBanUser($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $usuario_id = $id;
            
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
                // Registrar en el log
                $this->registrarLog('ban-user', 'Bloqueo de usuario #' . $usuario_id);
                
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

        return $this->render('admin-contenido', [
            'postsBaneados' => $postsBaneados,
            'usuariosBaneados' => $usuariosBaneados,
            'motivos' => $motivos
        ]);
    }

    /**
     * Acción para la administración de usuarios
     * @return string
     */
    public function actionAdminUsuarios()
    {
        if (!$this->hasAnyRole([1313, 1314, 1315])) {
            Yii::$app->session->setFlash('error', 'No tienes permisos para acceder a esta página.');
            return $this->redirect(['index']);
        }

        // Obtener todos los usuarios
        $usuarios = Usuarios::find()->all();

        return $this->render('admin-usuarios', [
            'usuarios' => $usuarios
        ]);
    }

    /**
     * Acción para cambiar el rol de un usuario
     * @return \yii\web\Response
     */
    public function actionCambiarRol()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->hasAnyRole([1313, 1314, 1315])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción',
                'type' => 'error'
            ];
        }

        $usuario_id = Yii::$app->request->post('usuario_id');
        $nuevo_rol = Yii::$app->request->post('rol_id');

        // Validar que el rol sea uno de los permitidos
        $roles_permitidos = [1313, 1314, 1315, 1316];
        if (!in_array($nuevo_rol, $roles_permitidos)) {
            return [
                'success' => false,
                'message' => 'Rol no válido',
                'type' => 'error'
            ];
        }

        $usuario = Usuarios::findOne($usuario_id);
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado',
                'type' => 'error'
            ];
        }

        $usuario->rol_id = $nuevo_rol;
        if ($usuario->save()) {
            return [
                'success' => true,
                'message' => 'Rol actualizado exitosamente',
                'type' => 'success'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al actualizar el rol',
            'type' => 'error'
        ];
    }

    /**
     * Acción para eliminar un usuario
     * @return \yii\web\Response
     */
    public function actionEliminarUsuario()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->hasAnyRole([1313, 1314, 1315])) {
            return [
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción',
                'type' => 'error'
            ];
        }

        $usuario_id = Yii::$app->request->post('usuario_id');

        $usuario = Usuarios::findOne($usuario_id);
        if (!$usuario) {
            return [
                'success' => false,
                'message' => 'Usuario no encontrado',
                'type' => 'error'
            ];
        }

        // No permitir eliminar el propio usuario
        if ($usuario_id == Yii::$app->user->id) {
            return [
                'success' => false,
                'message' => 'No puedes eliminar tu propia cuenta',
                'type' => 'error'
            ];
        }

        if ($usuario->delete()) {
            return [
                'success' => true,
                'message' => 'Usuario eliminado exitosamente',
                'type' => 'success'
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al eliminar el usuario',
            'type' => 'error'
        ];
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

    /**
     * Obtiene palabras clave para autocompletado
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
     * Acción para cargar más posts con scroll infinito
     */
    public function actionLoadMorePosts()
    {
        // Establecer formato de respuesta
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Depuración
        Yii::info('Ejecutando actionLoadMorePosts en SiteController', 'app');
        
        try {
            // Aceptar tanto POST como GET
            $page = Yii::$app->request->get('page', Yii::$app->request->post('page', 1));
            $pageSize = 10; // Número de posts por página
            $offset = ($page - 1) * $pageSize;
            
            // Incluir el archivo de helpers
            require_once(Yii::getAlias('@app/views/site/_partials/_helpers.php'));
            
            // Depuración
            Yii::info('Cargando página: ' . $page . ', offset: ' . $offset, 'app');
            
            // Construir la consulta
            $query = Posts::find()
                ->where(['padre_id' => null])
                ->with(['usuario']) // Cargar relación usuario para optimizar
                ->orderBy(['created_at' => SORT_DESC]);
            
            // Obtener el total para calcular si hay más páginas
            $totalPosts = $query->count();
            $totalPages = ceil($totalPosts / $pageSize);
                
            // Aplicar paginación
            $posts = $query->offset($offset)
                ->limit($pageSize)
                ->all();
            
            // Depuración
            Yii::info('Posts encontrados: ' . count($posts), 'app');
            
            if (empty($posts)) {
                Yii::info('No se encontraron más posts', 'app');
                return [
                    'success' => true,
                    'html' => '',
                    'hasMore' => false,
                    'message' => 'No hay más posts disponibles'
                ];
            }
            
            // Verificar posts baneados
            $motivos = [
                'HATE_LANG' => 'Lenguaje que incita al odio',
                'KIDS_HASSARAMENT' => 'Pedofilia',
                'SENSIBLE_CONTENT' => 'Contenido extremadamente sensible',
                'SCAM' => 'Estafa',
                'SPAM' => 'Spam',
                'RACIST_LANG' => 'Racismo o Xenofobia',
                'MODERATED' => 'Moderado a razón de un administrador'
            ];
            
            foreach ($posts as $post) {
                $bannedPost = BannedPosts::findOne(['post_id' => $post->id]);
                if ($bannedPost) {
                    $post->contenido = "Este post ha sido bloqueado debido a: " . $motivos[$bannedPost->motivo];
                }
            }
            
            // Renderizar los posts
            $html = '';
            foreach ($posts as $post) {
                $html .= $this->renderPartial('_partials/_post_card', [
                    'post' => $post
                ]);
            }
            
            // Depuración
            Yii::info('HTML generado: ' . strlen($html) . ' caracteres', 'app');
            
            return [
                'success' => true,
                'html' => $html,
                'hasMore' => ($page < $totalPages),
                'message' => 'Posts cargados correctamente',
                'currentPage' => $page,
                'totalPages' => $totalPages
            ];
        } catch (\Exception $e) {
            Yii::error('Error en actionLoadMorePosts: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Error al cargar más posts: ' . $e->getMessage(),
                'html' => '',
                'hasMore' => false
            ];
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
        
        // Obtener el ID del comentario a resaltar si existe
        $commentId = Yii::$app->request->get('comment_id');
        
        return $this->render('comentarios', [
            'post' => $post,
            'comentarios' => $comentarios,
            'commentId' => $commentId, // Pasar el ID del comentario a resaltar
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
     * Acción para comentar en un post o responder a un comentario
     * 
     * @return Response JSON con el resultado de la operación
     */
    public function actionComentar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!Yii::$app->request->isPost) {
            return [
                'success' => false,
                'message' => 'Método no permitido'
            ];
        }
        
        try {
            // Verificar si el usuario está autenticado
            if (Yii::$app->user->isGuest) {
                return [
                    'success' => false,
                    'message' => 'Debes iniciar sesión para comentar',
                    'redirect' => Yii::$app->urlManager->createUrl(['site/login'])
                ];
            }
            
            // Verificar si el usuario está baneado
            $isBanned = BannedUsuarios::find()
                ->where(['usuario_id' => Yii::$app->user->id])
                ->exists();
                
            if ($isBanned) {
                return [
                    'success' => false,
                    'message' => 'Tu cuenta ha sido baneada por violar las normas de la comunidad'
                ];
            }
            
            // Obtener datos del formulario
            $postData = Yii::$app->request->post('Posts');
            $padreId = null;
            $contenido = null;
            $age = null;
            $genre = 0;
            
            // Verificar los datos del formulario
            if (!empty($postData) && isset($postData['contenido'])) {
                // Los datos vienen en formato Posts[campo]
                $contenido = trim($postData['contenido']);
                $padreId = $postData['padre_id'] ?? null;
                $age = $postData['age'] ?? null;
                $genre = $postData['genre'] ?? 0;
            } else {
                // Los datos vienen directamente como campos individuales
                $contenido = trim(Yii::$app->request->post('contenido', ''));
                $padreId = Yii::$app->request->post('padre_id');
                $age = Yii::$app->request->post('age');
                $genre = Yii::$app->request->post('genre', 0);
            }
            
            // Validar el contenido
            if (empty($contenido)) {
                return [
                    'success' => false,
                    'message' => 'El contenido del comentario no puede estar vacío'
                ];
            }
            
            // Verificar si el padre existe (post o comentario)
            if (empty($padreId)) {
                return [
                    'success' => false,
                    'message' => 'Falta el ID del post o comentario padre'
                ];
            }
            
            $padre = Posts::findOne($padreId);
            if (!$padre) {
                return [
                    'success' => false,
                    'message' => 'El post o comentario al que intentas responder no existe'
                ];
            }
            
            // Verificar permisos según suscripción
            $maxChars = 480; // Valor predeterminado
            
            // Si el usuario está autenticado, verificar su nivel de suscripción
            $usuario = Usuarios::findOne(Yii::$app->user->id);
            if ($usuario) {
                // Primero verificamos si tiene una suscripción activa
                $suscripcionActiva = false;
                $suscripcion = \app\models\UsersSuscriptions::find()
                    ->where(['usuario_id' => $usuario->id])
                    ->andWhere(['>=', 'fecha_fin', date('Y-m-d')]) // Verifica que no haya vencido
                    ->one();
                    
                if ($suscripcion && $suscripcion->activo == 1) {
                    $suscripcionActiva = true;
                }
                
                // Obtener nivel de suscripción
                $subsLevel = $usuario->subs_level;
                
                // Obtener accesos de la suscripción
                $access = \app\models\Access::findOne(['subs_id' => $subsLevel]);
                if ($access && $suscripcionActiva) {
                    $maxChars = $access->getMaxCaracteres();
                }
            }
            
            // Validar longitud según suscripción
            if (mb_strlen($contenido) > $maxChars) {
                return [
                    'success' => false,
                    'message' => "El contenido no puede exceder los {$maxChars} caracteres con tu suscripción actual."
                ];
            }
            
            // Crear nuevo comentario
            $comentario = new Posts();
            $comentario->usuario_id = Yii::$app->user->id;
            $comentario->padre_id = $padreId;
            $comentario->contenido = $contenido;
            $comentario->age = $age; // Puede ser null
            $comentario->genre = $genre;
            $comentario->likes = 0;
            $comentario->dislikes = 0;
            
            // Guardar el comentario
            if (!$comentario->save()) {
                Yii::error('Error al guardar comentario: ' . json_encode($comentario->getErrors()));
                return [
                    'success' => false,
                    'message' => 'Error al guardar el comentario: ' . implode(', ', $comentario->getFirstErrors())
                ];
            }
            
            // Encontrar el post principal (para las notificaciones)
            $postPrincipal = $padre;
            while ($postPrincipal->padre_id !== null) {
                $postPrincipal = Posts::findOne($postPrincipal->padre_id);
            }
            
            // Notificar al autor del comentario/post padre (directo) si no es el mismo usuario
            if ($padre->usuario_id != Yii::$app->user->id) {
                $notificacion = new Notificaciones();
                $notificacion->receptor_id = $padre->usuario_id;
                $notificacion->post_original_id = $postPrincipal->id;
                $notificacion->comentario_id = $comentario->id;
                $notificacion->leido = false; // Inicializar como no leído
                $notificacion->save();
            }
            
            // Si el comentario es en respuesta a otro comentario (no al post principal),
            // también notificar al autor del post principal si es diferente
            if ($padre->padre_id !== null && $postPrincipal->usuario_id != Yii::$app->user->id && 
                $postPrincipal->usuario_id != $padre->usuario_id) {
                $notificacion = new Notificaciones();
                $notificacion->receptor_id = $postPrincipal->usuario_id;
                $notificacion->post_original_id = $postPrincipal->id;
                $notificacion->comentario_id = $comentario->id;
                $notificacion->leido = false; // Inicializar como no leído
                $notificacion->save();
            }
            
            return [
                'success' => true,
                'message' => 'Comentario publicado exitosamente',
                'comentario' => [
                    'id' => $comentario->id,
                    'contenido' => $comentario->contenido,
                ]
            ];
            
        } catch (\Exception $e) {
            Yii::error('Error en comentar: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ];
        }
    }
}