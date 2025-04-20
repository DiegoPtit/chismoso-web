<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Html;
use app\models\Notificaciones;
use yii\web\JqueryAsset;

JqueryAsset::register($this);
AppAsset::register($this);

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
        
        <div class="nav-item">
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            <?php else: ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/site/logout']) ?>" data-method="post">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
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
    z-index: 1060;
    width: 90%;
}

.toast {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    overflow: hidden;
    z-index: 1060;
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?> 