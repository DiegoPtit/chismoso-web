<?php
/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\Notificaciones;
use yii\web\JqueryAsset;
JqueryAsset::register($this);
AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/assets/img/chismo-ico.png')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= Html::cssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css') ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => '<i class="fas fa-comments"></i> El Chismoso',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm',
            'style' => 'padding: 0.5rem 0;'
        ],
        'innerContainerOptions' => [
            'class' => 'container-fluid px-4'
        ]
    ]);
    
    // Calcular el número de notificaciones
    $notificationCount = 0;
    if (!Yii::$app->user->isGuest) {
        $notificationCount = Notificaciones::find()
            ->where(['receptor_id' => Yii::$app->user->id])
            ->count();
    }

    $menuItems = [
        [
            'label' => '<i class="fas fa-home"></i> Inicio',
            'url' => ['/site/index'],
            'encode' => false,
            'options' => ['class' => 'nav-item mx-2']
        ],
        [
            'label' => '<i class="fas fa-bell"></i> Notificaciones' . 
                ($notificationCount > 0 ? 
                ' <span class="badge bg-danger rounded-pill">' . $notificationCount . '</span>' : ''),
            'url' => ['/site/notificaciones'],
            'encode' => false,
            'options' => ['class' => 'nav-item mx-2']
        ],
        [
            'label' => '<i class="fas fa-plus-circle"></i> Nuevo Chisme',
            'url' => ['/site/create-post'],
            'encode' => false,
            'options' => ['class' => 'nav-item mx-2']
        ],
    ];

    // Agregar el ítem de administración solo si el usuario tiene rol_id = 1313
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->rol_id == 1313) {
        $menuItems[] = [
            'label' => '<i class="fas fa-cog"></i> Administración',
            'url' => ['/site/logs'],
            'encode' => false,
            'options' => ['class' => 'nav-item mx-2']
        ];
    }

    $menuItems[] = Yii::$app->user->isGuest
        ? [
            'label' => '<i class="fas fa-sign-in-alt"></i> Iniciar Sesión',
            'url' => ['/site/login'],
            'encode' => false,
            'options' => ['class' => 'nav-item mx-2']
        ]
        : '<li class="nav-item mx-2">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                '<i class="fas fa-sign-out-alt"></i> Cerrar Sesión (' . Yii::$app->user->identity->user . ')',
                ['class' => 'nav-link btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';

    echo Nav::widget([
        'options' => [
            'class' => 'navbar-nav ms-auto',
            'style' => 'font-size: 1.1rem;'
        ],
        'items' => $menuItems,
    ]);
    
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">Creado por DiegoPtit (Github) <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end">Que vivan los secretos, QPD TuSecreto</div>
        </div>
    </div>
</footer>

<style>
.navbar {
    transition: all 0.3s ease;
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: 600;
}

.nav-link {
    position: relative;
    padding: 0.5rem 1rem !important;
    transition: all 0.3s ease;
}

.nav-link:hover {
    color: #fff !important;
    transform: translateY(-2px);
}

.nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 50%;
    background-color: #fff;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.nav-link:hover::after {
    width: 80%;
}

.badge {
    font-size: 0.8rem;
    padding: 0.35em 0.65em;
}

@media (max-width: 991.98px) {
    .navbar-nav {
        padding: 1rem 0;
    }
    
    .nav-item {
        margin: 0.5rem 0;
    }
}
</style>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
