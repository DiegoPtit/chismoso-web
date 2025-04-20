/**
 * Interacciones AJAX para botones de like y dislike
 */
document.addEventListener('DOMContentLoaded', function() {
    // Manejador global de errores de JavaScript
    window.onerror = function(message, source, lineno, colno, error) {
        console.error('Error global de JavaScript:', { message, source, lineno, colno, error });
        showError('Error en JavaScript: ' + message);
        return false;
    };

    // Función para mostrar un mensaje de éxito
    function showSuccess(message) {
        // Crear elemento toast
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1060';
        
        // HTML del toast
        toastContainer.innerHTML = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Agregar al documento
        document.body.appendChild(toastContainer);
        
        // Inicializar y mostrar toast
        const toastElement = new bootstrap.Toast(toastContainer.querySelector('.toast'), {
            autohide: true,
            delay: 3000
        });
        toastElement.show();
        
        // Eliminar del DOM al ocultarse
        toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toastContainer);
        });
    }
    
    // Función para mostrar un mensaje de error
    function showError(message) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1060';
        toastContainer.innerHTML = `
            <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toastContainer);
        const toastElement = new bootstrap.Toast(toastContainer.querySelector('.toast'), {
            autohide: true,
            delay: 4000
        });
        toastElement.show();
        
        // Eliminar el toast después de que se oculte
        toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toastContainer);
        });
    }
    
    // Función genérica para manejar las interacciones de like/dislike
    function handleInteraction(button, isLike) {
        // Verificar si el usuario está autenticado
        if (window.isGuest) {
            showError('Debes iniciar sesión para interactuar con los posts.');
            return;
        }
        
        const postId = button.getAttribute('data-post-id');
        const actionUrl = button.getAttribute('data-action') || 
                        (isLike ? 
                            (window.chismosoConfig ? window.chismosoConfig.likeUrl : '/site/like?id=') + postId : 
                            (window.chismosoConfig ? window.chismosoConfig.dislikeUrl : '/site/dislike?id=') + postId);
        
        const countClass = isLike ? 'likes-count' : 'dislikes-count';
        const countElement = button.querySelector('.' + countClass);
        
        // Guardar contenido original y mostrar carga
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Cargando...';
        
        // Realizar la solicitud AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', actionUrl, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        // Obtener token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
            document.querySelector('meta[name="csrf-token"]').getAttribute('content') : 
            (window.chismosoConfig ? window.chismosoConfig.csrfToken : '');
        
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        }
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.log('Respuesta del servidor:', response);
                    
                    if (response.success) {
                        // Obtener el valor del contador, con soporte para ambos formatos de respuesta
                        const newValue = isLike ? 
                                       (response.likes || response.count) : 
                                       (response.dislikes || response.count);
                        
                        if (countElement) {
                            countElement.textContent = newValue;
                        }
                        
                        // Restaurar el botón con el nuevo contador
                        const icon = isLike ? 'thumbs-up' : 'thumbs-down';
                        button.innerHTML = `<i class="fas fa-${icon} me-1"></i> <strong class="${countClass}">${newValue}</strong>`;
                        
                        // Mostrar mensaje de éxito
                        showSuccess(isLike ? '¡Like registrado con éxito!' : '¡Dislike registrado con éxito!');
                    } else {
                        button.innerHTML = originalContent;
                        showError(response.message || 'No se pudo procesar la solicitud.');
                    }
                } catch (e) {
                    console.error('Error al procesar respuesta:', e);
                    button.innerHTML = originalContent;
                    showError('Error al procesar la respuesta del servidor.');
                }
            } else {
                console.error('Error HTTP:', xhr.status);
                button.innerHTML = originalContent;
                showError('Error de conexión al servidor.');
            }
            button.disabled = false;
        };
        
        xhr.onerror = function() {
            console.error('Error de red');
            button.innerHTML = originalContent;
            showError('Error de conexión al servidor.');
            button.disabled = false;
        };
        
        xhr.send();
    }
    
    // Manejador para los botones de like
    document.addEventListener('click', function(e) {
        const likeButton = e.target.closest('.btn-like');
        if (likeButton) {
            e.preventDefault();
            handleInteraction(likeButton, true);
        }
    });
    
    // Manejador para los botones de dislike
    document.addEventListener('click', function(e) {
        const dislikeButton = e.target.closest('.btn-dislike');
        if (dislikeButton) {
            e.preventDefault();
            handleInteraction(dislikeButton, false);
        }
    });
    
    // Detectar si el usuario está autenticado (para usar en las validaciones)
    window.isGuest = document.body.classList.contains('guest') || 
                   (typeof Yii !== 'undefined' && Yii.app && Yii.app.user && Yii.app.user.isGuest);
    
    console.log('Estado de autenticación:', window.isGuest ? 'No autenticado' : 'Autenticado');
}); 