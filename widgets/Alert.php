<?php

namespace app\widgets;

use Yii;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Alert extends \yii\bootstrap5\Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - key: the name of the session flash variable
     * - value: the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'text-danger',
        'danger'  => 'text-danger',
        'success' => 'text-success',
        'info'    => 'text-info',
        'warning' => 'text-warning'
    ];

    /**
     * @var array header icons for each type of alert
     */
    public $iconTypes = [
        'error'   => '<i class="fas fa-exclamation-circle me-2"></i>',
        'danger'  => '<i class="fas fa-exclamation-circle me-2"></i>',
        'success' => '<i class="fas fa-check-circle me-2"></i>',
        'info'    => '<i class="fas fa-info-circle me-2"></i>',
        'warning' => '<i class="fas fa-exclamation-triangle me-2"></i>'
    ];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $session = Yii::$app->session;
        $flashes = [];
        $js = '';

        foreach (array_keys($this->alertTypes) as $type) {
            $flash = $session->getFlash($type);
            if (!empty($flash)) {
                foreach ((array) $flash as $i => $message) {
                    $modalId = $this->getId() . '-' . $type . '-' . $i;
                    $flashes[] = [
                        'type' => $type,
                        'message' => $message,
                        'modalId' => $modalId
                    ];
                    
                    // Generar el modal
                    echo '
                    <div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title ' . $this->alertTypes[$type] . '" id="' . $modalId . 'Label">' . 
                                        $this->iconTypes[$type] . 
                                        ucfirst(str_replace(['danger', 'error'], 'error', $type)) . 
                                    '</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    ' . $message . '
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                    
                    // JavaScript para mostrar el modal automáticamente
                    $js .= "
                    document.addEventListener('DOMContentLoaded', function() {
                        var alertModal = new bootstrap.Modal(document.getElementById('" . $modalId . "'));
                        alertModal.show();
                    });";
                }
                $session->removeFlash($type);
            }
        }
        
        // Registrar el JavaScript si hay flashes
        if (!empty($flashes)) {
            $view = $this->getView();
            $view->registerJs($js);
        }
    }
}
