<?php

namespace app\components;

use yii\base\Behavior;
use yii\db\Exception;
use Yii;

class DatabaseErrorBehavior extends Behavior
{
    public function events()
    {
        return [
            'beforeAction' => 'beforeAction',
        ];
    }

    public function beforeAction($event)
    {
        try {
            // Intenta hacer una consulta simple para verificar la conexión
            Yii::$app->db->createCommand('SELECT 1')->queryOne();
        } catch (Exception $e) {
            // Si hay un error de base de datos, redirige a la página de error
            Yii::$app->response->redirect(['error/database-error'])->send();
            return false;
        }
        return true;
    }
} 