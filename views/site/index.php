<?php
/* @var $this yii\web\View */
/* @var $posts app\models\Posts[] */
/* @var $modelComentario app\models\Posts */

use yii\helpers\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;

$this->title = 'Chismoso App';

?>
<div class="site-index">
    <?php if (empty($posts)): ?>
        <p class="text-center mt-5">No hay posts disponibles, intente de nuevo.</p>
    <?php else: ?>

        <style>
            /* Estilo para comentarios anidados */
            .subcomments {
                border-left: 3px solid #ddd;
                padding-left: 1.5rem;
                margin-left: 1rem;
            }

            /* En tu archivo CSS principal */
            .btn-flotante {
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                transition: all 0.3s ease;
            }

            .btn-flotante:hover {
                transform: scale(1.1);
                box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            }
        </style>

        <?php foreach ($posts as $post): ?>
            <?php
                // Definir colores según el género del post
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
            <!-- Tarjeta del post principal -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: <?= $headerFooterColor ?>; color: #000;">
                    <span><?= Html::encode('@' . $post->usuario->id) ?></span>
                    <span><?= Yii::$app->formatter->asDatetime($post->created_at) ?></span>
                    <span>
                        <i class="fa <?= $icon ?> me-2"></i> <!-- Ícono según el género -->
                        <strong><?= $post->age ?> años</strong>
                    </span>
                </div>
                <div class="card-body" style="background-color: <?= $bodyColor ?>;">
                    <p><?= Html::encode($post->contenido) ?></p>
                </div>
                <div class="card-footer" style="background-color: <?= $headerFooterColor ?>; color: #000;">
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
                        <?= Html::a('<i class="fa fa-comment"></i>', '#', [
                            'class' => 'text-dark',
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#commentModal' . $post->id,
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Modal de comentarios para el post -->
            <?php Modal::begin([
                'id' => 'commentModal' . $post->id,
                'title' => 'Comentarios',
                'size' => 'modal-lg',
            ]); ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: <?= $headerFooterColor ?>; color: #000;">
                        <span><?= Html::encode('@' . $post->usuario->id) ?></span>
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
                            <?= Html::beginForm(['/site/like', 'id' => $post->id, 'modal' => $post->id], 'post') ?>
                                <button type="submit" class="btn btn-link text-dark me-3">
                                    <i class="fa fa-thumbs-up"></i> <strong><?= $post->likes ?></strong>
                                </button>
                            <?= Html::endForm() ?>

                            <?= Html::beginForm(['/site/dislike', 'id' => $post->id, 'modal' => $post->id], 'post') ?>
                                <button type="submit" class="btn btn-link text-dark me-3">
                                    <i class="fa fa-thumbs-down"></i> <strong><?= $post->dislikes ?></strong>
                                </button>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Formulario para comentar el post -->
                <?php $form = ActiveForm::begin([
                    'action' => ['/site/comment', 'post_id' => $post->id, 'modal' => $post->id], // ← Pasamos el modal
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

                <br>

                <!-- Renderizado recursivo de comentarios -->
                <div id="comments-section-<?= $post->id ?>">
                <?php foreach ($post->getSubcomentarios()->orderBy(['created_at' => SORT_DESC])->all() as $comentario): ?>
                    <?= $this->render('_comentario', [
                        'comentario' => $comentario,
                        'modelComentario' => $modelComentario,
                    ]) ?>
                <?php endforeach; ?>
            </div>
            <?php Modal::end(); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php echo Html::a(
    '<i class="fa fa-paper-plane"></i>',
    ['/site/create-post'],
    [
        'class' => 'btn btn-primary btn-lg rounded-circle position-fixed btn-flotante',
        'style' => 'bottom: 20px; right: 20px; z-index: 1000;'
    ]
);
?>
</div>

<?php
// Si se recibe un parámetro "modal", reabrir ese modal automáticamente
$modalId = Yii::$app->request->get('modal');
if ($modalId) {
    $this->registerJs("
        $(document).ready(function(){
            $('#commentModal$modalId').modal('show');
            $('#commentModal$modalId').on('hidden.bs.modal', function(){
                window.location.href = window.location.pathname;
            });
        });

        var modal = new bootstrap.Modal(document.getElementById('commentModal{$modalId}'));
        modal.show();
    ");
}
?>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9977380373858586"
     crossorigin="anonymous"></script>

