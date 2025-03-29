<?php
/* @var $this yii\web\View */
/* @var $modelPost app\models\Posts */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Crear nuevo chisme';
$this->registerCss("
    .create-post-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    .post-form-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    .post-form-card:hover {
        transform: translateY(-5px);
    }
    .form-title {
        color: #2c3e50;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 0.8rem 1rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74,144,226,0.25);
    }
    .btn-publish {
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        border: none;
        border-radius: 12px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-publish:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(74,144,226,0.3);
    }
    .character-count {
        color: #6c757d;
        font-size: 0.875rem;
        text-align: right;
        margin-top: 0.5rem;
    }
");
?>

<div class="create-post-container">
    <h1 class="form-title"><?= Html::encode($this->title) ?></h1>

    <div class="card post-form-card">
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin([
                'action' => ['/site/create-post'],
                'options' => ['class' => 'd-flex flex-column gap-4']
            ]); ?>

            <div class="row g-4">
                <div class="col-md-4">
                    <?= $form->field($modelPost, 'age', [
                        'inputOptions' => [
                            'type' => 'number',
                            'min' => 1,
                            'max' => 120,
                            'class' => 'form-control',
                            'placeholder' => 'Tu edad',
                            'required' => true,
                        ],
                        'template' => '{input}',
                        'options' => ['class' => 'form-group']
                    ]) ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($modelPost, 'genre')->dropDownList([
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

            <div class="form-group">
                <?= $form->field($modelPost, 'contenido', [
                    'inputOptions' => [
                        'placeholder' => '¿Qué chisme quieres compartir hoy?',
                        'class' => 'form-control',
                        'rows' => 6,
                        'maxlength' => 480,
                        'id' => 'post-content'
                    ],
                    'template' => '{input}<div class="character-count"><span id="char-count">0</span>/480 caracteres</div>',
                    'options' => ['class' => 'form-group']
                ])->textarea()->label(false) ?>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-publish">
                    <i class="fa fa-paper-plane me-2"></i> Publicar
                </button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    document.getElementById('post-content').addEventListener('input', function() {
        const maxLength = 480;
        const currentLength = this.value.length;
        document.getElementById('char-count').textContent = currentLength;
        
        if (currentLength > maxLength) {
            this.value = this.value.substring(0, maxLength);
        }
    });
");
?>