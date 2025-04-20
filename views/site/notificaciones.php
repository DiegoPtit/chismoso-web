<?php
/** @var yii\web\View $this */
/** @var app\models\Notificaciones[] $notificaciones */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

$this->title = 'Notificaciones';
?>

<div class="site-notificaciones">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h1 class="text-center mb-4">
                <i class="fas fa-bell"></i> El Chismoso
                <small class="d-block text-muted mt-2">Tus notificaciones</small>
            </h1>
            
            <div class="forum-container">
                <div class="forum-post">
                    <div class="forum-post-content">
                        <?php if (empty($notificaciones)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No tienes notificaciones por el momento.
                            </div>
                        <?php else: ?>
                            <div class="notifications-list">
                                <?php foreach ($notificaciones as $notificacion): ?>
                                    <?php
                                    // Obtener el comentario y el post original
                                    $comentario = $notificacion->comentario;
                                    $postOriginal = $notificacion->postOriginal;
                                    
                                    // Marcar como leída si no lo está
                                    if (!$notificacion->leido) {
                                        $notificacion->marcarComoLeida();
                                    }
                                    
                                    // Definir la clase según el estado de lectura
                                    $itemClass = $notificacion->leido ? 'notification-item' : 'notification-item unread';
                                    ?>
                                    
                                    <div class="<?= $itemClass ?>">
                                        <div class="notification-header">
                                            <div class="notification-icon">
                                                <i class="fas fa-comment-dots"></i>
                                            </div>
                                            <div class="notification-content">
                                                <div class="notification-parent">
                                                    <i class="fas fa-reply"></i> Respuesta a:
                                                    <?php if ($postOriginal->padre_id === null): ?>
                                                        <span class="parent-text"><?= HtmlPurifier::process(mb_substr($postOriginal->contenido, 0, 50) . (mb_strlen($postOriginal->contenido) > 50 ? '...' : '')) ?></span>
                                                    <?php else: ?>
                                                        <span class="parent-text"><?= HtmlPurifier::process(mb_substr($postOriginal->contenido, 0, 50) . (mb_strlen($postOriginal->contenido) > 50 ? '...' : '')) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="notification-time">
                                                    <i class="far fa-clock"></i>
                                                    <?= Yii::$app->formatter->asRelativeTime($notificacion->created_at) ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="notification-body">
                                            <p class="notification-text">
                                                <?= HtmlPurifier::process($comentario->contenido) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="notification-footer">
                                            <a href="<?= Url::to(['site/comentarios', 'id' => $postOriginal->id, 'comment_id' => $comentario->id]) ?>" class="btn-forum">
                                                <i class="fas fa-eye"></i> Ver comentario
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.notification-item {
    background-color: #fff;
    border-radius: 5px;
    padding: 15px;
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.notification-item.unread {
    background-color: rgba(108, 92, 231, 0.1);
    border-left: 4px solid #6c5ce7;
}

.notification-header {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 15px;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #6c5ce7;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-parent {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 5px;
}

.parent-text {
    color: #444;
    font-weight: 500;
}

.notification-time {
    font-size: 0.8rem;
    color: #777;
}

.notification-body {
    background-color: #f8f9fa;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 15px;
}

.notification-text {
    font-size: 0.95rem;
    color: #444;
    margin: 0;
    line-height: 1.5;
}

.notification-footer {
    display: flex;
    justify-content: flex-end;
}

.btn-forum {
    border: none;
    background: #f1f1f1;
    color: #555;
    padding: 8px 16px;
    border-radius: 3px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-forum:hover {
    background: #e5e5e5;
    color: #555;
    text-decoration: none;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .notification-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .notification-time {
        margin-top: 10px;
    }
    
    .notification-footer {
        justify-content: center;
    }
}
</style> 