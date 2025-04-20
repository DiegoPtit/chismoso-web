<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use app\models\Usuarios;

$this->title = 'Iniciar Sesión';
?>

<div class="mobile-login">
    <div class="text-center mb-4">
        <h1 class="h3"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Por favor complete los campos para ingresar</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-3">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form-mobile',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label'],
                    'inputOptions' => ['class' => 'form-control mb-2'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"form-check mb-3\">{input} {label}</div>\n<div>{error}</div>",
            ]) ?>

            <div class="form-group mb-3">
                <?= Html::submitButton('Ingresar', ['class' => 'btn btn-primary btn-block w-100', 'name' => 'login-button']) ?>
            </div>

            <div class="form-group text-center">
                <?= Html::button('Registrarse', ['class' => 'btn btn-outline-secondary w-100', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#registroModal']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php Modal::begin([
    'id' => 'registroModal',
    'title' => '<h4>Registro de Usuario</h4>',
]); ?>

<?php $form = ActiveForm::begin([
    'id' => 'registro-form',
    'action' => ['mobile/register'],
]); ?>

<?php $modelRegistro = new Usuarios(); ?>

<?= $form->field($modelRegistro, 'user')->textInput(['maxlength' => true])->label('Nombre de usuario') ?>

<?= $form->field($modelRegistro, 'pwd')->passwordInput(['maxlength' => true])->label('Contraseña') ?>

<?= $form->field($modelRegistro, 'birthday')->input('date')->label('Fecha de nacimiento') ?>

<div class="form-group">
    <?= Html::submitButton('Registrar', ['class' => 'btn btn-primary w-100']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php Modal::end(); ?>

<style>
.mobile-login {
    max-width: 100%;
    margin: 0 auto;
}

/* Estilos para el modal en mobile */
.modal-dialog {
    margin: 10px;
    width: calc(100% - 20px);
    max-width: none;
}

.modal-content {
    border-radius: 15px;
}

.modal-body {
    padding: 20px;
}

/* Mejorando el aspecto de los inputs para móvil */
.form-control {
    height: 48px;
    font-size: 16px; /* Evita zoom en iOS */
    border-radius: 10px;
}

.btn {
    height: 48px;
    border-radius: 10px;
    font-weight: 500;
}

.btn-outline-secondary {
    border-color: #ddd;
}

.form-check-input {
    width: 20px;
    height: 20px;
    margin-top: 2px;
}

.form-check-label {
    margin-left: 8px;
    font-size: 14px;
}
</style> 