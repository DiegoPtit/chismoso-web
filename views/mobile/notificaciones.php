<?php
/* @var $this yii\web\View */
/* @var $notificaciones app\models\Notificaciones[] */

use yii\helpers\Html;
use yii\bootstrap5\Modal;

$this->title = 'Notificaciones';
$notificationsUrl = Yii::$app->urlManager->createUrl(['mobile/notificaciones']);

// Registrar CSS específico para móviles
$this->registerCssFile('@web/css/notificaciones.css', ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);
?>

<div class="mobile-notification-container">
    <h2 class="notification-title text-center"><?= Html::encode($this->title) ?></h2>

    <?= Html::button('<i class="fa fa-sync"></i>', [
        'id' => 'reload-notifications',
        'class' => 'btn btn-primary rounded-circle position-fixed btn-reload d-flex align-items-center justify-content-center',
        'style' => 'bottom: 80px; right: 20px; z-index: 1000; width: 50px; height: 50px;',
        'title' => 'Recargar notificaciones'
    ]) ?>

    <?php if (empty($notificaciones)): ?>
        <div class="empty-notification-state">
            <i class="fa fa-bell-slash fa-4x mb-3"></i>
            <p class="empty-notification-text">No tienes nuevas notificaciones.</p>
            <div class="support-note mt-4">
                <p>Nota del Creador: Tenganme paciencia, apenas puedo descansar. 😫</p>
                <p>Si deseas apoyarme, mi Binance Pay es newdblogs@gmail.com o mi dirección crypto es 0xacddd2c165c0c2d3b55a6ebe3a2400859a49cb7b en la red BSC para USDT (TetherUS).</p>
                <p>Te lo agradecería mucho, me ayudaría a seguir creciendo! 💙</p>
            </div>
        </div>
    <?php else: ?>
        <div id="notifications-container" class="mobile-notifications-list">
            <?php foreach ($notificaciones as $notificacion): ?>
                <?php 
                    $comentario = $notificacion->comentario;
                    $postOriginal = $notificacion->postOriginal;
                ?>
                <div class="mobile-notification-card">
                    <!-- Vista previa del post padre -->
                    <div class="parent-post-preview">
                        <div class="parent-post-header">
                            <div class="parent-post-icon">
                                <i class="fa <?= $postOriginal->padre_id === null ? 'fa-bullhorn' : 'fa-comment-o' ?>"></i>
                            </div>
                            <div class="parent-post-meta">
                                <span class="parent-post-type">
                                    <?= $postOriginal->padre_id === null ? 'Chisme' : 'Comentario' ?>
                                </span>
                                <span class="parent-post-time">
                                    <?= Yii::$app->formatter->asRelativeTime($postOriginal->created_at) ?>
                                </span>
                            </div>
                        </div>
                        <div class="parent-post-content">
                            <?= Html::encode(mb_strimwidth($postOriginal->contenido, 0, 150, '...')) ?>
                        </div>
                    </div>
                    
                    <div class="notification-divider"></div>
                    
                    <!-- Contenido de la notificación -->
                    <div class="notification-content">
                        <div class="notification-icon">
                            <i class="fa fa-comment"></i>
                        </div>
                        <div class="notification-body">
                            <h5 class="notification-heading">Nuevo comentario en tu chisme</h5>
                            <p class="notification-text">
                                <?= Html::encode(mb_strimwidth($comentario->contenido, 0, 100, '...')) ?>
                            </p>
                            <div class="notification-footer">
                                <span class="notification-time">
                                    <i class="fa fa-clock-o me-1"></i>
                                    <?= Yii::$app->formatter->asRelativeTime($comentario->created_at) ?>
                                </span>
                                <?= Html::a('<i class="fa fa-eye me-1"></i> Ver', ['/mobile/comentarios', 'id' => $postOriginal->id], [
                                    'class' => 'btn btn-sm btn-primary notification-action-btn',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$this->registerCss(<<<CSS
    .mobile-notification-container {
        padding: 15px;
        max-width: 100%;
    }
    
    .notification-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    
    .mobile-notifications-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .mobile-notification-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.03);
    }
    
    .mobile-notification-card:active {
        transform: scale(0.98);
        background-color: #f8f9fc;
    }
    
    /* Estilos para la vista previa del post padre */
    .parent-post-preview {
        background-color: #f0f3f8;
        border-radius: 16px;
        padding: 16px;
        transition: background-color 0.2s ease;
    }
    
    .parent-post-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .parent-post-icon {
        background: rgba(0,0,0,0.06);
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4a6cf7;
        margin-right: 14px;
    }
    
    .parent-post-meta {
        display: flex;
        flex-direction: column;
    }
    
    .parent-post-type {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1f1f1f;
    }
    
    .parent-post-time {
        font-size: 0.75rem;
        color: #5f5f5f;
    }
    
    .parent-post-content {
        font-size: 0.95rem;
        color: #2e2e2e;
        line-height: 1.5;
    }
    
    .notification-divider {
        height: 1px;
        background-color: rgba(0,0,0,0.06);
        margin: 0 -20px;
    }
    
    /* Contenido de la notificación */
    .notification-content {
        display: flex;
        gap: 18px;
    }
    
    .notification-icon {
        background: #e8f0fe;
        border-radius: 50%;
        min-width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4a6cf7;
        margin-top: 4px;
    }
    
    .notification-body {
        flex: 1;
    }
    
    .notification-heading {
        font-size: 1.05rem;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1a1a1a;
    }
    
    .notification-text {
        font-size: 0.95rem;
        color: #3c3c3c;
        margin-bottom: 14px;
        line-height: 1.5;
    }
    
    .notification-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }
    
    .notification-time {
        color: #606060;
    }
    
    .notification-action-btn {
        padding: 8px 16px;
        font-size: 0.85rem;
        border-radius: 18px;
        transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: none;
        border: none;
    }
    
    .notification-action-btn:hover,
    .notification-action-btn:focus {
        background-color: #3959d9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    
    .empty-notification-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        color: #5f5f5f;
    }
    
    .empty-notification-text {
        font-size: 1.2rem;
        margin-bottom: 24px;
    }
    
    .support-note {
        font-size: 0.85rem;
        background: #f0f3f8;
        padding: 20px;
        border-radius: 16px;
        max-width: 100%;
        line-height: 1.5;
    }
    
    .btn-reload {
        width: 56px !important;
        height: 56px !important;
        box-shadow: 0 4px 12px rgba(74, 108, 247, 0.25);
        border-radius: 28px !important;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }
    
    .btn-reload:hover,
    .btn-reload:focus {
        box-shadow: 0 6px 16px rgba(74, 108, 247, 0.35);
    }
    
    .btn-reload:active {
        transform: scale(0.92);
    }
CSS
);

$this->registerJs(<<<JS
    $(document).ready(function() {
        setupReloadButton();
    });
    
    function setupReloadButton() {
        $("#reload-notifications").on("click", function() {
            var btn = $(this);
            btn.find('i').addClass('fa-spin');
            
            $.ajax({
                url: "{$notificationsUrl}",
                type: "GET",
                success: function(data) {
                    var newContent = $(data).find("#notifications-container").html();
                    if (newContent) {
                        $("#notifications-container").html(newContent);
                    } else {
                        location.reload();
                    }
                },
                error: function() {
                    showToast("Error al cargar notificaciones. Intenta de nuevo.");
                },
                complete: function() {
                    btn.find('i').removeClass('fa-spin');
                }
            });
        });
    }
    
    function showToast(message) {
        var toast = $('<div class="mobile-toast">' + message + '</div>');
        $('body').append(toast);
        
        setTimeout(function() {
            toast.addClass('show');
            
            setTimeout(function() {
                toast.removeClass('show');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        }, 100);
    }
    
    $(document).on('click', '.mobile-notification-card', function(e) {
        if (!$(e.target).closest('a').length) {
            var viewUrl = $(this).find('.notification-action-btn').attr('href');
            if (viewUrl) {
                window.location.href = viewUrl;
            }
        }
    });
JS
);

$this->registerCss(<<<CSS
    .mobile-toast {
        position: fixed;
        bottom: -50px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(33, 33, 33, 0.9);
        color: white;
        padding: 12px 24px;
        border-radius: 24px;
        z-index: 9999;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        backdrop-filter: blur(8px);
        max-width: 85%;
        text-align: center;
    }
    
    .mobile-toast.show {
        bottom: 110px;
    }
CSS
);
?> 