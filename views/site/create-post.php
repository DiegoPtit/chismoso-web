<?php
/** @var yii\web\View $this */
/** @var app\models\Posts $modelPost */

// Registrar los assets de SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

$this->title = 'El Chismoso - Crear Post';

// Generar la URL para el endpoint check-subscription
$checkSubscriptionUrl = Yii::$app->urlManager->createUrl(['site/check-subscription']);
?>

<div class="site-create-post">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> El Chismoso</h1>
        <p class="lead text-center mb-4">Comparte un nuevo chisme de forma anónima</p>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="forum-container">
                    <div class="forum-post">
                        <div class="card-header">
                            <h2 class="h4 mb-0"><i class="fas fa-edit"></i> Crear Nuevo Post</h2>
                        </div>
                        <div class="card-body">
                            <?php $form = \yii\widgets\ActiveForm::begin([
                                'id' => 'create-post-form',
                                'enableAjaxValidation' => false,
                                'options' => ['class' => 'needs-validation']
                            ]); ?>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($modelPost, 'age')->input('number', [
                                            'min' => 13,
                                            'max' => 100,
                                            'class' => 'form-control',
                                            'placeholder' => 'Ingresa tu edad'
                                        ])->label('Edad (opcional)') ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($modelPost, 'genre')->dropDownList([
                                            0 => 'Prefiero no decir',
                                            1 => 'Hombre',
                                            2 => 'Mujer'
                                        ], [
                                            'class' => 'form-control'
                                        ])->label('Género (opcional)') ?>
                                    </div>
                                </div>
                                
                                <?= $form->field($modelPost, 'contenido')->textarea([
                                    'rows' => 5,
                                    'maxlength' => 480,
                                    'class' => 'form-control',
                                    'required' => true,
                                    'placeholder' => 'Escribe tu chisme aquí...'
                                ])->label('Contenido del Post') ?>
                                <div class="character-counter">
                                    <span class="current-count">0</span>/<span class="max-count">480</span> caracteres
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label class="form-label"><i class="fas fa-image"></i> Imágenes</label>
                                    <div class="image-upload-container">
                                        <button type="button" id="image-upload-btn" class="btn btn-outline-primary">
                                            <i class="fas fa-paperclip"></i> Adjuntar imagen
                                        </button>
                                        <small class="form-text text-muted image-limit-info">
                                            Puedes adjuntar <span id="max-images">1</span> imágenes según tu suscripción
                                        </small>
                                        <?= $form->field($modelPost, 'imageFiles[]')->fileInput([
                                            'multiple' => true, 
                                            'accept' => 'image/*', 
                                            'id' => 'image-upload-input',
                                            'style' => 'display: none;'
                                        ])->label(false) ?>
                                    </div>
                                    <div id="image-preview-container" class="d-flex flex-wrap mt-2"></div>
                                </div>
                                
                                <div class="form-group text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane"></i> Publicar
                                    </button>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['site/index']) ?>" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            <?php \yii\widgets\ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos tipo foro consistentes con index.php */
.forum-container {
    background-color: #f9f9f9;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}

.forum-post {
    padding: 15px;
    background-color: #fff;
}

.card-header {
    padding: 1.5rem;
    color: #444;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.card-body {
    padding: 2rem;
}

.form-control {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    border: 1px solid #ced4da;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 2rem;
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

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.has-error .form-control {
    border-color: #dc3545;
}

.has-error .help-block {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-outline-secondary {
        margin-left: 0 !important;
    }
}

.character-counter {
    text-align: right;
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    padding: 0.25rem 0.5rem;
    background-color: #f8f9fa;
    border-radius: 4px;
    display: inline-block;
    float: right;
}

.character-counter.warning {
    color: #ffc107;
}

.character-counter.danger {
    color: #dc3545;
}

/* Avatar styles - Consistentes con index.php */
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    color: white;
    flex-shrink: 0;
}

.avatar.superadmin {
    background-color: #ffc107; /* Amarillo */
}

.avatar.admin {
    background-color: #fd7e14; /* Naranja */
}

.avatar.mod {
    background-color: #28a745; /* Verde */
}

.avatar.user-neutral {
    background-color: #6c757d; /* Gris */
}

.avatar.user-female {
    background-color: #007bff; /* Azul */
}

.avatar.user-male {
    background-color: #e83e8c; /* Rosado */
}

/* Estilos para la subida de imágenes */
.image-upload-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.image-preview-item {
    position: relative;
    margin: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}

.image-preview-item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.image-preview-remove {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    color: #dc3545;
}

.image-upload-disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Estilos para alertas de suscripción */
.alert {
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.alert i {
    font-size: 1.2rem;
}
</style>

<?php
$js = <<<JS
    // Contador de caracteres
    var textarea = $('#posts-contenido');
    var counter = $('.character-counter');
    var currentCount = $('.current-count');
    var maxCount = $('.max-count');
    var maxChars = 480; // Valor predeterminado
    var maxImages = 1; // Valor predeterminado (cambiado de 0 a 1)
    var uploadedImages = [];
    
    // Variables para el manejo de imágenes
    var imageUploadBtn = $('#image-upload-btn');
    var imageUploadInput = $('#image-upload-input');
    var imagePreviewContainer = $('#image-preview-container');
    var maxImagesSpan = $('#max-images');
    var subscriptionStatus = $('.subscription-status');
    
    // Función para verificar la suscripción y permisos del usuario
    function checkUserSubscription() {
        $.ajax({
            url: '$checkSubscriptionUrl',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostrar información de depuración en la consola
                    console.log('Respuesta completa de suscripción:', response);
                    if (response.debug) {
                        console.log('Información de depuración:', response.debug);
                    }
                    
                    maxChars = response.maxChars || 480;
                    maxImages = response.maxImages || 1;
                    
                    // Actualizar interfaz con los límites
                    maxCount.text(maxChars);
                    maxImagesSpan.text(maxImages);
                    
                    // Establecer el maxlength del textarea
                    textarea.attr('maxlength', maxChars);
                    
                    // Habilitar/deshabilitar botón de subida según permisos
                    if (maxImages <= 0) {
                        imageUploadBtn.addClass('image-upload-disabled').attr('disabled', true);
                    } else {
                        imageUploadBtn.removeClass('image-upload-disabled').attr('disabled', false);
                    }
                } else {
                    console.error('Error al verificar suscripción:', response.message);
                    if (response.debug) {
                        console.error('Detalles del error:', response.debug);
                    }
                    
                    // En caso de error, establecer valores predeterminados seguros
                    maxChars = 480;
                    maxImages = 1;
                    maxCount.text(maxChars);
                    maxImagesSpan.text(maxImages);
                    textarea.attr('maxlength', maxChars);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al conectar con el servidor:', status, error);
                console.error('Respuesta:', xhr.responseText);
                
                // En caso de error, establecer valores predeterminados seguros
                maxChars = 480;
                maxImages = 1;
                maxCount.text(maxChars);
                maxImagesSpan.text(maxImages);
                textarea.attr('maxlength', maxChars);
            }
        });
    }
    
    // Función para actualizar contador
    function updateCounter() {
        var length = textarea.val().length;
        currentCount.text(length);
        
        // Actualizar clases según la cantidad de caracteres
        counter.removeClass('warning danger');
        if (length >= maxChars * 0.9) {
            counter.addClass('warning');
        }
        if (length >= maxChars * 0.98) {
            counter.addClass('danger');
        }
    }
    
    // Función para manejar la selección de imágenes
    function handleImageSelection(e) {
        var files = e.target.files;
        
        if (files.length > 0) {
            // Verificar si ya se alcanzó el límite de imágenes
            var currentImages = imagePreviewContainer.find('.image-preview-item').length;
            var availableSlots = maxImages - currentImages;
            
            if (availableSlots <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite alcanzado',
                    text: 'Ya has alcanzado el límite de imágenes para tu suscripción'
                });
                return;
            }
            
            // Limitar el número de archivos a procesar según disponibilidad
            var filesToProcess = Math.min(files.length, availableSlots);
            
            for (var i = 0; i < filesToProcess; i++) {
                var file = files[i];
                
                // Verificar que sea una imagen
                if (!file.type.match('image.*')) {
                    continue;
                }
                
                // Leer y mostrar la imagen
                (function(file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgId = 'img-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
                        var imgHtml = 
                            '<div class="image-preview-item" id="' + imgId + '">' +
                                '<img src="' + e.target.result + '" alt="Preview">' +
                                '<div class="image-preview-remove" data-id="' + imgId + '">' +
                                    '<i class="fas fa-times"></i>' +
                                '</div>' +
                            '</div>';
                        imagePreviewContainer.append(imgHtml);
                    };
                    reader.readAsDataURL(file);
                })(file);
            }
            
            // Deshabilitar botón si se alcanza el límite
            if (currentImages + filesToProcess >= maxImages) {
                imageUploadBtn.addClass('image-upload-disabled').attr('disabled', true);
            }
        }
    }
    
    // Función para eliminar una imagen previamente seleccionada
    function removeImage() {
        var imgId = $(this).data('id');
        
        // Eliminar el elemento visual
        $('#' + imgId).remove();
        
        // Habilitar el botón si estaba deshabilitado
        var currentImages = imagePreviewContainer.find('.image-preview-item').length;
        if (currentImages < maxImages) {
            imageUploadBtn.removeClass('image-upload-disabled').attr('disabled', false);
        }
    }
    
    // Inicialización
    $(document).ready(function() {
        // Verificar suscripción al cargar la página
        checkUserSubscription();
        
        // Actualizar contador al cargar la página
        updateCounter();
        
        // Actualizar contador mientras se escribe
        textarea.on('input', updateCounter);
        
        // Configurar manejo de imágenes
        imageUploadBtn.on('click', function() {
            if (!$(this).hasClass('image-upload-disabled')) {
                imageUploadInput.click();
            }
        });
        
        imageUploadInput.on('change', handleImageSelection);
        
        // Delegación de eventos para botones de eliminar imagen
        $(document).on('click', '.image-preview-remove', removeImage);
    });
    
    // Manejar el envío del formulario
    $('#create-post-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        
        // Deshabilitar el botón y mostrar indicador de carga
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Publicando...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: new FormData(form[0]),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = response.redirectUrl;
                    });
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al crear el post'
                    });
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Publicar');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la solicitud'
                });
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Publicar');
            }
        });
        return false;
    });
JS;
$this->registerJs($js);
?> 