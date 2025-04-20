<?php
/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use app\models\Usuarios;

$this->title = 'El Chismoso - Iniciar Sesión';
?>

<div class="site-login">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> El Chismoso</h1>
        <p class="lead text-center mb-4">Inicia sesión para participar en el foro</p>

        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="forum-container">
                    <div class="forum-post">
                        <div class="forum-post-header">
                            <div class="avatar user-neutral">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="post-info">
                                <span class="username">Iniciar Sesión</span>
                                <span class="post-date">
                                    <i class="far fa-user"></i> Acceso a usuarios registrados
                                </span>
                            </div>
                        </div>
                        
                        <div class="forum-post-content login-content">
                            <?php $form = ActiveForm::begin([
                                'id' => 'login-form',
                                'layout' => 'default',
                                'fieldConfig' => [
                                    'template' => "{label}\n{input}\n{error}",
                                    'labelOptions' => ['class' => 'form-label'],
                                    'inputOptions' => ['class' => 'form-control mb-3'],
                                    'errorOptions' => ['class' => 'invalid-feedback'],
                                ],
                            ]); ?>

                            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Nombre de usuario']) ?>

                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Contraseña']) ?>

                            <?= $form->field($model, 'rememberMe')->checkbox([
                                'template' => "<div class=\"form-check mb-3\">{input} {label}</div>\n{error}",
                                'class' => 'form-check-input',
                                'labelOptions' => ['class' => 'form-check-label'],
                            ]) ?>

                            <div class="form-group mb-3">
                                <?= Html::submitButton('Iniciar Sesión', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                        
                        <div class="forum-post-actions justify-content-center">
                            <button type="button" class="btn-forum" data-bs-toggle="modal" data-bs-target="#registroModal">
                                <i class="fas fa-user-plus"></i> Registrarse
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin([
    'id' => 'registroModal',
    'title' => '<h4><i class="fas fa-user-plus"></i> Registro de Usuario</h4>',
    'options' => ['class' => 'modal fade'],
    'bodyOptions' => ['class' => 'p-4'],
    'headerOptions' => ['class' => 'modal-header bg-primary'],
]); ?>

<?php $formRegistro = ActiveForm::begin([
    'id' => 'registro-form',
    'action' => ['site/register'],
]); ?>

<?php $modelRegistro = new Usuarios(); ?>

<?= $formRegistro->field($modelRegistro, 'user')->textInput(['maxlength' => true, 'class' => 'form-control mb-3'])->label('Nombre de usuario') ?>

<?= $formRegistro->field($modelRegistro, 'pwd')->passwordInput(['maxlength' => true, 'class' => 'form-control mb-3'])->label('Contraseña') ?>

<?= $formRegistro->field($modelRegistro, 'birthday')->input('date', ['class' => 'form-control mb-3'])->label('Fecha de nacimiento') ?>

<div class="form-group">
    <?= Html::submitButton('Registrar', ['class' => 'btn btn-primary w-100']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php Modal::end(); ?>

<style>
/* Importar estilos comunes del foro */
.forum-container {
    background-color: #f9f9f9;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
    margin-bottom: 30px;
}

.forum-post {
    padding: 20px;
    background-color: #fff;
    border-bottom: 1px solid #e0e0e0;
}

.forum-post-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    flex-shrink: 0;
    font-size: 1.2rem;
}

.avatar.user-neutral {
    background-color: #6c5ce7;
}

.post-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.username {
    font-weight: 600;
    color: #444;
    font-size: 1.1rem;
}

.post-date {
    color: #777;
    font-size: 0.9rem;
}

.forum-post-content {
    margin-bottom: 20px;
    line-height: 1.5;
}

.forum-post-content.login-content {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #e9ecef;
}

.forum-post-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.justify-content-center {
    justify-content: center;
}

.btn-forum {
    border: none;
    background: #f1f1f1;
    color: #555;
    padding: 8px 15px;
    border-radius: 3px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-forum:hover {
    background: #6c5ce7;
    color: white;
    transform: translateY(-2px);
}

/* Estilos para formularios */
.form-control {
    border-radius: 5px;
    padding: 12px;
    border: 1px solid #ced4da;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #495057;
}

.form-check-input {
    margin-top: 0.2rem;
}

.btn {
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #6c5ce7;
    border-color: #6c5ce7;
}

.btn-primary:hover {
    background-color: #5f4dd0;
    border-color: #5f4dd0;
    transform: translateY(-2px);
}

/* Estilos para el modal */
.modal-content {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    border-bottom: none;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    padding: 1.25rem;
}

.modal-header h4 {
    color: white;
    margin: 0;
    font-size: 1.25rem;
}

.bg-primary {
    background-color: #6c5ce7 !important;
}

/* Estilos responsive */
@media (max-width: 768px) {
    .forum-post {
        padding: 15px;
    }
    
    .forum-post-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .avatar {
        margin-bottom: 10px;
    }
    
    .forum-post-actions {
        flex-direction: column;
    }
    
    .btn-forum, .btn {
        width: 100%;
    }
}
</style> 