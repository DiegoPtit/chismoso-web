<?php
/**
 * Scripts específicos para la vista de comentarios
 * 
 * @var $checkSubscriptionUrl string URL para verificar suscripción
 * @var $csrfParam string Nombre del parámetro CSRF
 */
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let maxChars = 480; // Valor predeterminado
    
    // Función para verificar la suscripción y permisos del usuario
    function checkUserSubscription() {
        fetch('<?= $checkSubscriptionUrl ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Respuesta completa de suscripción:', data);
                if (data.debug) {
                    console.log('Información de depuración:', data.debug);
                }
                
                // Actualizar límites con los valores del servidor
                maxChars = data.maxChars || 480;
                
                // Actualizar maxlength de todos los textareas
                document.querySelectorAll('textarea[name="contenido"]').forEach(textarea => {
                    textarea.setAttribute('maxlength', maxChars);
                    
                    // Actualizar contador si existe
                    const counter = textarea.nextElementSibling;
                    if (counter && counter.classList.contains('character-count')) {
                        const currentLength = textarea.value.length;
                        counter.textContent = currentLength + '/' + maxChars + ' caracteres';
                    }
                });
            } else {
                console.error('Error al verificar suscripción:', data.message);
                if (data.debug) {
                    console.error('Detalles del error:', data.debug);
                }
            }
        })
        .catch(error => {
            console.error('Error al conectar con el servidor:', error);
        });
    }
    
    // Verificar suscripción al cargar la página
    checkUserSubscription();
    
    // Si hay un comentario para resaltar, desplazarse hacia él
    const highlightedComment = document.querySelector('.forum-comment.highlighted');
    if (highlightedComment) {
        // Esperar un poco para que la página se cargue completamente
        setTimeout(function() {
            highlightedComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Expandir los contenedores de comentarios padre si están colapsados
            const parentToggle = highlightedComment.closest('.comments-list')?.previousElementSibling;
            if (parentToggle && parentToggle.classList.contains('forum-comments-header')) {
                // Simular clic para expandir
                parentToggle.click();
            }
        }, 500);
    }
    
    // Funcionalidad para botones "Ver más" / "Ver menos"
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-ver-mas')) {
            const button = e.target;
            const action = button.getAttribute('data-action');
            const container = button.closest('.expandable');
            
            if (action === 'expand') {
                // Mostrar contenido completo
                container.querySelector('.content-preview').style.display = 'none';
                container.querySelector('.content-full').style.display = 'block';
                button.textContent = 'Ver menos';
                button.setAttribute('data-action', 'collapse');
            } else {
                // Colapsar contenido
                container.querySelector('.content-preview').style.display = 'block';
                container.querySelector('.content-full').style.display = 'none';
                button.textContent = 'Ver más';
                button.setAttribute('data-action', 'expand');
            }
        }
    });
    
    // Toggle para mostrar/ocultar el formulario de comentarios
    const commentButton = document.querySelector('.comment-button');
    if (commentButton) {
        commentButton.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const formContainer = document.getElementById('form-post-' + postId);
            
            if (formContainer) {
                formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
                
                // Ocultar otros formularios abiertos
                document.querySelectorAll('.comment-form-container').forEach(container => {
                    if (container !== formContainer && container.style.display !== 'none') {
                        container.style.display = 'none';
                    }
                });
                
                // Inicializar contador de caracteres cuando se muestra el formulario
                if (formContainer.style.display !== 'none') {
                    const textarea = formContainer.querySelector('textarea');
                    const counter = formContainer.querySelector('.character-count');
                    if (textarea && counter) {
                        updateCharacterCount(textarea, counter);
                    }
                }
            }
        });
    }
    
    // Manejo de botones de cancelar
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('cancel-comment')) {
            const formContainer = e.target.closest('.comment-form-container');
            if (formContainer) {
                formContainer.style.display = 'none';
                // Limpiar el formulario
                const form = formContainer.querySelector('form');
                if (form) form.reset();
            }
        }
    });
    
    // Función para actualizar el contador de caracteres
    function updateCharacterCount(textarea, counter) {
        const maxLength = textarea.getAttribute('maxlength') || maxChars;
        const currentLength = textarea.value.length;
        counter.textContent = currentLength + '/' + maxLength + ' caracteres';
        
        // Actualizar color según el número de caracteres
        counter.style.color = '#6c757d'; // Color por defecto
        if (currentLength >= maxLength * 0.9) {
            counter.style.color = '#ffc107'; // Amarillo (advertencia)
        }
        if (currentLength >= maxLength * 0.98) {
            counter.style.color = '#dc3545'; // Rojo (peligro)
        }
    }
    
    // Contador de caracteres para textareas
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'TEXTAREA') {
            const counter = e.target.nextElementSibling;
            if (counter && counter.classList.contains('character-count')) {
                updateCharacterCount(e.target, counter);
            }
        }
    });
    
    // Funcionalidad para los botones de like y dislike
    document.addEventListener('click', function(e) {
        if (e.target.closest('.like-button')) {
            const button = e.target.closest('.like-button');
            const postId = button.getAttribute('data-post-id') || button.getAttribute('data-comment-id');
            handleLikeDislike(postId, true, button);
        } else if (e.target.closest('.dislike-button')) {
            const button = e.target.closest('.dislike-button');
            const postId = button.getAttribute('data-post-id') || button.getAttribute('data-comment-id');
            handleLikeDislike(postId, false, button);
        }
    });
    
    function handleLikeDislike(id, isLike, button) {
        // Determinar si es un comentario o un post
        const isComment = button.hasAttribute('data-comment-id');
        const elementType = isComment ? '.forum-comment' : '.forum-post';
        const element = button.closest(elementType);
        
        // URL para la acción AJAX dependiendo si es comentario o post
        const url = isLike 
            ? (isComment ? '<?= Yii::$app->urlManager->createUrl(['site/like-comment']) ?>' : '<?= Yii::$app->urlManager->createUrl(['site/like']) ?>')
            : (isComment ? '<?= Yii::$app->urlManager->createUrl(['site/dislike-comment']) ?>' : '<?= Yii::$app->urlManager->createUrl(['site/dislike']) ?>');
        
        // Encontrar el contador correcto (likes o dislikes)
        const countIndex = isLike ? 0 : 1;
        const statsSelector = isComment ? '.comment-stats' : '.post-stats';
        const countElement = element.querySelector(statsSelector + ' .stat-item:nth-child(' + (countIndex + 1) + ')');
        
        // Cambio visual para confirmar la acción
        button.classList.add('active');
        
        // Crear FormData con los datos necesarios
        const formData = new FormData();
        formData.append('id', id);
        
        // Añadir token CSRF
        const csrfToken = document.querySelector('input[name="<?= $csrfParam ?>"]');
        if (csrfToken) {
            formData.append(csrfToken.name, csrfToken.value);
        }
        
        // Realizar la petición AJAX
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar el contador visual
                if (countElement) {
                    countElement.innerHTML = '<i class="far fa-thumbs-' + (isLike ? 'up' : 'down') + '"></i> ' + data.count;
                }
            } else {
                // Mostrar mensaje de error usando SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Ocurrió un error al procesar tu solicitud'
                });
            }
            
            // Quitar clase active después de un tiempo
            setTimeout(() => {
                button.classList.remove('active');
            }, 500);
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar tu solicitud'
            });
            button.classList.remove('active');
        });
    }
    
    // Botón para compartir comentario
    document.addEventListener('click', function(e) {
        if (e.target.closest('.share-button')) {
            const button = e.target.closest('.share-button');
            const commentId = button.getAttribute('data-comment-id');
            
            if (commentId) {
                // Crear la URL completa para compartir
                const baseUrl = window.location.origin + window.location.pathname;
                const shareUrl = baseUrl + '?commentId=' + commentId;
                
                // Copiar al portapapeles
                navigator.clipboard.writeText(shareUrl).then(function() {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Enlace copiado',
                        text: 'El enlace ha sido copiado al portapapeles',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }, function(err) {
                    console.error('Error al copiar:', err);
                    // Fallback: mostrar la URL para copiar manualmente
                    Swal.fire({
                        title: 'Copiar enlace',
                        html: 'Copia este enlace para compartir el comentario:<br><input value="' + shareUrl + '" class="form-control mt-2">',
                        icon: 'info'
                    });
                });
            }
        }
    });
    
    // Validación de formulario y envío AJAX
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('comment-form')) {
            e.preventDefault();
            
            const form = e.target;
            const textarea = form.querySelector('textarea[name="contenido"]');
            const contenido = textarea.value.trim();
            
            if (!contenido) {
                textarea.classList.add('is-invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'El contenido del comentario no puede estar vacío'
                });
                return;
            }
            
            // Verificar límite de caracteres según suscripción
            if (contenido.length > maxChars) {
                textarea.classList.add('is-invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite excedido',
                    text: 'El contenido no puede exceder los ' + maxChars + ' caracteres con tu suscripción actual.'
                });
                return;
            }
            
            // Deshabilitar botón de envío y mostrar indicador de carga
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            
            // Obtener el ID del padre
            const padreIdInput = form.querySelector('input[name="padre_id"]');
            if (!padreIdInput || !padreIdInput.value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Falta el ID del post o comentario padre'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                return;
            }
            
            // Crear FormData con los datos necesarios
            const formData = new FormData(form);
            
            // Enviar la solicitud mediante AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data.success) {
                    // Éxito - mostrar mensaje y actualizar UI
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Ocultar el formulario
                        const formContainer = form.closest('.comment-form-container');
                        formContainer.style.display = 'none';
                        
                        // Limpiar el formulario
                        form.reset();
                        
                        // Recargar la página para mostrar el nuevo comentario
                        window.location.reload();
                    });
                } else {
                    // Error - mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al procesar la solicitud'
                    });
                    
                    // Si hay redirección (por ejemplo, a login)
                    if (data.redirect) {
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al enviar el comentario. Por favor, inténtalo de nuevo.'
                });
            });
        }
    });
});
</script> 