<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $post app\models\Posts
 * @var $modelComentario app\models\Posts
 */
?>

<!-- Contenido del Comentario Principal -->
<div class="card mb-3">
    <div class="card-header">@<?= $post->usuario->user ?></div>
    <div class="card-body">
        <?= Html::encode($post->contenido) ?>
    </div>
</div>

<!-- Formulario de Respuesta -->
<?php $form = ActiveForm::begin([
    'action' => ['/site/comment', 'post_id' => $post->id],
    'options' => ['class' => 'mb-4']
]) ?>

    <div class="row g-3">
        <div class="col-md-3">
            <?= $form->field($modelComentario, 'age')->textInput([
                'type' => 'number',
                'min' => 1,
                'max' => 120,
                'class' => 'form-control',
                'placeholder' => 'Tu edad'
            ])->label(false) ?>
        </div>
        
        <div class="col-md-9">
            <?= $form->field($modelComentario, 'genre')->dropDownList([
                0 => 'Prefiero no decir',
                1 => 'Hombre',
                2 => 'Mujer'
            ], [
                'class' => 'form-select',
                'prompt' => 'Selecciona tu género'
            ])->label(false) ?>
        </div>
    </div>

    <?= $form->field($modelComentario, 'contenido')->textarea([
        'rows' => 3,
        'placeholder' => 'Escribe tu respuesta...'
    ])->label(false) ?>

    <div class="text-end">
        <?= Html::submitButton('Responder', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end() ?>

<!-- Lista de Respuestas -->
<div class="comments-list">
    <?php foreach ($post->subcomentarios as $comentario): ?>
        <?= $this->render('_comentario', [
            'comentario' => $comentario,
            'modelComentario' => $modelComentario
        ]) ?>
    <?php endforeach; ?>
</div>