<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use app\models\Usuarios;

$this->title = 'Iniciar Sesión';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Por favor complete los siguientes campos para ingresar:</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'col-lg-1 col-form-label mr-lg-3'],
                    'inputOptions' => ['class' => 'col-lg-3 form-control'],
                    'errorOptions' => ['class' => 'col-lg-7 invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
            ]) ?>

            <div class="form-group">
                <div>
                    <?= Html::submitButton('Ingresar', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    <?= Html::button('Registrarse', ['class' => 'btn btn-success', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#registroModal']) ?>
                </div>
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
    'action' => ['site/register'],
]); ?>

<?php $modelRegistro = new Usuarios(); ?>

<?= $form->field($modelRegistro, 'user')->textInput(['maxlength' => true]) ?>

<?= $form->field($modelRegistro, 'pwd')->passwordInput(['maxlength' => true]) ?>

<?= $form->field($modelRegistro, 'birthday')->input('date') ?>

<div class="form-group">
    <?= Html::submitButton('Registrar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php Modal::end(); ?>