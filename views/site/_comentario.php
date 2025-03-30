<?php
/**
 * @var \app\models\Posts $comentario
 * @var \app\models\Posts $modelComentario
 */

use yii\helpers\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;

// Definir colores según el género del comentario
$headerFooterColor = match ($comentario->genre) {
    1 => "#aeb3ff",  // Hombre
    2 => "#ffb3fa",  // Mujer
    default => "#c2c2c2"  // Incógnito
};

$bodyColor = match ($comentario->genre) {
    1 => "#e4e6ff",
    2 => "#ffddfd",
    default => "#e5e5e5"
};

$icon = match ($comentario->genre) {
    1 => 'fa-male',
    2 => 'fa-female',
    default => 'fa-user-secret'
};
?>

<div class="card mb-3 dashboard-card subcomments" id="comentario-<?= $comentario->id ?>">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="background: linear-gradient(45deg, <?= $headerFooterColor ?>, <?= $bodyColor ?>); color: #000;">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <i class="fa <?= $icon ?> me-2"></i>
            <span class="me-2"><?= Html::encode('@' . $comentario->id) ?></span>
            <span class="badge bg-light text-dark"><?= $comentario->age ?> años</span>
            <?= $this->render('_block-buttons', [
                'post_id' => $comentario->id,
                'usuario_id' => $comentario->usuario_id
            ]) ?>
        </div>
        <small class="text-muted"><?= Yii::$app->formatter->asDatetime($comentario->created_at) ?></small>
    </div>
    <div class="card-body" style="background-color: <?= $bodyColor ?>;">
        <p class="mb-0"><?= Html::encode($comentario->contenido) ?></p>
    </div>
    <div class="card-footer" style="background: linear-gradient(45deg, <?= $headerFooterColor ?>, <?= $bodyColor ?>); color: #000;">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="d-flex gap-2">
                <?= Html::beginForm(['/site/like-comment', 'id' => $comentario->id], 'post', ['class' => 'comment-like-form']) ?>
                    <button type="submit" class="btn btn-link text-dark p-0">
                        <i class="fa fa-thumbs-up"></i> <span class="btn-label"></span> <strong class="likes-count"><?= $comentario->likes ?></strong>
                    </button>
                <?= Html::endForm() ?>

                <?= Html::beginForm(['/site/dislike-comment', 'id' => $comentario->id], 'post', ['class' => 'comment-dislike-form']) ?>
                    <button type="submit" class="btn btn-link text-dark p-0">
                        <i class="fa fa-thumbs-down"></i> <span class="btn-label"></span> <strong class="dislikes-count"><?= $comentario->dislikes ?></strong>
                    </button>
                <?= Html::endForm() ?>

                <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="collapse" data-bs-target="#formComentario-<?= $comentario->id ?>">
                    <i class="fa fa-comment"></i> <span class="btn-label">Responder</span>
                </button>
            </div>

            <div class="ms-auto d-flex gap-2">
                <?= Html::a('<i class="fa fa-flag"></i> <span class="btn-label">Reportar</span>', 
                    ['site/reportar', 'post_id' => $comentario->id], 
                    ['class' => 'icono-reporte', 'title' => 'Reportar Chisme']) ?>
                <?= Html::a('<i class="fa fa-user-times"></i> <span class="btn-label">Reportar Usuario</span>', 
                    ['site/reportar', 'usuario_id' => $comentario->usuario->id], 
                    ['class' => 'icono-reporte', 'title' => 'Reportar Usuario']) ?>
            </div>
        </div>
    </div>

    <!-- Formulario para responder al comentario -->
    <div class="collapse" id="formComentario-<?= $comentario->id ?>">
        <div class="card card-body comment-form-container">
            <?php $form = ActiveForm::begin([
                'action' => ['/site/comment', 'post_id' => $comentario->id, 'modal' => $comentario->padre_id],
                'options' => [
                    'class' => 'd-flex flex-column gap-3 comment-form',
                    'data-post-id' => $comentario->id
                ]
            ]); ?>
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <?= $form->field($modelComentario, 'age', [
                        'inputOptions' => [
                            'type' => 'number',
                            'min' => 1,
                            'max' => 120,
                            'class' => 'form-control form-control-lg',
                            'placeholder' => 'Tu edad',
                            'required' => true,
                        ],
                        'template' => '{input}'
                    ]) ?>
                </div>
                <div class="col-12 col-md-9">
                    <?= $form->field($modelComentario, 'genre', [
                        'inputOptions' => [
                            'class' => 'form-select form-select-lg',
                        ],
                        'template' => '{input}'
                    ])->dropDownList([
                        0 => 'Prefiero no decir',
                        1 => 'Hombre',
                        2 => 'Mujer'
                    ], [
                        'prompt' => 'Selecciona tu género',
                        'required' => true,
                    ]) ?>
                </div>
            </div>
            <?= $form->field($modelComentario, 'contenido', [
                'inputOptions' => [
                    'placeholder' => 'Escribe tu respuesta...',
                    'class' => 'form-control form-control-lg',
                    'rows' => 3,
                    'maxlength' => 480
                ],
                'template' => '{input}'
            ])->textarea() ?>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa fa-paper-plane me-2"></i> Publicar respuesta
                </button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Subcomentarios -->
    <div class="collapse mt-2" id="subcomentarios-<?= $comentario->id ?>">
        <?php 
        $subcomentarios = $comentario->getSubcomentarios()->orderBy(['created_at' => SORT_DESC])->all();
        foreach ($subcomentarios as $subcomentario): 
        ?>
            <?= $this->render('_comentario', ['comentario' => $subcomentario, 'modelComentario' => $modelComentario]) ?>
        <?php endforeach; ?>
    </div>

    <?php if ($comentario->getSubcomentarios()->exists()): ?>
        <div class="card-footer bg-light border-top-0">
            <button class="btn btn-link w-100 text-center text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#subcomentarios-<?= $comentario->id ?>">
                <i class="fa fa-comments me-2"></i>
                <span class="show-comments">Ver comentarios</span>
                <span class="hide-comments d-none">Ocultar comentarios</span>
            </button>
        </div>
    <?php endif; ?>
</div>

<style>
.dashboard-card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    margin-bottom: 1.5rem;
}
.dashboard-card:hover {
    transform: translateY(-5px);
}
.card-header {
    border-radius: 15px 15px 0 0 !important;
    padding: 1.5rem;
}
.card-body {
    padding: 1.5rem;
}
.card-footer {
    border-radius: 0 0 15px 15px;
    padding: 1rem 1.5rem;
}
.btn-link {
    text-decoration: none;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
}
.btn-link:hover {
    background-color: rgba(0,0,0,0.1);
}
.icono-reporte {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
    color: #d93e3e;
    text-decoration: none;
}
.icono-reporte:hover {
    background-color: rgba(0,0,0,0.1);
}
.subcomments {
    margin-left: 0.5rem;
}
@media (min-width: 768px) {
    .subcomments {
        margin-left: 1.5rem;
    }
}
.comment-form-container {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.comment-form .form-control,
.comment-form .form-select {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
}
.comment-form .form-control:focus,
.comment-form .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}
.comment-form textarea.form-control {
    resize: none;
    min-height: 100px;
}
.comment-form .btn-primary {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}
.comment-form .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.comment-form .form-control::placeholder {
    color: #adb5bd;
}
@media (max-width: 767px) {
    .card-footer {
        padding: 0.75rem;
    }
    .card-footer .d-flex {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .card-footer .btn-link,
    .card-footer .icono-reporte {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    .card-footer .btn-label {
        display: none;
    }
    .card-footer .btn i,
    .card-footer .icono-reporte i {
        margin-right: 0 !important;
    }
    .card-footer .ms-auto {
        margin-left: 0 !important;
        width: 100%;
        justify-content: flex-end;
    }
    .subcomments {
        margin-left: 0.25rem;
    }
    .comment-form-container {
        padding: 1rem;
    }
    .comment-form .form-control,
    .comment-form .form-select {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    .comment-form .btn-primary {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
}
</style>

<?php
$this->registerJs(<<<JS
    // Manejo de likes y dislikes con AJAX para comentarios
    $(document).on('submit', '.comment-like-form, .comment-dislike-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var isLike = form.hasClass('comment-like-form');
        var countElement = isLike ? form.find('.likes-count') : form.find('.dislikes-count');
        
        $.post(url, function(response) {
            if (response.success) {
                countElement.text(response.count);
            } else {
                alert(response.message || 'Error al procesar la solicitud');
            }
        }).fail(function() {
            alert('Error al procesar la solicitud');
        });
    });

    // Función para actualizar el texto del botón
    function updateButtonText(button, isExpanded) {
        var showText = button.find('.show-comments');
        var hideText = button.find('.hide-comments');
        
        if (isExpanded) {
            showText.addClass('d-none');
            hideText.removeClass('d-none');
        } else {
            showText.removeClass('d-none');
            hideText.addClass('d-none');
        }
    }

    // Manejo del botón de ver/ocultar comentarios
    $(document).on('click', '[data-bs-toggle="collapse"]', function() {
        var target = $(this).data('bs-target');
        var isExpanded = $(target).hasClass('show');
        updateButtonText($(this), isExpanded);
    });

    // Manejo del evento de colapso
    $(document).on('shown.bs.collapse hidden.bs.collapse', '.collapse', function() {
        var button = $('[data-bs-target="#' + $(this).attr('id') + '"]');
        var isExpanded = $(this).hasClass('show');
        updateButtonText(button, isExpanded);
    });

    // Manejo del formulario de comentarios con AJAX
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var postId = form.data('post-id');
        var submitButton = form.find('button[type="submit"]');
        var originalButtonText = submitButton.html();
        
        // Deshabilitar el botón y mostrar loading
        submitButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Enviando...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    // Cerrar el formulario
                    form.closest('.collapse').collapse('hide');
                    
                    // Limpiar el formulario
                    form[0].reset();
                    
                    // Obtener el contenedor de subcomentarios
                    var subcommentsContainer = $('#subcomentarios-' + postId);
                    console.log('Buscando contenedor:', '#subcomentarios-' + postId);
                    console.log('Contenedor encontrado:', subcommentsContainer.length > 0);
                    
                    if (response.isMainPost) {
                        // Si es un comentario al post principal, recargar todo el modal
                        $.get(window.location.pathname, { modal: postId }, function(data) {
                            var newModalContent = $(data).find('#commentModal' + postId).html();
                            $('#commentModal' + postId).html(newModalContent);
                        });
                    } else {
                        // Si es un subcomentario
                        if (subcommentsContainer.length) {
                            console.log('Contenedor de subcomentarios encontrado');
                            console.log('HTML del nuevo comentario:', response.commentHtml);
                            
                            // Expandir la sección de subcomentarios
                            subcommentsContainer.collapse('show');
                            updateButtonText($('[data-bs-target="#subcomentarios-' + postId + '"]'), true);
                            
                            // Insertar el nuevo comentario al principio
                            if (response.commentHtml) {
                                subcommentsContainer.prepend(response.commentHtml);
                            } else {
                                console.error('No se recibió HTML del comentario');
                            }
                        } else {
                            console.error('No se encontró el contenedor de subcomentarios');
                            // Intentar recargar la sección completa
                            $.get(window.location.pathname, { modal: postId }, function(data) {
                                var newContent = $(data).find('#subcomentarios-' + postId).html();
                                if (newContent) {
                                    subcommentsContainer.html(newContent);
                                }
                            });
                        }
                    }
                } else {
                    alert(response.message || 'Error al publicar el comentario');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    alert(response.message || 'Error al procesar la solicitud. Por favor, intente de nuevo.');
                } catch (e) {
                    alert('Error al procesar la solicitud. Por favor, intente de nuevo.');
                }
            },
            complete: function() {
                // Restaurar el botón
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
JS
);
?>
