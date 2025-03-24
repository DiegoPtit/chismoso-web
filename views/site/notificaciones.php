<?php
/* @var $this yii\web\View */
/* @var $notificaciones app\models\Notificaciones[] */

use yii\helpers\Html;
use yii\bootstrap5\Modal;

$this->title = 'Notificaciones';

?>
<div class="site-notificaciones">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Botón flotante de recarga -->
    <?= Html::button('<i class="fa fa-sync"></i>', [
        'id' => 'reload-notifications',
        'class' => 'btn btn-primary btn-lg rounded-circle position-fixed',
        'style' => 'bottom: 20px; right: 20px; z-index: 1000;',
        'title' => 'Recargar notificaciones'
    ]) ?>

    <?php if (empty($notificaciones)): ?>
        <p class="text-center mt-5">No tienes nuevas notificaciones.</p>
        <br>
        <p class="text-center mt-5">Nota del Creador: Tenganme paciencia, apenas puedo descansar. 😫</p>
        <p class="text-center mt-5">Si deseas apoyarme, mi Binance Pay es newdblogs@gmail.com o mi dirección crypto es 0xacddd2c165c0c2d3b55a6ebe3a2400859a49cb7b en la red BSC para USDT (TetherUS).</p>
        <p class="text-center mt-5">Te lo agradecería mucho, me ayudaría a seguir creciendo! 💙</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($notificaciones as $notificacion): ?>
                <?php 
                    $comentario = $notificacion->comentario;
                    $postOriginal = $notificacion->postOriginal;
                ?>
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">
                                        Hay un poco de chisme en tu comentario:                                        
                                    </h5>
                                    <p class="card-text">
                                        <?= Html::encode(mb_strimwidth($comentario->contenido, 0, 100, '...')) ?>
                                    </p>
                                    <small class="text-muted">
                                        <?= Yii::$app->formatter->asDatetime($comentario->created_at) ?>
                                    </small>
                                </div>
                                <?= Html::a('Ir al post', ['/site/index', 'modal' => $postOriginal->id], [
                                    'class' => 'btn btn-primary'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para la notificación -->
                <?php Modal::begin([
                    'id' => 'modalNotificacion<?= $comentario->id ?>',
                    'title' => 'Conversación completa',
                    'size' => 'modal-lg'
                ]); ?>
                
                <!-- Contenido del modal (similar al de comentarios) -->
                <div class="card mb-3">
                    <div class="card-header">
                        <span><?= Html::encode('@' . $postOriginal->usuario->id) ?></span>
                        <span><?= Yii::$app->formatter->asDatetime($postOriginal->created_at) ?></span>
                    </div>
                    <div class="card-body">
                        <p><?= Html::encode($postOriginal->contenido) ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span><?= Html::encode('@' . $comentario->usuario->id) ?></span>
                        <span><?= Yii::$app->formatter->asDatetime($comentario->created_at) ?></span>
                    </div>
                    <div class="card-body">
                        <p><?= Html::encode($comentario->contenido) ?></p>
                    </div>
                    <div class="card-footer">
                        <?= Html::beginForm(['/site/like', 'id' => $comentario->id], 'post') ?>
                            <button type="submit" class="btn btn-link text-dark me-3">
                                <i class="fa fa-thumbs-up"></i> <?= $comentario->likes ?>
                            </button>
                        <?= Html::endForm() ?>

                        <?= Html::beginForm(['/site/dislike', 'id' => $comentario->id], 'post') ?>
                            <button type="submit" class="btn btn-link text-dark me-3">
                                <i class="fa fa-thumbs-down"></i> <?= $comentario->dislikes ?>
                            </button>
                        <?= Html::endForm() ?>
                    </div>
                </div>

                <?php Modal::end(); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Script para recarga AJAX
$this->registerJs('
    $("#reload-notifications").on("click", function() {
        $.ajax({
            url: "' . Yii::$app->urlManager->createUrl(['site/notificaciones']) . '",
            type: "GET",
            success: function(data) {
                $(".site-notificaciones").html($(data).find(".site-notificaciones").html());
            }
        });
    });
');
?>