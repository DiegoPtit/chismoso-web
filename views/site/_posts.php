<?php
/* @var $post app\models\Posts */
use yii\helpers\Html;

// Definir estilos e íconos según el género del post
switch ($post->genre) {
    case 1:
        $headerFooterColor = "#aeb3ff";
        $bodyColor = "#e4e6ff";
        $icon = 'fa-male';  // Ícono para hombre
        break;
    case 2:
        $headerFooterColor = "#ffb3fa";
        $bodyColor = "#ffddfd";
        $icon = 'fa-female';  // Ícono para mujer
        break;
    default:
        $headerFooterColor = "#c2c2c2";
        $bodyColor = "#e5e5e5";
        $icon = 'fa-user-secret';  // Ícono para incognito
        break;
}
?>
<div class="card mb-3 dashboard-card">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, <?= $headerFooterColor ?>, <?= $bodyColor ?>); color: #000;">
        <span><?= Html::encode('@' . $post->id) ?></span>
        <span><?= Yii::$app->formatter->asDatetime($post->created_at) ?></span>
        <span>
            <i class="fa <?= $icon ?> me-2"></i>
            <strong><?= $post->age ?> años</strong>
        </span>
    </div>
    <div class="card-body" style="background-color: <?= $bodyColor ?>;">
        <p class="mb-0"><?= Html::encode($post->contenido) ?></p>
    </div>
    <div class="card-footer" style="background: linear-gradient(45deg, <?= $headerFooterColor ?>, <?= $bodyColor ?>); color: #000;">
        <div class="d-flex align-items-center">
            <!-- Formulario de Like -->
            <?= Html::beginForm(['/site/like', 'id' => $post->id], 'post') ?>
                <button type="submit" class="btn btn-link text-dark me-3">
                    <i class="fa fa-thumbs-up"></i> <strong><?= $post->likes ?></strong>
                </button>
            <?= Html::endForm() ?>

            <!-- Formulario de Dislike -->
            <?= Html::beginForm(['/site/dislike', 'id' => $post->id], 'post') ?>
                <button type="submit" class="btn btn-link text-dark me-3">
                    <i class="fa fa-thumbs-down"></i> <strong><?= $post->dislikes ?></strong>
                </button>
            <?= Html::endForm() ?>

            <!-- Botón para abrir modal de comentarios -->
            <?= Html::a('<i class="fa fa-comment" style="margin-right: 10px;"></i>', '#', [
                'class' => 'text-dark me-3',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#commentModal' . $post->id,
            ]) ?>

            <span>
                <?= Html::a('<i class="fa fa-flag"></i> <strong style="margin-right: 10px;">Reportar Chisme</strong>', ['site/reportar', 'post_id' => $post->id], [
                    'class' => 'icono-reporte',
                    'title' => 'Reportar Chisme',
                ]) ?>
                <?= Html::a('<i class="fa fa-user-times"></i> <strong style="margin-right: 10px;">Reportar Usuario</strong>', ['site/reportar', 'usuario_id' => $post->usuario->id], [
                    'class' => 'icono-reporte',
                    'title' => 'Reportar Usuario',
                ]) ?>
            </span>
        </div>
    </div>
</div>

<style>
.dashboard-card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    margin-bottom: 1.5rem;
}
.dashboard-card:hover {
    transform: translateY(-5px);
}
.card-header {
    border-radius: 15px 15px 0 0 !important;
    padding: 1.5rem;
}
.card-body {
    padding: 1.5rem;
}
.card-footer {
    border-radius: 0 0 15px 15px;
    padding: 1rem 1.5rem;
}
.btn-link {
    text-decoration: none;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
}
.btn-link:hover {
    background-color: rgba(0,0,0,0.1);
}
.icono-reporte {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
    color: #d93e3e;
    text-decoration: none;
}
.icono-reporte:hover {
    background-color: rgba(0,0,0,0.1);
}
</style>
