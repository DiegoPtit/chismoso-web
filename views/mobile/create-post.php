<?php
/* @var $this yii\web\View */
/* @var $modelPost app\models\Posts */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Registrar los assets de SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

$this->title = 'Crear nuevo chisme';

// Generar la URL para el endpoint check-subscription
$checkSubscriptionUrl = Yii::$app->urlManager->createUrl(['site/check-subscription']);

$this->registerCss("
    .create-post-container {
        max-width: 100%;
        margin: 0;
        padding: 1rem;
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
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
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
        width: 100%;
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
    .btn-back {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 10;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-decoration: none;
    }
    .character-count.warning {
        color: #ffc107;
    }
    .character-count.danger {
        color: #dc3545;
    }
    
    /* Estilos para la subida de imágenes */
    .image-upload-container {
        display: flex;
        align-items: center;
        margin-top: 1rem;
        gap: 10px;
    }
    
    .image-upload-btn {
        width: 100%;
        border-radius: 12px;
        padding: 0.8rem 1rem;
        background: #f8f9fa;
        border: 2px dashed #ced4da;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .image-upload-btn:hover {
        background: #e9ecef;
        border-color: #4a90e2;
        color: #4a90e2;
    }
    
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .image-preview-item {
        position: relative;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .image-preview-item img {
        width: 80px;
        height: 80px;
        object-fit: cover;
    }
    
    .image-preview-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 255, 255, 0.7);
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
    
    .image-limit-info {
        display: block;
        margin-top: 5px;
        color: #6c757d;
        font-size: 0.75rem;
    }
");
?>

<div class="create-post-container">
    <a href="<?= Yii::$app->urlManager->createUrl(['mobile/index']) ?>" class="btn-back">
        <i class="fas fa-arrow-left"></i>
    </a>
    
    <h1 class="form-title"><?= Html::encode($this->title) ?></h1>

    <div class="card post-form-card">
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin([
                'id' => 'create-post-form',
                'action' => ['/mobile/create-post'],
                'options' => [
                    'enctype' => 'multipart/form-data',
                    'class' => 'd-flex flex-column gap-4'
                ]
            ]); ?>

            <div class="row g-4">
                <div class="col-6">
                    <?= $form->field($modelPost, 'age', [
                        'inputOptions' => [
                            'type' => 'number',
                            'min' => 13,
                            'max' => 100,
                            'class' => 'form-control',
                            'placeholder' => 'Tu edad',
                        ],
                        'template' => '{input}',
                        'options' => ['class' => 'form-group']
                    ]) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($modelPost, 'genre')->dropDownList([
                        0 => 'Prefiero no decir',
                        1 => 'Hombre',
                        2 => 'Mujer'
                    ], [
                        'class' => 'form-select',
                        'prompt' => 'Selecciona tu género',
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
                        'id' => 'post-content',
                        'required' => true
                    ],
                    'template' => '{input}<div class="character-count"><span id="char-count">0</span>/<span id="max-chars">480</span> caracteres</div>',
                    'options' => ['class' => 'form-group']
                ])->textarea()->label(false) ?>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-image"></i> Imágenes</label>
                <div class="image-upload-container">
                    <button type="button" id="image-upload-btn" class="image-upload-btn">
                        <i class="fas fa-paperclip me-2"></i> Adjuntar imagen
                    </button>
                    <small class="image-limit-info">
                        Puedes adjuntar <span id="max-images">1</span> imágenes según tu suscripción
                    </small>
                </div>
                
                <?= $form->field($modelPost, 'imageFiles[]', [
                    'options' => ['style' => 'display:none'],
                ])->fileInput([
                    'multiple' => true,
                    'accept' => 'image/*',
                    'id' => 'image-upload-input',
                    'style' => 'display: none;'
                ])->label(false) ?>
                
                <div id="image-preview-container" class="image-preview-container"></div>
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
$js = <<<JS
    // Variables para el contador de caracteres
    var textarea = $('#post-content');
    var charCount = $('#char-count');
    var maxCharsSpan = $('#max-chars');
    var characterCount = $('.character-count');
    var maxChars = 480; // Valor predeterminado
    var maxImages = 1; // Valor predeterminado
    
    // Variables para el manejo de imágenes
    var imageUploadBtn = $('#image-upload-btn');
    var imageUploadInput = $('#image-upload-input');
    var imagePreviewContainer = $('#image-preview-container');
    var maxImagesSpan = $('#max-images');
    
    // Función para verificar la suscripción y permisos del usuario
    function checkUserSubscription() {
        console.log('Verificando suscripción...');
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
                    maxCharsSpan.text(maxChars);
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
                    maxCharsSpan.text(maxChars);
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
                maxCharsSpan.text(maxChars);
                maxImagesSpan.text(maxImages);
                textarea.attr('maxlength', maxChars);
            }
        });
    }
    
    // Función para actualizar contador
    function updateCounter() {
        var length = textarea.val().length;
        charCount.text(length);
        
        // Actualizar clases según la cantidad de caracteres
        characterCount.removeClass('warning danger');
        if (length >= maxChars * 0.9) {
            characterCount.addClass('warning');
        }
        if (length >= maxChars * 0.98) {
            characterCount.addClass('danger');
        }
    }
    
    // Función para manejar la selección de imágenes
    function handleImageSelection(e) {
        console.log('Archivos seleccionados:', e.target.files);
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
            console.log('Procesando', filesToProcess, 'archivos');
            
            for (var i = 0; i < filesToProcess; i++) {
                var file = files[i];
                
                // Verificar que sea una imagen
                if (!file.type.match('image.*')) {
                    console.log('Archivo no es imagen:', file.name);
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
                        console.log('Vista previa creada para:', file.name);
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
        console.log('Eliminando imagen:', imgId);
        
        // Eliminar el elemento visual
        $('#' + imgId).remove();
        
        // Habilitar el botón si estaba deshabilitado
        var currentImages = imagePreviewContainer.find('.image-preview-item').length;
        if (currentImages < maxImages) {
            imageUploadBtn.removeClass('image-upload-disabled').attr('disabled', false);
        }
        
        // Nota: no necesitamos manipular el input file directamente
        // La imagen se eliminará solo visualmente, pero el servidor
        // procesará solo las imágenes que se muestran en la vista previa
    }
    
    // Inicialización
    $(document).ready(function() {
        console.log('Inicializando formulario de creación de post');
        
        // Verificar suscripción al cargar la página
        checkUserSubscription();
        
        // Actualizar contador al cargar la página
        updateCounter();
        
        // Actualizar contador mientras se escribe
        textarea.on('input', updateCounter);
        
        // Configurar manejo de imágenes
        imageUploadBtn.on('click', function() {
            console.log('Botón de carga de imágenes clickeado');
            if (!$(this).hasClass('image-upload-disabled')) {
                // Limpiar el valor del input para que se dispare el evento change
                // incluso si el usuario selecciona el mismo archivo
                imageUploadInput.val('');
                imageUploadInput.click();
            }
        });
        
        imageUploadInput.on('change', handleImageSelection);
        
        // Delegación de eventos para botones de eliminar imagen
        $(document).on('click', '.image-preview-remove', removeImage);
        
        // Manejar el envío del formulario
        $('#create-post-form').on('submit', function(e) {
            e.preventDefault();
            console.log('Enviando formulario...');
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            
            // Verificar que el contenido no esté vacío
            if (!textarea.val().trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El contenido del post no puede estar vacío'
                });
                return false;
            }
            
            // Deshabilitar el botón y mostrar indicador de carga
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Publicando...');
            
            // Obtener el formulario y enviarlo mediante AJAX
            var formData = new FormData(form[0]);
            
            // Mostrar lo que se va a enviar para depuración
            console.log('Contenido del post:', textarea.val());
            console.log('Número de imágenes:', imagePreviewContainer.find('.image-preview-item').length);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
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
                        submitBtn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Publicar');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', status, error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la solicitud'
                    });
                    submitBtn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Publicar');
                }
            });
            
            return false;
        });
    });
JS;
$this->registerJs($js);
?> 