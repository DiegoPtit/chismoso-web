<?php
/* @var $this yii\web\View */
/* @var $modelPost app\models\Posts */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Crear nuevo post';

?>
<div class="site-create-post">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'action' => ['/site/create-post'],
                'options' => ['class' => 'd-flex flex-column gap-3']
            ]); ?>

            <div class="row g-3">
                <div class="col-md-3">
                    <?= $form->field($modelPost, 'age', [
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

            <?= $form->field($modelPost, 'contenido', [
                'inputOptions' => [
                    'placeholder' => 'Escribe tu post...',
                    'class' => 'form-control',
                    'rows' => 5,
                    'maxlength' => 480
                ]
            ])->textarea()->label(false) ?>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane me-2"></i> Publicar
                </button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>