<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Html;
use app\models\Notificaciones;
use yii\web\JqueryAsset;
use yii\bootstrap5\BootstrapPluginAsset;

JqueryAsset::register($this);
AppAsset::register($this);
BootstrapPluginAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/assets/img/chismo-ico.png')]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

// Calcular el número de notificaciones
$notificationCount = 0;
if (!Yii::$app->user->isGuest) {
    $notificationCount = Notificaciones::find()
        ->where(['receptor_id' => Yii::$app->user->id])
        ->count();
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="mobile-header" class="fixed-top">
    <div class="text-center p-3">
        <h1 class="app-title mb-0"><i class="fas fa-comments"></i> El Chismoso</h1>
    </div>
</header>

<main id="mobile-main" class="flex-grow-1" role="main">
    <div class="container py-4">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="mobile-footer" class="fixed-bottom">
    <div class="mobile-nav">
        <div class="nav-item <?= Yii::$app->controller->action->id === 'index' ? 'active' : '' ?>">
            <a href="<?= Yii::$app->urlManager->createUrl(['/mobile']) ?>">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
        </div>
        
        <div class="nav-item <?= Yii::$app->controller->action->id === 'notificaciones' ? 'active' : '' ?>">
            <a href="<?= Yii::$app->urlManager->createUrl(['/mobile/notificaciones']) ?>">
                <i class="fas fa-bell"></i>
                <span>Notificaciones</span>
                <?php if ($notificationCount > 0): ?>
                <span class="notification-badge"><?= $notificationCount ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="nav-item <?= Yii::$app->controller->action->id === 'create-post' ? 'active' : '' ?>">
            <a href="<?= Yii::$app->urlManager->createUrl(['/mobile/create-post']) ?>">
                <i class="fas fa-plus-circle"></i>
                <span>Crear</span>
            </a>
        </div>
        
        <?php if (!Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])): ?>
        <div class="nav-item <?= in_array(Yii::$app->controller->action->id, ['logs', 'gestion-contenido', 'admin-usuarios']) ? 'active' : '' ?>">
            <a href="<?= Yii::$app->urlManager->createUrl(['/mobile/admin']) ?>">
                <i class="fas fa-cog"></i>
                <span>Admin</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="nav-item <?= Yii::$app->controller->action->id === 'perfil' ? 'active' : '' ?>">
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/mobile/login']) ?>">
                    <i class="fas fa-user"></i>
                    <span>Yo</span>
                </a>
            <?php else: ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/mobile/perfil']) ?>">
                    <i class="fas fa-user"></i>
                    <span>Yo</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</footer>

<style>
:root {
    --primary-color: #6c5ce7;
    --secondary-color: #a8a4e6;
    --hover-color: #5b4cc4;
    --text-color: #2d3436;
    --light-bg: #f8f9fa;
    --nav-height: 60px;
    --header-height: 60px;
}

body {
    background-color: var(--light-bg);
    color: var(--text-color);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    padding-top: var(--header-height);
    padding-bottom: var(--nav-height);
    overflow-x: hidden;
    touch-action: manipulation;
}

/* Header */
#mobile-header {
    height: var(--header-height);
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1030;
}

.app-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--primary-color);
    margin: 0;
}

/* Main content */
#mobile-main {
    padding-bottom: 1rem;
}

/* Mobile navigation bar (Material You style) */
#mobile-footer {
    height: var(--nav-height);
    background-color: white;
    z-index: 1030;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.mobile-nav {
    display: flex;
    height: 100%;
    justify-content: space-around;
}

.mobile-nav .nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.mobile-nav .nav-item a {
    color: var(--text-color);
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    padding: 8px 0;
}

.mobile-nav .nav-item i {
    font-size: 1.2rem;
    margin-bottom: 4px;
    transition: transform 0.2s ease;
}

.mobile-nav .nav-item span {
    font-size: 0.75rem;
    font-weight: 500;
}

.mobile-nav .nav-item.active {
    color: var(--primary-color);
}

.mobile-nav .nav-item.active i {
    transform: translateY(-2px);
}

.mobile-nav .nav-item.active a {
    color: var(--primary-color);
}

.mobile-nav .nav-item.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 40%;
    height: 3px;
    background-color: var(--primary-color);
    border-radius: 3px 3px 0 0;
}

.mobile-nav .nav-item a:active {
    opacity: 0.7;
}

/* Notification badge */
.notification-badge {
    position: absolute;
    top: 4px;
    right: calc(50% - 14px);
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    background-color: #ff4757;
    color: white;
    font-size: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
    font-weight: bold;
}

/* Material design ripple effect */
.ripple {
    position: relative;
    overflow: hidden;
}

.ripple::after {
    content: "";
    display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    pointer-events: none;
    background-image: radial-gradient(circle, #000 10%, transparent 10.01%);
    background-repeat: no-repeat;
    background-position: 50%;
    transform: scale(10, 10);
    opacity: 0;
    transition: transform .5s, opacity 1s;
}

.ripple:active::after {
    transform: scale(0, 0);
    opacity: .2;
    transition: 0s;
}

/* Toast alerts */
.toast-container {
    position: fixed;
    top: calc(var(--header-height) + 10px);
    left: 50%;
    transform: translateX(-50%);
    z-index: 1050;
    width: 90%;
}

.toast {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    overflow: hidden;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.container {
    animation: fadeIn 0.3s ease-out;
}
</style>

<!-- Scripts para las acciones de moderación -->
<script>
$(document).ready(function() {
    // Manejador para el botón de banear post
    $(document).on('click', '.ban-post-link', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        if (confirm('¿Estás seguro de que deseas banear este post?')) {
            // Redirigir a la página de banear post
            window.location.href = '<?= Yii::$app->urlManager->createUrl(['/mobile/ban-post']) ?>?id=' + postId;
        }
    });

    // Manejador para el botón de banear usuario
    $(document).on('click', '.ban-user-link', function(e) {
        e.preventDefault();
        var userId = $(this).data('user-id');
        if (confirm('¿Estás seguro de que deseas banear a este usuario?')) {
            // Redirigir a la página de banear usuario
            window.location.href = '<?= Yii::$app->urlManager->createUrl(['/mobile/ban-user']) ?>?id=' + userId;
        }
    });
});
</script>

<!-- Modal para visualizar imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center bg-dark p-0">
                <img src="" id="fullScreenImage" class="img-fluid" alt="Imagen a pantalla completa">
            </div>
        </div>
    </div>
</div>

<script>
// Script para manejar la visualización de imágenes en el modal
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        // Verificar si el clic fue en una imagen con la clase post-image
        if (e.target && e.target.classList.contains('post-image')) {
            const imgSrc = e.target.getAttribute('data-img-src');
            if (imgSrc) {
                const fullScreenImage = document.getElementById('fullScreenImage');
                if (fullScreenImage) {
                    fullScreenImage.src = imgSrc;
                    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                    imageModal.show();
                }
            }
        }
    });
});
</script>

<?php $this->endBody() ?>

<!-- Script para navegación AJAX -->
<script>
$(document).ready(function() {
    // Indicador de carga
    $('<div id="ajax-loader" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:1100;background-color:rgba(255,255,255,0.8);padding:20px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.2);"><i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--primary-color);"></i></div>').appendTo('body');

    // Función para cargar contenido mediante AJAX
    function loadContent(url, pushState = true) {
        // Mostrar indicador de carga
        $('#ajax-loader').fadeIn(200);
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                try {
                    // Crear un DOM temporal con los datos recibidos
                    var $data = $(data);
                    
                    // Extraer el contenido principal (solo la parte interna del contenedor)
                    var $content = $data.find('#mobile-main .container').html();
                    
                    if ($content) {
                        // Actualizar el contenido
                        $('#mobile-main .container').html($content);
                        
                        // Actualizar el título si está disponible
                        var newTitle = $data.filter('title').text();
                        if (newTitle) {
                            document.title = newTitle;
                        }
                        
                        // Actualizar contador de notificaciones si está disponible
                        var newBadge = $data.find('.notification-badge').text();
                        if (newBadge) {
                            $('.notification-badge').text(newBadge);
                        }
                        
                        // Actualizar el historial del navegador si es necesario
                        if (pushState) {
                            history.pushState({url: url}, document.title, url);
                        }
                        
                        // Actualizar la navegación activa
                        updateActiveNavItem(url);
                        
                        // Reinicializar los scripts específicos de la página si es necesario
                        reinitScripts();
                    } else {
                        // Si no se encuentra el contenido, recargar normalmente
                        console.error('No se pudo encontrar el contenido en la respuesta');
                        window.location = url;
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta:', e);
                    window.location = url;
                }
                
                // Ocultar indicador de carga
                $('#ajax-loader').fadeOut(200);
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                // En caso de error, redirigir normalmente
                window.location = url;
            }
        });
    }
    
    // Actualizar elemento activo en la navegación
    function updateActiveNavItem(url) {
        // Limpiar todas las clases activas
        $('.mobile-nav .nav-item').removeClass('active');
        
        // Determinar qué sección debe estar activa basada en la URL
        if (url.includes('/mobile/notificaciones')) {
            $('.mobile-nav .nav-item a[href*="/mobile/notificaciones"]').parent().addClass('active');
        } else if (url.includes('/mobile/create-post')) {
            $('.mobile-nav .nav-item a[href*="/mobile/create-post"]').parent().addClass('active');
        } else if (url.includes('/mobile/admin')) {
            $('.mobile-nav .nav-item a[href*="/mobile/admin"]').parent().addClass('active');
        } else if (url.includes('/mobile/login') || url.includes('/site/login')) {
            $('.mobile-nav .nav-item a[href*="/login"]').parent().addClass('active');
        } else if (url.includes('/mobile') && !url.includes('/mobile/')) {
            // Solo /mobile sin subdirectorio (inicio)
            $('.mobile-nav .nav-item a[href$="/mobile"]').parent().addClass('active');
        }
    }
    
    // Reinicializar scripts específicos después de cargar contenido
    function reinitScripts() {
        // Reinicializar cualquier componente de Bootstrap activo
        if (typeof $.fn.tooltip !== 'undefined') {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
        if (typeof $.fn.popover !== 'undefined') {
            $('[data-bs-toggle="popover"]').popover();
        }
        
        // Reactivar los manejadores de moderación
        $('.ban-post-link, .ban-user-link').off('click').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('post-id') ? 'post' : 'user';
            var id = $(this).data(target + '-id');
            
            if (confirm('¿Estás seguro de que deseas banear este ' + target + '?')) {
                window.location.href = '<?= Yii::$app->urlManager->createUrl(['/mobile/ban-']) ?>' + target + '?id=' + id;
            }
        });
    }
    
    // Interceptar clicks en los enlaces del menú de navegación
    $('.mobile-nav .nav-item a').on('click', function(e) {
        var href = $(this).attr('href');
        
        // Solo interceptar enlaces internos sin atributos especiales
        if (href && !$(this).attr('data-method') && !$(this).attr('target')) {
            e.preventDefault();
            loadContent(href);
        }
    });
    
    // Interceptar enlaces dentro del contenido principal
    $(document).on('click', '#mobile-main a', function(e) {
        var href = $(this).attr('href');
        
        // Verificar que es un enlace interno y no un enlace especial
        if (href && 
            href.indexOf('#') !== 0 && // No es un ancla
            !$(this).attr('data-method') && // No tiene data-method (usado por Yii para POST)
            !$(this).attr('target') && // No tiene target (ej. _blank)
            !$(this).hasClass('no-ajax') && // No tiene clase para deshabilitar AJAX
            href.indexOf('://') === -1) { // No es una URL externa
            
            e.preventDefault();
            loadContent(href);
        }
    });
    
    // Manejar navegación con los botones del navegador
    $(window).on('popstate', function(e) {
        if (e.originalEvent.state && e.originalEvent.state.url) {
            loadContent(e.originalEvent.state.url, false);
        } else {
            // Si no hay estado, cargar la página actual
            loadContent(window.location.href, false);
        }
    });
    
    // Guardar estado inicial
    history.replaceState({url: window.location.href}, document.title, window.location.href);
});
</script>

<!-- Estilos adicionales para la carga de contenido -->
<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.container {
    animation: fadeIn 0.3s ease-out;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s infinite linear;
}
</style>

</body>
</html>
<?php $this->endPage() ?> 