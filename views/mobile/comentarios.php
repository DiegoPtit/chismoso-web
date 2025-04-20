<?php
// Archivo comentarios.php para la vista de comentarios
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Comentarios';

// Incluir los estilos CSS para las tarjetas
require '_partials/_styles.php';
?>

<div class="container-fluid p-0 my-3">
    <div class="row">
        <div class="col-12">
            <!-- Botón Volver Atrás y título -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="<?= Yii::$app->urlManager->createUrl(['mobile/index']) ?>" class="btn btn-primary rounded-pill shadow px-3 py-2">
                    <i class="fas fa-arrow-left fs-5"></i>
                </a>
                <h1 class="text-center fw-bold my-0">Comentarios</h1>
                <div style="width: 50px;"></div> <!-- Elemento vacío para equilibrar el flex, ahora más pequeño -->
            </div>
            
            <!-- Sección 1: Post Original -->
            <div class="mb-4">
                <h2 class="fs-5 fw-bold mb-3">Post Original</h2>
                <?php require '_partials/_post_card.php'; ?>
            </div>
            
            <!-- Sección 2: Formulario para responder -->
            <div class="card rounded-4 shadow mb-4">
                <div class="card-header py-3 px-4">
                    <h2 class="fs-5 fw-bold m-0">Responder a este post</h2>
                </div>
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin([
                        'id' => 'comment-form',
                        'action' => ['/mobile/create-comment'],
                        'options' => ['class' => 'needs-validation'],
                    ]); ?>
                    
                    <!-- Campo oculto para el padre_id -->
                    <?= Html::hiddenInput('Posts[padre_id]', $post->id) ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <?= Html::input('number', 'Posts[age]', '', [
                                    'class' => 'form-control',
                                    'id' => 'age',
                                    'placeholder' => 'Edad',
                                    'min' => '1',
                                    'max' => '120',
                                    'required' => true
                                ]) ?>
                                <label for="age">Edad</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <?= Html::dropDownList('Posts[genre]', '', [
                                    '0' => 'Prefiero no decirlo',
                                    '1' => 'Hombre',
                                    '2' => 'Mujer'
                                ], [
                                    'class' => 'form-select',
                                    'id' => 'genre',
                                    'required' => true
                                ]) ?>
                                <label for="genre">Género</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <?= Html::textarea('Posts[contenido]', '', [
                            'class' => 'form-control',
                            'id' => 'contenido',
                            'placeholder' => 'Escribe tu comentario aquí',
                            'style' => 'height: 150px',
                            'required' => true,
                            'maxlength' => 480
                        ]) ?>
                        <label for="contenido">Comentario</label>
                        <div class="form-text text-end" id="contador">0/480</div>
                    </div>
                    
                    <div class="d-grid">
                        <?= Html::submitButton('<i class="fas fa-paper-plane me-2"></i> Enviar comentario', [
                            'class' => 'btn btn-primary btn-lg rounded-pill',
                            'id' => 'submit-comment'
                        ]) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            
            <!-- Sección 3: Comentarios -->
            <div class="mb-4">
                <h2 class="fs-5 fw-bold mb-3">Comentarios</h2>
                
                <?php if (empty($comentarios)): ?>
                    <div class="modal fade" id="noCommentsModal" tabindex="-1" aria-labelledby="noCommentsModalLabel" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="noCommentsModalLabel">
                                        <i class="fas fa-info-circle me-2"></i>Información
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    No hay comentarios disponibles para este post.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center p-4">
                        No hay comentarios disponibles para este post.
                    </div>
                    <?php 
                    $this->registerJs("
                        document.addEventListener('DOMContentLoaded', function() {
                            var noCommentsModal = new bootstrap.Modal(document.getElementById('noCommentsModal'));
                            noCommentsModal.show();
                        });
                    ");
                    ?>
                <?php else: ?>
                    <div class="comentarios-container">
                        <?php foreach ($comentarios as $comentario): ?>
                            <?php require '_partials/_comment_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center d-flex align-items-center justify-content-center">
                <img src="" class="modal-image img-fluid" alt="Imagen a tamaño completo">
            </div>
        </div>
    </div>
</div>

<?php
// Script para el contador de caracteres y autocompletado
$this->registerJs('
    $(document).ready(function() {
        // Contador de caracteres
        $("#contenido").on("input", function() {
            var charCount = $(this).val().length;
            $("#contador").text(charCount + "/480");
        });

        // Inicializar contador
        $("#contador").text($("#contenido").val().length + "/480");
        
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="tooltip"]\'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Envío del formulario mediante AJAX
        $("#comment-form").on("submit", function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $("#submit-comment").prop("disabled", true).html("<i class=\"fas fa-spinner fa-spin me-2\"></i> Enviando...");
                },
                success: function(response) {
                    if (response.success) {
                        // Limpiar formulario
                        $("#contenido").val("");
                        $("#contador").text("0/480");
                        
                        // Mostrar mensaje de éxito
                        alert(response.message);
                        
                        // Recargar la página para mostrar el nuevo comentario
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                complete: function() {
                    $("#submit-comment").prop("disabled", false).html("<i class=\"fas fa-paper-plane me-2\"></i> Enviar comentario");
                }
            });
        });
    });
');
?>

<?php
// Incluir el script para la funcionalidad de los posts
require '_partials/_posts_script.php';
?> 