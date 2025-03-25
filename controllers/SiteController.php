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
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'like' => ['post'], // <--- Añadir esta línea
                    'dislike' => ['post'], // <--- Añadir esta línea
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
{
    $posts = Posts::find()
        ->where(['padre_id' => null])
        ->with(['usuario', 'posts.usuario'])
        ->orderBy(['created_at' => SORT_DESC])
        ->all();

    // Crear nueva instancia del modelo para el formulario
    $modelComentario = new Posts();

    return $this->render('index', [
        'posts' => $posts,
        'modelComentario' => $modelComentario, // Pasar el modelo a la vista
    ]);
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

    public function actionLike($id)
{
    if (Yii::$app->user->isGuest) {
        Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
        return $this->redirect(['site/login']);
    }

    $post = \app\models\Posts::findOne($id);
    if ($post) {
        $post->updateCounters(['likes' => 1]);
    }
    $modalId = Yii::$app->request->get('modal');
    return $this->redirect(['index', 'modal' => $modalId]);
}

public function actionDislike($id)
{
    if (Yii::$app->user->isGuest) {
        Yii::$app->session->setFlash('error', 'Debes estar registrado para hacer miles de cosas asombrosas!');
        return $this->redirect(['site/login']);
    }

    $post = \app\models\Posts::findOne($id);
    if ($post) {
        $post->updateCounters(['dislikes' => 1]);
    }
    $modalId = Yii::$app->request->get('modal');
    return $this->redirect(['index', 'modal' => $modalId]);
}

    public function actionComment($post_id)
    {
        if (Yii::$app->user->isGuest) {
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

            return $this->redirect(['index', 'modal' => $originalPost->id]);
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
}