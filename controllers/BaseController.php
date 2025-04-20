<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use app\models\Logs;
use app\models\ReportedUsers;

class BaseController extends Controller
{
    protected $logRecord;

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $log = new Logs();
        $ip = Yii::$app->request->userIP;
        $log->ip = $ip;
        $log->usuario_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
        $log->ubicacion = $this->getLocationFromApi($ip);
        $log->accion = $action->getUniqueId();
        $log->status = 0;
        $userAgent = Yii::$app->request->getUserAgent();
        $log->useragent = $userAgent;
        $log->osver = $this->getOsFromUserAgent($userAgent);
        $log->save(false);

        $this->logRecord = $log;
        return true;
    }

    public function afterAction($action, $result)
{
    if (!Yii::$app->user->isGuest) {
        $userId = Yii::$app->user->id;
        $isBanned = \app\models\BannedUsuarios::find()->where(['usuario_id' => $userId])->exists();

        if ($isBanned) {
            Yii::$app->user->logout();
            throw new \yii\web\ForbiddenHttpException('⚠️ Has sido baneado por la comunidad permanentemente por uso inadecuado. ⚠️');
        }
    }

    if ($this->logRecord) {
        $this->logRecord->status = 1;
        $this->logRecord->save(false);
    }

    return parent::afterAction($action, $result);
}


    protected function getLocationFromApi($ip)
    {
        $url = "http://ip-api.com/json/{$ip}";
        try {
            $response = file_get_contents($url);
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    $city = isset($data['city']) ? $data['city'] : '';
                    $country = isset($data['country']) ? $data['country'] : '';
                    return ($city ? $city . ', ' : '') . $country;
                }
            }
        } catch (\Exception $e) {}
        return 'Ubicación no disponible';
    }

    protected function getOsFromUserAgent($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'Mac OS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';
        return 'Desconocido';
    }
    
    /**
     * Determina si el dispositivo actual es móvil basado en el User Agent
     * 
     * @return boolean
     */
    protected function isMobile()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        return (
            strpos($userAgent, 'Android') !== false ||
            strpos($userAgent, 'iPhone') !== false ||
            strpos($userAgent, 'iPad') !== false ||
            strpos($userAgent, 'Mobile') !== false ||
            strpos($userAgent, 'webOS') !== false ||
            strpos($userAgent, 'BlackBerry') !== false ||
            strpos($userAgent, 'iPod') !== false ||
            strpos($userAgent, 'Opera Mini') !== false
        );
    }
    
    /**
     * Registra una acción en el log del sistema
     * 
     * @param string $accion Tipo de acción a registrar
     * @param string $detalles Detalles adicionales de la acción
     * @return boolean Si se guardó correctamente el log
     */
    protected function registrarLog($accion, $detalles = '')
    {
        $log = new Logs();
        $ip = Yii::$app->request->userIP;
        $log->ip = $ip;
        $log->usuario_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
        $log->ubicacion = $this->getLocationFromApi($ip);
        $log->accion = $accion;
        $log->details = $detalles;
        $log->status = 1;
        $userAgent = Yii::$app->request->getUserAgent();
        $log->useragent = $userAgent;
        $log->osver = $this->getOsFromUserAgent($userAgent);
        return $log->save(false);
    }
}
