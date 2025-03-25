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