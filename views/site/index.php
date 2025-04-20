<?php
/** @var yii\web\View $this */

use app\models\Posts;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$this->title = 'El Chismoso - Inicio';

// Registrar los assets de SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

// Generar la URL para el endpoint check-subscription
$checkSubscriptionUrl = Yii::$app->urlManager->createUrl(['site/check-subscription']);

// Almacenar el nombre del parámetro CSRF para usarlo en JavaScript
$csrfParam = Yii::$app->request->csrfParam;

// Obtener todos los posts que son publicaciones principales (padre_id = null)
// ordenados por fecha de creación descendente
$posts = Posts::find()
    ->where(['padre_id' => null])
    ->orderBy(['created_at' => SORT_DESC])
    ->all();
?>

<div class="site-index">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> El Chismoso</h1>
        <p class="lead text-center mb-4">Comparte tus chismes favoritos de forma anónima</p>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <?php if (empty($posts)): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> No hay chismes publicados todavía. ¡Sé el primero en compartir!
                    </div>
                <?php else: ?>
                    <div class="forum-container">
                        <?php foreach ($posts as $post): ?>
                            <?= $this->render('_post', [
                                'post' => $post,
                            ]) ?>
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
            <div class="modal-body p-0 d-flex align-items-center justify-content-center bg-black">
                <div class="modal-image-container" id="imageContainer">
                    <img src="" class="modal-image" id="fullImage" alt="Imagen a tamaño completo">
                </div>
                <div class="modal-controls">
                    <button type="button" class="modal-control-btn" id="zoomToggleBtn">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" class="modal-control-btn" id="downloadBtn">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir estilos CSS
echo $this->render('_styles');

// Incluir scripts JS DOM
echo $this->render('_scripts');

// Registrar scripts de jQuery
$script = <<<JS
    // Variables globales para los límites
    let maxChars = 480; // Valor predeterminado
    let csrfParam = "{$csrfParam}"; // Nombre del parámetro CSRF

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
                    
                    // Actualizar límites con los valores del servidor
                    maxChars = response.maxChars || 480;
                    
                    // Actualizar maxlength de todos los textareas
                    $('textarea[name="contenido"]').attr('maxlength', maxChars);
                    
                    // Actualizar contadores existentes
                    $('textarea[name="contenido"]').each(function() {
                        var textarea = $(this);
                        var counter = textarea.next('.character-count');
                        if (counter.length) {
                            var currentLength = textarea.val().length;
                            counter.text(currentLength + '/' + maxChars + ' caracteres');
                        }
                    });
                } else {
                    console.error('Error al verificar suscripción:', response.message);
                    if (response.debug) {
                        console.error('Detalles del error:', response.debug);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al conectar con el servidor:', status, error);
                console.error('Respuesta:', xhr.responseText);
            }
        });
    }
    
    // Verificar suscripción al cargar la página
    $(document).ready(function() {
        checkUserSubscription();
    });

    // Contador de caracteres para textareas
    $(document).on('input', 'textarea', function() {
        var textarea = $(this);
        var counter = textarea.next('.character-count');
        if (counter.length) {
            var maxLength = textarea.attr('maxlength') || maxChars;
            var currentLength = textarea.val().length;
            
            counter.text(currentLength + '/' + maxLength + ' caracteres');
            
            // Actualizar color según el número de caracteres
            counter.css('color', '#6c757d'); // Color por defecto
            if (currentLength >= maxLength * 0.9) {
                counter.css('color', '#ffc107'); // Amarillo (advertencia)
            }
            if (currentLength >= maxLength * 0.98) {
                counter.css('color', '#dc3545'); // Rojo (peligro)
            }
        }
    });
    
    // Manejo de formularios de comentarios
    $(document).on('click', '.reply-button', function() {
        var postId = $(this).data('post-id');
        var formContainer = $('#form-comment-' + postId);
        
        if (formContainer.is(':visible')) {
            formContainer.slideUp();
        } else {
            // Ocultar otros formularios
            $('.comment-form-container').slideUp();
            formContainer.slideDown();
            
            // Inicializar contador de caracteres
            var textarea = formContainer.find('textarea');
            var counter = formContainer.find('.character-count');
            if (textarea.length && counter.length) {
                counter.text('0/' + (textarea.attr('maxlength') || 480) + ' caracteres');
            }
        }
    });
    
    // Cancelar comentario
    $(document).on('click', '.cancel-comment', function() {
        var form = $(this).closest('form');
        form[0].reset();
        form.closest('.comment-form-container').slideUp();
    });
    
    // Envío de formularios de comentarios
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var textarea = form.find('textarea[name="contenido"]');
        var contenido = textarea.val().trim();
        
        if (!contenido) {
            textarea.addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Campo requerido',
                text: 'El contenido del comentario no puede estar vacío'
            });
            return;
        }
        
        // Verificar límite de caracteres según suscripción
        if (contenido.length > maxChars) {
            textarea.addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Límite excedido',
                text: 'El contenido no puede exceder los ' + maxChars + ' caracteres con tu suscripción actual.'
            });
            return;
        }
        
        // Obtener el ID del padre
        var padreIdInput = form.find('input[name="padre_id"]');
        if (padreIdInput.length === 0 || !padreIdInput.val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Falta el ID del post o comentario padre'
            });
            return;
        }
        
        // Deshabilitar botón de envío y mostrar indicador de carga
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
        
        // Crear FormData manualmente con los campos exactos que necesitamos
        var formData = new FormData();
        
        // Añadir CSRF token
        var csrfToken = form.find('input[name="' + csrfParam + '"]');
        if (csrfToken.length > 0) {
            formData.append(csrfToken.attr('name'), csrfToken.val());
        }
        
        // Añadir campos principales
        formData.append('padre_id', padreIdInput.val());
        formData.append('contenido', contenido);
        
        // Añadir campos opcionales si existen
        var ageInput = form.find('input[name="age"]');
        if (ageInput.length > 0 && ageInput.val()) {
            formData.append('age', ageInput.val());
        }
        
        var genreSelect = form.find('select[name="genre"]');
        if (genreSelect.length > 0) {
            formData.append('genre', genreSelect.val());
        }
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Ocultar formulario y recargar
                        form[0].reset();
                        form.closest('.comment-form-container').slideUp();
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al procesar la solicitud'
                    });
                    
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    }
                }
            },
            error: function() {
                submitBtn.prop('disabled', false).html(originalText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al enviar el comentario. Por favor, inténtalo de nuevo.'
                });
            }
        });
    });

    // Cargar más posts al hacer scroll
    $(window).scroll(function() {
        var scrollHeight = $(document).height();
        var scrollPosition = $(window).height() + $(window).scrollTop();
        
        // Si estamos cerca del final, cargar más posts automáticamente
        if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
            $('#load-more').click();
        }
    });
    
    // Botón para cargar más posts
    $('#load-more').on('click', function() {
        var btn = $(this);
        var page = btn.data('page');
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...').prop('disabled', true);
        
        $.ajax({
            url: yii.getUrlManagerBaseUrl() + '/site/load-more-posts',
            type: 'GET',
            data: {page: page},
            success: function(response) {
                if (response.success) {
                    $('#posts-container').append(response.html);
                    
                    if (response.hasMore) {
                        btn.data('page', page + 1).html('<i class="fas fa-sync"></i> Cargar más').prop('disabled', false);
                    } else {
                        btn.remove();
                    }
                } else {
                    btn.html('<i class="fas fa-times"></i> ' + response.message).addClass('btn-outline-danger');
                    setTimeout(function() {
                        btn.html('<i class="fas fa-sync"></i> Reintentar').removeClass('btn-outline-danger').prop('disabled', false);
                    }, 3000);
                }
            },
            error: function() {
                btn.html('<i class="fas fa-times"></i> Error al cargar').addClass('btn-outline-danger');
                setTimeout(function() {
                    btn.html('<i class="fas fa-sync"></i> Reintentar').removeClass('btn-outline-danger').prop('disabled', false);
                }, 3000);
            }
        });
    });
    
    // Like y dislike buttons
    $(document).on('click', '.like-button', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        
        // Determinar si es un comentario o un post
        var isComment = btn.closest('.forum-comment').length > 0;
        var elementType = isComment ? '.forum-comment' : '.forum-post';
        var element = btn.closest(elementType);
        
        // URL para la acción AJAX
        var url = isComment 
            ? yii.getUrlManagerBaseUrl() + '/site/like-comment'
            : yii.getUrlManagerBaseUrl() + '/site/like';
        
        // Encontrar el contador
        var countElement = element.find('.post-stats .stat-item:first-child');
        
        // Cambio visual para confirmar la acción
        btn.addClass('active');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {id: postId},
            success: function(response) {
                if (response.success) {
                    // Actualizar el contador visual
                    if (countElement.length) {
                        countElement.html('<i class="far fa-thumbs-up"></i> ' + response.count);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al procesar la solicitud'
                    });
                }
                
                // Quitar clase active después de un tiempo
                setTimeout(function() {
                    btn.removeClass('active');
                }, 500);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar tu solicitud'
                });
                btn.removeClass('active');
            }
        });
    });
    
    $(document).on('click', '.dislike-button', function() {
        var btn = $(this);
        var postId = btn.data('post-id');
        
        // Determinar si es un comentario o un post
        var isComment = btn.closest('.forum-comment').length > 0;
        var elementType = isComment ? '.forum-comment' : '.forum-post';
        var element = btn.closest(elementType);
        
        // URL para la acción AJAX
        var url = isComment 
            ? yii.getUrlManagerBaseUrl() + '/site/dislike-comment'
            : yii.getUrlManagerBaseUrl() + '/site/dislike';
        
        // Encontrar el contador
        var countElement = element.find('.post-stats .stat-item:nth-child(2)');
        
        // Cambio visual para confirmar la acción
        btn.addClass('active');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {id: postId},
            success: function(response) {
                if (response.success) {
                    // Actualizar el contador visual
                    if (countElement.length) {
                        countElement.html('<i class="far fa-thumbs-down"></i> ' + response.count);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al procesar la solicitud'
                    });
                }
                
                // Quitar clase active después de un tiempo
                setTimeout(function() {
                    btn.removeClass('active');
                }, 500);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al procesar tu solicitud'
                });
                btn.removeClass('active');
            }
        });
    });
    
    // Manejo de imágenes en posts
    $(document).on('click', '.carousel-image', function() {
        var imgSrc = $(this).data('img-src') || $(this).attr('src');
        $('#imageModal').find('.full-image').attr('src', imgSrc);
        $('#imageModal').modal('show');
    });
    
    // Solucionar problema del backdrop
    $(document).on('hidden.bs.modal', '#imageModal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    });
JS;
$this->registerJs($script);
?>
