<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $comentario app\models\Posts */
/* @var $modelComentario app\models\Posts */
switch ($comentario->genre) {
    case 1:
        $headerFooterColor = "#aeb3ff";
        $bodyColor = "#e4e6ff";
        $icon = 'fa-male';
        break;
    case 2:
        $headerFooterColor = "#ffb3fa";
        $bodyColor = "#ffddfd";
        $icon = 'fa-female';
        break;
    default:
        $headerFooterColor = "#c2c2c2";
        $bodyColor = "#e5e5e5";
        $icon = 'fa-user-secret';
        break;
}
?>
<!-- Contenedor del comentario -->
<div class="card mb-3 subcomments" id="comentario-<?= $comentario->id ?>">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: <?= $headerFooterColor ?>; color: #000;">
        <span><?= Html::encode('@' . $comentario->id) ?></span>
        <span><?= Yii::$app->formatter->asDatetime($comentario->created_at) ?></span>
        <span>
            <i class="fa <?= $icon ?> me-2"></i>
            <strong><?= $comentario->age ?> años</strong>
        </span>
    </div>

    <!-- Cuerpo del comentario -->
    <div class="card-body" style="background-color: <?= $bodyColor ?>;">
        <p><?= Html::encode($comentario->contenido) ?></p>
    </div>

    <!-- Footer con botones -->
    <div class="card-footer" style="background-color: <?= $headerFooterColor ?>; color: #000;">
        <div class="d-flex align-items-center">
            <?= Html::beginForm(['/site/like', 'id' => $comentario->id, 'modal' => $comentario->padre_id], 'post') ?>
                <button type="submit" class="btn btn-link text-dark me-3">
                    <i class="fa fa-thumbs-up"></i> <strong><?= $comentario->likes ?></strong>
                </button>
            <?= Html::endForm() ?>

            <?= Html::beginForm(['/site/dislike', 'id' => $comentario->id, 'modal' => $comentario->padre_id], 'post') ?>
                <button type="submit" class="btn btn-link text-dark me-3">
                    <i class="fa fa-thumbs-down"></i> <strong><?= $comentario->dislikes ?></strong>
                </button>
            <?= Html::endForm() ?>

            <button class="btn btn-link text-dark me-3" type="button" data-bs-toggle="collapse" data-bs-target="#subcomentarios-<?= $comentario->id ?>, #formComentario-<?= $comentario->id ?>">
                <i class="fa fa-comment"></i>
            </button>

            <?php if ($comentario->getSubcomentarios()->exists()): ?>
                <button class="btn btn-link text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#subcomentarios-<?= $comentario->id ?>">
                    <i class="fa fa-eye"></i> Ver/Ocultar Subcomentarios
                </button>
            <?php endif; ?>

            <span>
                <?= Html::a('<i class="fa fa-flag"></i> <strong style="margin-right: 10px;"></strong>', ['site/reportar', 'post_id' => $comentario->id], [
                    'class' => 'icono-reporte',
                    'title' => 'Reportar Chisme',
                ]) ?>
                <?= Html::a('<i class="fa fa-user-times"></i> <strong style="margin-right: 10px;"></strong>', ['site/reportar', 'usuario_id' => $comentario->usuario->id], [
                    'class' => 'icono-reporte',
                    'title' => 'Reportar Usuario',
                ]) ?>
            </span>
        </div>
    </div>

    <!-- Formulario para responder al comentario -->
    <div class="collapse" id="formComentario-<?= $comentario->id ?>">
        <div class="card card-body">
            <?php $form = ActiveForm::begin([
                'action' => ['/site/comment', 'post_id' => $comentario->id, 'modal' => $comentario->padre_id],
                'options' => ['class' => 'd-flex flex-column gap-3']
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
        </div>
    </div>

    <!-- Subcomentarios -->
    <div class="collapse mt-2" id="subcomentarios-<?= $comentario->id ?>">
        <?php foreach ($comentario->getSubcomentarios()->orderBy(['created_at' => SORT_DESC])->all() as $subcomentario): ?>
            <?= $this->render('_comentario', ['comentario' => $subcomentario, 'modelComentario' => $modelComentario]) ?>
        <?php endforeach; ?>
    </div>
</div>
