<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="container mt-5">
    <?php if (isset($post_id) && !empty($post_id)) : ?>
        <?php 
            // Buscar el post según su id
            $post = \app\models\Posts::findOne($post_id);
            if ($post === null) : 
        ?>
            <div class="alert alert-warning">
                <strong>Atención!</strong> Post no encontrado.
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3>Reportar Post</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3"><strong>Contenido del Post:</strong></p>
                    <div class="alert alert-light">
                        <?= Html::encode($post->contenido) ?>
                    </div>
                    <?php $form = ActiveForm::begin([
                        'action' => ['site/create-reported-posts'], // Acción que procesará el reporte de posts
                        'method' => 'post',
                    ]); ?>
                        <?= Html::hiddenInput('post_id', $post->id) ?>
                        <div class="form-group">
                            <label><strong>Seleccione el motivo del reporte:</strong></label>
                            <div class="form-check">
                                <?= Html::radio('motivo', false, ['value' => 'HATE_LANG', 'class' => 'form-check-input', 'id' => 'motivo1']) ?>
                                <?= Html::label('Lenguaje que incita al odio', 'motivo1', ['class' => 'form-check-label']) ?>
                            </div>
                            <div class="form-check">
                                <?= Html::radio('motivo', false, ['value' => 'KIDS_HASSARAMENT', 'class' => 'form-check-input', 'id' => 'motivo2']) ?>
                                <?= Html::label('Pedofilia', 'motivo2', ['class' => 'form-check-label']) ?>
                            </div>
                            <div class="form-check">
                                <?= Html::radio('motivo', false, ['value' => 'SENSIBLE_CONTENT', 'class' => 'form-check-input', 'id' => 'motivo3']) ?>
                                <?= Html::label('Contenido inapropiado (Incluso para un mayor de edad)', 'motivo3', ['class' => 'form-check-label']) ?>
                            </div>
                            <div class="form-check">
                                <?= Html::radio('motivo', false, ['value' => 'SCAM', 'class' => 'form-check-input', 'id' => 'motivo4']) ?>
                                <?= Html::label('Estafa', 'motivo4', ['class' => 'form-check-label']) ?>
                            </div>
                            <div class="form-check">
                                <?= Html::radio('motivo', false, ['value' => 'SPAM', 'class' => 'form-check-input', 'id' => 'motivo5']) ?>
                                <?= Html::label('Spam', 'motivo5', ['class' => 'form-check-label']) ?>
                            </div>
                            <div class="form-check">
                                <?= Html::radio('motivo', false, ['value' => 'RACIST_LANG', 'class' => 'form-check-input', 'id' => 'motivo6']) ?>
                                <?= Html::label('Racismo o Xenofobia', 'motivo6', ['class' => 'form-check-label']) ?>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <?= Html::submitButton('Enviar Reporte', ['class' => 'btn btn-danger btn-lg btn-block']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php elseif (isset($usuario_id) && !empty($usuario_id)) : ?>
        <?php 
            // Buscar el usuario según su id
            $usuario = \app\models\Usuarios::findOne($usuario_id);
            if ($usuario === null) : 
        ?>
            <div class="alert alert-warning">
                <strong>Atención!</strong> Usuario no encontrado.
            </div>
        <?php else: 
                // Buscar la última ubicación del usuario en la tabla logs
                $log = \app\models\Logs::find()
                    ->where(['usuario_id' => $usuario_id])
                    ->orderBy(['fecha_hora' => SORT_DESC])
                    ->one();
        ?>
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3>Reportar Usuario</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nickname:</strong> <?= Html::encode($usuario->user) ?></p>
                    <p><strong>Última Ubicación de Acceso:</strong> <?= $log ? Html::encode($log->ubicacion) : 'No disponible' ?></p>
                    <?php $form = ActiveForm::begin([
                        'action' => ['site/create-reported-users'], // Acción que procesará el reporte de usuarios
                        'method' => 'post',
                    ]); ?>
                        <?= Html::hiddenInput('usuario_id', $usuario->id) ?>
                        <div class="form-group mt-3">
                            <?= Html::submitButton('Enviar Reporte', ['class' => 'btn btn-danger btn-lg btn-block']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <p>No se ha especificado qué reportar.</p>
        </div>
    <?php endif; ?>
</div>
