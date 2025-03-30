<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

class ErrorController extends Controller
{
    public function actionDatabaseError()
    {
        \Yii::$app->response->format = Response::FORMAT_HTML;
        return $this->renderPartial('database-error');
    }
} 