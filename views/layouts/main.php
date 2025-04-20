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
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
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

<header id="navbar" class="fixed-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
                <i class="fas fa-comments"></i> El Chismoso
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (!Yii::$app->user->isGuest): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= Yii::$app->controller->action->id === 'notificaciones' ? 'active' : '' ?>" href="<?= Yii::$app->urlManager->createUrl(['/site/notificaciones']) ?>">
                            <i class="fas fa-bell"></i> Notificaciones
                            <?php if ($notificationCount > 0): ?>
                            <span class="badge bg-danger"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= Yii::$app->controller->action->id === 'create-post' ? 'active' : '' ?>" href="<?= Yii::$app->urlManager->createUrl(['/site/create-post']) ?>">
                            <i class="fas fa-plus-circle"></i> Crear Post
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (!Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['/site/logs']) ?>">Logs</a></li>
                            <li><a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['/site/gestion-contenido']) ?>">Gestión de Contenido</a></li>
                            <li><a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['/site/admin-usuarios']) ?>">Administrar Usuarios</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (Yii::$app->user->isGuest): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <?= Yii::$app->user->identity->user ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['/site/logout']) ?>" data-method="post">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><div class="dropdown-item-text subscription-status-container px-3 py-2"></div></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="main-content" class="flex-grow-1 py-4">
    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="footer fixed-bottom py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; El Chismoso <?= date('Y') ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="social-links">
                    <a href="#" class="me-2"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
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
}

body {
    background-color: var(--light-bg);
    color: var(--text-color);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    min-height: 100vh;
    padding-top: 70px; /* Espacio para el header fijo */
    padding-bottom: 80px; /* Espacio para el footer fijo */
}

/* Navbar */
#navbar {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1030;
}

.navbar-brand {
    font-weight: 600;
    color: var(--primary-color);
}

.nav-link {
    position: relative;
    color: var(--text-color);
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease;
}

.nav-link:hover, .nav-link.active {
    color: var(--primary-color);
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 10%;
    width: 80%;
    height: 3px;
    background-color: var(--primary-color);
    border-radius: 3px 3px 0 0;
}

/* Main content */
#main-content {
    min-height: calc(100vh - 160px); /* Altura mínima para el contenido principal */
}

/* Footer */
.footer {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.social-links a {
    color: var(--text-color);
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: var(--primary-color);
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

<!-- Modal para visualizar imágenes a pantalla completa -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen Adjunta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center bg-dark p-0">
                <img src="" id="fullScreenImage" class="img-fluid" alt="Imagen a pantalla completa">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
    
    // Verificar estado de suscripción si el usuario está logueado
    <?php if (!Yii::$app->user->isGuest): ?>
    function checkUserSubscription() {
        $.ajax({
            url: '<?= Yii::$app->urlManager->createUrl(['site/check-subscription']) ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Actualizar mensaje de estado de suscripción
                    var suscripcionMsg = '';
                    if (response.debug && typeof response.debug === 'object') {
                        if (response.debug.estado_suscripcion !== undefined) {
                            // Verificar estado de suscripción (0: inactiva, 1: activa, 2: morosa)
                            var estadoSuscripcion = response.debug.estado_suscripcion;
                            
                            if (estadoSuscripcion == 1) {
                                suscripcionMsg = '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Suscripción activa</span>';
                            } else if (estadoSuscripcion == 2) {
                                suscripcionMsg = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Suscripción morosa</span>';
                            } else {
                                suscripcionMsg = '<span class="badge bg-secondary"><i class="fas fa-exclamation-triangle"></i> Sin suscripción</span>';
                            }
                        } else if (response.debug.suscripcion) {
                            // Mantener compatibilidad con respuestas anteriores
                            if (response.debug.suscripcion.includes('Activa')) {
                                suscripcionMsg = '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Suscripción activa</span>';
                            } else if (response.debug.suscripcion.includes('Morosa')) {
                                suscripcionMsg = '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Suscripción morosa</span>';
                            } else {
                                suscripcionMsg = '<span class="badge bg-secondary"><i class="fas fa-exclamation-triangle"></i> Sin suscripción</span>';
                            }
                        }
                        
                        if (suscripcionMsg) {
                            $('.subscription-status-container').html(suscripcionMsg);
                        }
                    }
                }
            }
        });
    }
    
    // Ejecutar al cargar la página
    checkUserSubscription();
    <?php endif; ?>
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
