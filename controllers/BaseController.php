<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Logs;

class BaseController extends Controller
{
    /**
     * Propiedad para almacenar el registro del log y actualizarlo en afterAction.
     *
     * @var Logs
     */
    protected $logRecord;

    /**
     * Se ejecuta antes de cada acción.
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Crear una nueva instancia del modelo Logs
        $log = new Logs();

        // Obtener la IP del usuario
        $ip = Yii::$app->request->userIP;
        $log->ip = $ip;

        //Obtener el id del usuario
        $log->usuario_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : 0;

        // Obtener la ubicación a partir de una API externa
        $log->ubicacion = $this->getLocationFromApi($ip);

        // Registrar la acción actual (por ejemplo, site/login)
        $log->accion = $action->getUniqueId();

        // Estado inicial (0 por defecto; se actualizará en afterAction)
        $log->status = 0;

        // Obtener el User Agent del usuario
        $userAgent = Yii::$app->request->getUserAgent();
        $log->useragent = $userAgent;

        // Determinar el sistema operativo a partir del User Agent
        $log->osver = $this->getOsFromUserAgent($userAgent);

        // Guardar el registro en la base de datos (sin validación adicional)
        $log->save(false);

        // Guardamos el log para actualizarlo posteriormente
        $this->logRecord = $log;

        return true;
    }

    /**
     * Se ejecuta después de cada acción.
     *
     * @param \yii\base\Action $action
     * @param mixed $result Resultado de la acción
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        // Si la acción se ejecutó correctamente, actualizamos el status a 1 (éxito)
        // Aquí podrías incluir lógica adicional para casos específicos, por ejemplo:
        // if ($action->id == 'login') { ... }
        if ($this->logRecord) {
            $this->logRecord->status = 1;
            $this->logRecord->save(false);
        }

        return parent::afterAction($action, $result);
    }

    /**
     * Consulta una API externa para obtener la ubicación a partir de la IP.
     *
     * @param string $ip
     * @return string
     */
    protected function getLocationFromApi($ip)
    {
        // URL de la API: ip-api.com devuelve un JSON con datos de ubicación
        $url = "http://ip-api.com/json/{$ip}";
        
        try {
            // Se utiliza file_get_contents para hacer la consulta
            $response = file_get_contents($url);
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    $city = isset($data['city']) ? $data['city'] : '';
                    $country = isset($data['country']) ? $data['country'] : '';
                    return ($city ? $city . ', ' : '') . $country;
                }
            }
        } catch (\Exception $e) {
            // En caso de error, se retorna un mensaje por defecto
        }
        
        return 'Ubicación no disponible';
    }

    /**
     * Determina el sistema operativo a partir del User Agent.
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
