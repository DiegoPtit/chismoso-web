<?php
/* @var $this yii\web\View */
/* @var $notificaciones app\models\Notificaciones[] */

use yii\helpers\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;
use app\models\Posts;

$this->title = 'Notificaciones';

// Crear instancia del modelo para el formulario
$modelComentario = new Posts();

$this->registerCss(<<<CSS
    .site-notificaciones {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    .notifications-title {
        color: #2c3e50;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
    }
    .notification-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .notification-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .notification-content {
        padding: 1.5rem;
    }
    .notification-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    .notification-icon {
        width: 40px;
        height: 40px;
        background: #e3f2fd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    .notification-icon i {
        color: #1976d2;
        font-size: 1.2rem;
    }
    .notification-title {
        color: #2c3e50;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }
    .notification-text {
        color: #666;
        margin-bottom: 1rem;
        line-height: 1.5;
    }
    .notification-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #999;
        font-size: 0.9rem;
    }
    .btn-view-post {
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        border: none;
        border-radius: 12px;
        padding: 0.5rem 1.5rem;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-view-post:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(74,144,226,0.3);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #f8f9fa;
        border-radius: 16px;
        margin: 2rem 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .empty-state i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .empty-state p {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    .support-note {
        background: #e3f2fd;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }
    .support-note p {
        color: #1976d2;
        margin-bottom: 0.5rem;
    }
    .btn-reload {
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        border: none;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    .btn-reload:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    @media (max-width: 767px) {
        .site-notificaciones {
            padding: 1rem;
        }
        .notification-card {
            border-radius: 12px;
        }
        .notification-content {
            padding: 1rem;
        }
        .notification-header {
            flex-direction: column;
            text-align: center;
        }
        .notification-icon {
            margin: 0 0 1rem 0;
        }
        .btn-view-post {
            width: 100%;
            margin-top: 1rem;
        }
    }
    .comment-form-container {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .comment-form .form-control,
    .comment-form .form-select {
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    .comment-form .form-control:focus,
    .comment-form .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .comment-form textarea.form-control {
        resize: none;
        min-height: 100px;
    }
    .comment-form .btn-primary {
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .comment-form .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .comment-form .form-control::placeholder {
        color: #adb5bd;
    }
CSS
);

// Crear la URL antes del heredoc
$notificationsUrl = Yii::$app->urlManager->createUrl(['site/notificaciones']);
?>

<div class="site-notificaciones">
    <h1 class="notifications-title"><?= Html::encode($this->title) ?></h1>

    <?= Html::button('<i class="fa fa-sync"></i>', [
        'id' => 'reload-notifications',
        'class' => 'btn btn-primary btn-lg rounded-circle position-fixed btn-reload d-flex align-items-center justify-content-center',
        'style' => 'bottom: 30px; right: 30px; z-index: 1000;',
        'title' => 'Recargar notificaciones'
    ]) ?>

    <?php if (empty($notificaciones)): ?>
        <div class="empty-state">
            <i class="fa fa-bell-slash"></i>
            <p>No tienes nuevas notificaciones.</p>
            <div class="support-note">
                <p>Nota del Creador: Tenganme paciencia, apenas puedo descansar. 😫</p>
                <p>Si deseas apoyarme, mi Binance Pay es newdblogs@gmail.com o mi dirección crypto es 0xacddd2c165c0c2d3b55a6ebe3a2400859a49cb7b en la red BSC para USDT (TetherUS).</p>
                <p>Te lo agradecería mucho, me ayudaría a seguir creciendo! 💙</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($notificaciones as $notificacion): ?>
                <?php 
                    $comentario = $notificacion->comentario;
                    $postOriginal = $notificacion->postOriginal;
                ?>
                <div class="col-12">
                    <div class="notification-card">
                        <div class="notification-content">
                            <div class="notification-header">
                                <div class="notification-icon">
                                    <i class="fa fa-comment"></i>
                                </div>
                                <h5 class="notification-title">
                                    Nuevo comentario en tu chisme
                                </h5>
                            </div>
                            <div class="notification-text">
                                <?= Html::encode(mb_strimwidth($comentario->contenido, 0, 150, '...')) ?>
                            </div>
                            <div class="notification-meta">
                                <span>
                                    <i class="fa fa-clock-o me-1"></i>
                                    <?= Yii::$app->formatter->asDatetime($comentario->created_at) ?>
                                </span>
                                <?= Html::a('Ver conversación', '#', [
                                    'class' => 'btn btn-view-post',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#commentModal' . $postOriginal->id,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Renderizar los modales de comentarios -->
        <?php foreach ($notificaciones as $notificacion): ?>
            <?php 
                $postOriginal = $notificacion->postOriginal;
                echo $this->render('_post', [
                    'post' => $postOriginal,
                    'modelComentario' => $modelComentario
                ]);
            ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$this->registerJs(<<<JS
    // Script para recarga AJAX
    $("#reload-notifications").on("click", function() {
        var btn = $(this);
        btn.find('i').addClass('fa-spin');
        
        $.ajax({
            url: "{$notificationsUrl}",
            type: "GET",
            success: function(data) {
                $(".site-notificaciones").html($(data).find(".site-notificaciones").html());
            },
            complete: function() {
                btn.find('i').removeClass('fa-spin');
            }
        });
    });

    // Manejo de likes y dislikes con AJAX
    $(document).on('submit', '.like-form, .dislike-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var isLike = form.hasClass('like-form');
        var countElement = isLike ? form.find('.likes-count') : form.find('.dislikes-count');
        
        $.post(url, function(response) {
            if (response.success) {
                countElement.text(response.count);
            } else {
                alert(response.message || 'Error al procesar la solicitud');
            }
        }).fail(function() {
            alert('Error al procesar la solicitud');
        });
    });

    // Manejo del formulario de comentarios con AJAX
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var postId = form.data('post-id');
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();
        
        // Deshabilitar el botón y mostrar loading
        submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Enviando...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Cerrar el modal
                    form.closest('.modal').modal('hide');
                    
                    // Limpiar el formulario
                    form[0].reset();
                    
                    // Recargar la página para mostrar el nuevo comentario
                    window.location.reload();
                } else {
                    alert(response.message || 'Error al publicar el comentario');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud. Por favor, intente de nuevo.');
            },
            complete: function() {
                // Restaurar el botón
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
JS
);
?>