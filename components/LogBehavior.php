<?php
namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use app\models\Logs;

class LogBehavior extends Behavior
{
    /**
     * Define los eventos a los que se va a suscribir este comportamiento.
     * En este caso, se suscribe al evento EVENT_BEFORE_ACTION de los controladores.
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'handleBeforeAction',
        ];
    }

    /**
     * Función que se ejecuta antes de cada acción.
     *
     * @param \yii\base\ActionEvent $event
     */
    public function handleBeforeAction($event)
    {
        $action = $event->action;
        
        // Crear una nueva instancia del modelo Logs
        $log = new Logs();
        
        // Obtener la IP del usuario
        $log->ip = Yii::$app->request->userIP;
        
        // Obtener la ubicación (puedes integrar una librería GeoIP aquí)
        $log->ubicacion = 'Ubicación calculada'; // Reemplaza con la lógica real
        
        // Registrar la acción (por ejemplo, site/login)
        $log->accion = $action->getUniqueId();
        
        // Estado inicial (podrías actualizarlo posteriormente en un afterAction)
        $log->status = 0;
        
        // Obtener el User Agent del usuario
        $log->useragent = Yii::$app->request->getUserAgent();
        
        // Obtener el sistema operativo a partir del User Agent
        $log->osver = $this->getOsFromUserAgent($log->useragent);
        
        // Guardar el registro en la base de datos (sin validación)
        $log->save(false);
    }

    /**
     * Método auxiliar para determinar el sistema operativo a partir del User Agent.
     *
     * @param string $userAgent
     * @return string
     */
    protected function getOsFromUserAgent($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac OS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            return 'iOS';
        }
        return 'Desconocido';
    }
}
