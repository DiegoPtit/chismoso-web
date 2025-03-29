<?php
/**
 * @var \app\models\Posts $post
 * @var \app\models\Posts $modelComentario
 */

use yii\helpers\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;

// Definir colores según el género del post
$headerFooterColor = match ($post->genre) {
    1 => "#aeb3ff",  // Hombre
    2 => "#ffb3fa",  // Mujer
    default => "#c2c2c2"  // Incógnito
};

$bodyColor = match ($post->genre) {
    1 => "#e4e6ff",
    2 => "#ffddfd",
    default => "#e5e5e5"
};

$icon = match ($post->genre) {
    1 => 'fa-male',
    2 => 'fa-female',
    default => 'fa-user-secret'
};
?>

<!-- Tarjeta del post principal -->
<div class="card mb-3 post-card" data-post-id="<?= $post->id ?>">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: <?= $headerFooterColor ?>; color: #000;">
        <span><?= Html::encode('@' . $post->id) ?></span>
        <span><?= Yii::$app->formatter->asDatetime($post->created_at) ?></span>
        <span>
            <i class="fa <?= $icon ?> me-2"></i>
            <strong><?= $post->age ?> años</strong>
        </span>
    </div>
    <div class="card-body" style="background-color: <?= $bodyColor ?>;">
        <p><?= Html::encode($post->contenido) ?></p>
    </div>
    <div class="card-footer" style="background-color: <?= $headerFooterColor ?>; color: #000;">
        <div class="d-flex align-items-center">
            <?= Html::beginForm(['/site/like', 'id' => $post->id], 'post', ['class' => 'like-form']) ?>
                <button type="submit" class="btn btn-link text-dark me-3">
                    <i class="fa fa-thumbs-up"></i> <span class="btn-label"></span> <strong class="likes-count"><?= $post->likes ?></strong>
                </button>
            <?= Html::endForm() ?>

            <?= Html::beginForm(['/site/dislike', 'id' => $post->id], 'post', ['class' => 'dislike-form']) ?>
                <button type="submit" class="btn btn-link text-dark me-3">
                    <i class="fa fa-thumbs-down"></i> <span class="btn-label"></span> <strong class="dislikes-count"><?= $post->dislikes ?></strong>
                </button>
            <?= Html::endForm() ?>

            <?= Html::a('<i class="fa fa-comment"></i><span class="btn-label"></span>', '#', [
                'class' => 'text-dark me-3',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#commentModal' . $post->id,
            ]) ?>

            <span>
                <?= Html::a('<i class="fa fa-flag"></i> <span class="btn-label">Reportar Chisme</span>', 
                    ['site/reportar', 'post_id' => $post->id], 
                    ['class' => 'icono-reporte', 'title' => 'Reportar Chisme']) ?>
                <?= Html::a('<i class="fa fa-user-times"></i> <span class="btn-label">Reportar Usuario</span>', 
                    ['site/reportar', 'usuario_id' => $post->usuario->id], 
                    ['class' => 'icono-reporte', 'title' => 'Reportar Usuario']) ?>
            </span>
        </div>
    </div>
</div>

<!-- Modal de comentarios -->
<?php Modal::begin([
    'id' => 'commentModal' . $post->id,
    'title' => 'Comentarios',
    'size' => 'modal-lg',
    'options' => ['data-post-id' => $post->id]
]); ?>
    <div class="post-content mb-3">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: <?= $headerFooterColor ?>; color: #000;">
                <span><?= Html::encode('@' . $post->id) ?></span>
                <span><?= Yii::$app->formatter->asDatetime($post->created_at) ?></span>
                <span>
                    <i class="fa <?= $icon ?> me-2"></i>
                    <strong><?= $post->age ?> años</strong>
                </span>
            </div>
            <div class="card-body" style="background-color: <?= $bodyColor ?>;">
                <p><?= Html::encode($post->contenido) ?></p>
            </div>
        </div>
    </div>
    <hr>
    
    <!-- Formulario para comentar -->
    <?php $form = ActiveForm::begin([
        'action' => ['/site/comment', 'post_id' => $post->id, 'modal' => $post->id],
        'options' => [
            'class' => 'comment-form d-flex flex-column gap-3',
            'data-post-id' => $post->id
        ]
    ]); ?>

    <div class="row g-3">
        <div class="col-md-3">
            <?= $form->field($modelComentario, 'age', [
                'inputOptions' => [
                    'type' => 'number',
                    'min' => 1,
                    'max' => 120,
                    'class' => 'form-control',
                    'placeholder' => 'Tu edad',
                    'required' => true,
                ]
            ])->label(false) ?>
        </div>
        <div class="col-md-9">
            <?= $form->field($modelComentario, 'genre')->dropDownList([
                0 => 'Prefiero no decir',
                1 => 'Hombre',
                2 => 'Mujer'
            ], [
                'class' => 'form-select',
                'prompt' => 'Selecciona tu género',
                'required' => true,
            ])->label(false) ?>
        </div>
    </div>
    <?= $form->field($modelComentario, 'contenido', [
        'inputOptions' => [
            'placeholder' => 'Escribe tu respuesta...',
            'class' => 'form-control',
            'rows' => 3,
            'maxlength' => 480
        ]
    ])->textarea()->label(false) ?>
    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-paper-plane me-2"></i> Publicar respuesta
        </button>
    </div>
    <?php ActiveForm::end(); ?>

    <br>

    <!-- Sección de comentarios -->
    <div id="comments-section-<?= $post->id ?>" class="comments-container">
        <?php foreach ($post->getSubcomentarios()->all() as $comentario): ?>
            <?= $this->render('_comentario', [
                'comentario' => $comentario,
                'modelComentario' => $modelComentario,
            ]) ?>
        <?php endforeach; ?>
    </div>
<?php Modal::end(); ?> 