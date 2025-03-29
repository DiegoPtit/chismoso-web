<?php
/* @var $this yii\web\View */
/* @var $posts app\models\Posts[] */
/* @var $modelComentario app\models\Posts */
/* @var $perPage int */
/* @var $totalPosts int */

use yii\helpers\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;

$this->title = 'Chismoso App';

// Registrar estilos CSS
$this->registerCss(<<<CSS
    .site-index {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    .subcomments {
        border-left: 3px solid #e9ecef;
        padding-left: 1.5rem;
        margin-left: 1rem;
        border-radius: 0 4px 4px 0;
    }
    .btn-flotante {
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        border: none;
        width: 60px;
        height: 60px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    .btn-flotante:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    .icono-reporte {
        cursor: pointer;
        color: #d9534f;
        margin-right: 10px;
        transition: transform 0.2s ease;
    }
    .icono-reporte:hover {
        transform: scale(1.1);
    }
    .post-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 2rem;
        background: #ffffff;
        overflow: hidden;
    }
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
    .post-card .card-body {
        padding: 2rem;
    }
    @media (min-width: 768px) {
        .post-card {
            margin: 0 1rem 2rem 1rem;
        }
        .post-card .card-body {
            padding: 2.5rem;
        }
    }
    .post-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }
    .post-card .card-footer {
        background: transparent;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }
    .comments-container {
        max-height: 500px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #4a90e2 #f3f3f3;
        padding: 0 0.5rem;
    }
    .comments-container::-webkit-scrollbar {
        width: 6px;
    }
    .comments-container::-webkit-scrollbar-track {
        background: #f3f3f3;
        border-radius: 3px;
    }
    .comments-container::-webkit-scrollbar-thumb {
        background: #4a90e2;
        border-radius: 3px;
    }
    #loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #4a90e2;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #f8f9fa;
        border-radius: 24px;
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
    }
    .modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    .modal-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem 2rem;
    }
    .modal-body {
        padding: 2rem;
    }
    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1.5rem 2rem;
    }

    /* Estilos responsivos para móviles */
    @media (max-width: 767px) {
        .site-index {
            padding: 1rem;
        }
        .post-card {
            border-radius: 16px;
            margin-bottom: 1rem;
        }
        .post-card .card-body {
            padding: 1rem;
        }
        .post-card .card-header {
            padding: 1rem;
        }
        .post-card .card-footer {
            padding: 1rem;
        }
        .btn-label {
            display: none;
        }
        .btn i {
            margin-right: 0 !important;
        }
        .btn {
            padding: 0.5rem 0.75rem;
        }
        .btn-lg {
            padding: 0.75rem 1rem;
        }
        .empty-state {
            padding: 2rem 1rem;
            border-radius: 16px;
        }
        .empty-state i {
            font-size: 2.5rem;
        }
        .empty-state p {
            font-size: 1rem;
        }
    }
CSS
);

// Verificar si hay algún mensaje flash
$flashTypes = ['success', 'error', 'warning', 'info'];
foreach ($flashTypes as $type) {
    if (Yii::$app->session->hasFlash($type)) {
        $message = Yii::$app->session->getFlash($type);
        $modalType = match ($type) {
            'success' => 'bg-success text-white',
            'error' => 'bg-danger text-white',
            'warning' => 'bg-warning text-dark',
            'info' => 'bg-info text-dark',
        };

        Modal::begin([
            'id' => 'flashMessageModal',
            'title' => match ($type) {
                'success' => '¡Exito!',
                'error' => '¡Ha ocurrido un error!',
                'warning' => '¡Advertencia!',
                'info' => 'Información',
            },
            'options' => ['class' => 'fade'],
            'headerOptions' => ['class' => $modalType],
        ]);

        echo "<p>{$message}</p>";
        echo Html::button('Cerrar', [
            'class' => 'btn btn-secondary',
            'data-bs-dismiss' => 'modal'
        ]);

        Modal::end();

        $this->registerJs(<<<JS
            var flashModal = new bootstrap.Modal(document.getElementById('flashMessageModal'));
            flashModal.show();
        JS);
        break;
    }
}
?>

<div class="site-index">
    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <i class="fa fa-comments"></i>
            <p>No hay chismes disponibles en este momento.</p>
            <p class="text-muted">¡Sé el primero en compartir un chisme!</p>
        </div>
    <?php else: ?>
        <div id="posts-container">
            <?php foreach ($posts as $post): ?>
                <?= $this->render('_post', [
                    'post' => $post,
                    'modelComentario' => $modelComentario
                ]) ?>
            <?php endforeach; ?>
        </div>

        <div id="loading-spinner">
            <div class="loading-spinner"></div>
        </div>

        <?= Html::a(
            '<i class="fa fa-plus"></i>',
            ['/site/create-post'],
            [
                'class' => 'btn btn-primary btn-lg rounded-circle position-fixed btn-flotante d-flex align-items-center justify-content-center',
                'style' => 'bottom: 30px; right: 30px; z-index: 1000;'
            ]
        ); ?>
    <?php endif; ?>
</div>

<?php
// Si se recibe un parámetro "modal", reabrir ese modal automáticamente
$modalId = Yii::$app->request->get('modal');
if ($modalId) {
    $this->registerJs("
        $(document).ready(function(){
            var modal = new bootstrap.Modal(document.getElementById('commentModal$modalId'));
            modal.show();
            $('#commentModal$modalId').on('hidden.bs.modal', function(){
                window.location.href = window.location.pathname;
            });
        });
    ");
}
?>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9977380373858586"
     crossorigin="anonymous"></script>

<?php
// Registrar scripts
$this->registerJs(<<<JS
    // Variables para el infinite scroll
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    const perPage = $perPage;
    const totalPosts = $totalPosts;
    const totalPages = Math.ceil(totalPosts / perPage);

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

    // Función para cargar más posts
    function loadMorePosts() {
        if (isLoading || !hasMore) return;
        
        isLoading = true;
        $('#loading-spinner').show();
        
        $.get(window.location.pathname, {
            page: currentPage + 1
        })
        .done(function(response) {
            if (response.success) {
                $('#posts-container').append(response.html);
                currentPage++;
                hasMore = response.hasMore;
                
                // Reinicializar los modales de Bootstrap para los nuevos posts
                response.html.match(/id="commentModal\d+"/g).forEach(function(match) {
                    const modalId = match.match(/\d+/)[0];
                    new bootstrap.Modal(document.getElementById('commentModal' + modalId));
                });
            }
        })
        .fail(function() {
            alert('Error al cargar más posts');
        })
        .always(function() {
            isLoading = false;
            $('#loading-spinner').hide();
        });
    }

    // Detectar cuando el usuario llega al final de la página
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            loadMorePosts();
        }
    });
JS
);
?>
