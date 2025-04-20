<?php
/**
 * Scripts JavaScript para la interacción de los posts y comentarios
 */
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar comentarios principales
    const posts = document.querySelectorAll('.forum-post');
    
    posts.forEach(post => {
        const commentsToggle = post.querySelector('.forum-comments-header');
        if (commentsToggle) {
            const commentsContainer = commentsToggle.nextElementSibling;
            const toggleText = commentsToggle.querySelector('span');
            const commentCount = commentsContainer.querySelectorAll('.forum-comment').length;
            
            commentsToggle.addEventListener('click', function() {
                if (commentsContainer.style.display === 'none') {
                    commentsContainer.style.display = 'block';
                    toggleText.textContent = 'Ocultar comentarios';
                } else {
                    commentsContainer.style.display = 'none';
                    toggleText.textContent = 'Ver ' + commentCount + ' comentarios';
                }
            });
        }
    });
    
    // Toggle para mostrar/ocultar subcomentarios
    document.addEventListener('click', function(e) {
        if (e.target.closest('.forum-comments-header')) {
            const header = e.target.closest('.forum-comments-header');
            if (header.id && header.id.startsWith('subcomments-toggle-')) {
                const commentId = header.id.replace('subcomments-toggle-', '');
                const container = document.getElementById('subcomments-container-' + commentId);
                const toggleText = header.querySelector('span');
                
                if (container) {
                    if (container.style.display === 'none') {
                        container.style.display = 'block';
                        toggleText.textContent = 'Ocultar respuestas';
                    } else {
                        container.style.display = 'none';
                        const commentCount = container.querySelectorAll('.forum-comment').length;
                        toggleText.textContent = 'Ver ' + commentCount + ' respuestas';
                    }
                }
            }
        }
    });
    
    // Funcionalidad para los botones de like y dislike
    const likeButtons = document.querySelectorAll('.like-button');
    const dislikeButtons = document.querySelectorAll('.dislike-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const isComment = this.closest('.forum-comment') !== null;
            const elementType = isComment ? '.forum-comment' : '.forum-post';
            const element = this.closest(elementType);
            
            // URL para la acción AJAX dependiendo si es comentario o post
            const likeUrl = isComment 
                ? '<?= Yii::$app->urlManager->createUrl(['site/like-comment', 'id' => '']) ?>' + postId
                : '<?= Yii::$app->urlManager->createUrl(['site/like', 'id' => '']) ?>' + postId;
            
            // Encontrar el contador de likes
            const likeCount = element.querySelector('.post-stats .stat-item:first-child') || 
                             document.createElement('span');
            
            // Cambio visual para confirmar la acción
            this.classList.add('active');
            
            // Realizar la petición AJAX
            fetch(likeUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar el contador visual
                    likeCount.innerHTML = '<i class="far fa-thumbs-up"></i> ' + data.count;
                } else {
                    // Mostrar mensaje de error
                    alert(data.message || 'Ocurrió un error al procesar tu solicitud');
                }
                
                // Quitar clase active después de un tiempo
                setTimeout(() => {
                    this.classList.remove('active');
                }, 500);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar tu solicitud');
                this.classList.remove('active');
            });
        });
    });
    
    dislikeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const isComment = this.closest('.forum-comment') !== null;
            const elementType = isComment ? '.forum-comment' : '.forum-post';
            const element = this.closest(elementType);
            
            // URL para la acción AJAX dependiendo si es comentario o post
            const dislikeUrl = isComment 
                ? '<?= Yii::$app->urlManager->createUrl(['site/dislike-comment', 'id' => '']) ?>' + postId
                : '<?= Yii::$app->urlManager->createUrl(['site/dislike', 'id' => '']) ?>' + postId;
            
            // Encontrar el contador de dislikes
            const dislikeCount = element.querySelector('.post-stats .stat-item:nth-child(2)') || 
                               document.createElement('span');
            
            // Cambio visual para confirmar la acción
            this.classList.add('active');
            
            // Realizar la petición AJAX
            fetch(dislikeUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar el contador visual
                    dislikeCount.innerHTML = '<i class="far fa-thumbs-down"></i> ' + data.count;
                } else {
                    // Mostrar mensaje de error
                    alert(data.message || 'Ocurrió un error al procesar tu solicitud');
                }
                
                // Quitar clase active después de un tiempo
                setTimeout(() => {
                    this.classList.remove('active');
                }, 500);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar tu solicitud');
                this.classList.remove('active');
            });
        });
    });
    
    // Mostrar formulario de comentarios para posts
    const commentButtons = document.querySelectorAll('.comment-button');
    
    commentButtons.forEach(button => {
        button.addEventListener('click', function() {
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
            }
        });
    });
    
    // Mostrar formulario de respuesta para comentarios
    document.addEventListener('click', function(e) {
        if (e.target.closest('.reply-button')) {
            const button = e.target.closest('.reply-button');
            const postId = button.getAttribute('data-post-id');
            const formContainer = document.getElementById('form-comment-' + postId);
            
            if (formContainer) {
                formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
                
                // Ocultar otros formularios abiertos
                document.querySelectorAll('.comment-form-container').forEach(container => {
                    if (container !== formContainer && container.style.display !== 'none') {
                        container.style.display = 'none';
                    }
                });
            }
        }
    });
    
    // Manejar botones de cancelar
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
    
    // Contador de caracteres para textareas
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'TEXTAREA') {
            const maxLength = e.target.getAttribute('maxlength');
            const currentLength = e.target.value.length;
            const countDisplay = e.target.nextElementSibling;
            
            if (countDisplay && countDisplay.classList.contains('character-count')) {
                countDisplay.textContent = currentLength + '/' + maxLength + ' caracteres';
                
                // Cambiar color cuando se acerca al límite
                if (currentLength > maxLength * 0.8) {
                    countDisplay.style.color = '#e74c3c';
                } else {
                    countDisplay.style.color = '#6c757d';
                }
            }
        }
    });
    
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

    // Funcionalidad del carrusel de imágenes
    function setupCarousels() {
        document.querySelectorAll('.post-carousel').forEach(carousel => {
            const items = carousel.querySelectorAll('.carousel-item');
            const dots = carousel.querySelectorAll('.carousel-dot');
            const prevBtn = carousel.querySelector('.carousel-control.prev');
            const nextBtn = carousel.querySelector('.carousel-control.next');
            
            if (items.length <= 1) return; // No necesitamos navegación si solo hay una imagen
            
            let currentIndex = 0;
            
            // Función para mostrar una imagen específica
            function showSlide(index) {
                // Normalizar el índice
                if (index < 0) index = items.length - 1;
                if (index >= items.length) index = 0;
                
                // Actualizar items
                items.forEach(item => item.classList.remove('active'));
                items[index].classList.add('active');
                
                // Actualizar dots
                dots.forEach(dot => dot.classList.remove('active'));
                dots[index].classList.add('active');
                
                currentIndex = index;
            }
            
            // Event listeners para los botones
            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    showSlide(currentIndex - 1);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    showSlide(currentIndex + 1);
                });
            }
            
            // Event listeners para los dots
            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    const index = parseInt(dot.getAttribute('data-index'));
                    showSlide(index);
                });
            });
            
            // Swipe para móviles
            let touchStartX = 0;
            let touchEndX = 0;
            
            carousel.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });
            
            carousel.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                
                if (touchStartX - touchEndX > 50) {
                    // Swipe izquierda -> Siguiente
                    showSlide(currentIndex + 1);
                } else if (touchEndX - touchStartX > 50) {
                    // Swipe derecha -> Anterior
                    showSlide(currentIndex - 1);
                }
            }, { passive: true });
        });
    }
    
    // Inicializar carruseles después de cargar la página
    setupCarousels();
    
    // Manejo de imágenes en posts
    $(document).on('click', '.carousel-image, .post-image', function() {
        var imgSrc = $(this).data('img-src') || $(this).attr('src');
        
        // Actualizar la imagen en el modal
        const fullImage = document.getElementById('fullImage');
        if (fullImage) {
            fullImage.setAttribute('src', imgSrc);
            fullImage.classList.remove('zoomed');
            
            // Actualizar el icono del botón de zoom
            const zoomBtn = document.getElementById('zoomToggleBtn');
            if (zoomBtn) {
                zoomBtn.innerHTML = '<i class="fas fa-search-plus"></i>';
            }
        }
        
        // Mostrar el modal
        $('#imageModal').modal('show');
    });
    
    // Funcionalidad de zoom para la imagen en el modal
    const setupImageModal = function() {
        const fullImage = document.getElementById('fullImage');
        const zoomToggleBtn = document.getElementById('zoomToggleBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const imageContainer = document.getElementById('imageContainer');
        
        if (fullImage && zoomToggleBtn) {
            // Función para alternar zoom
            function toggleZoom() {
                fullImage.classList.toggle('zoomed');
                
                // Cambiar el icono según el estado
                if (fullImage.classList.contains('zoomed')) {
                    zoomToggleBtn.innerHTML = '<i class="fas fa-search-minus"></i>';
                } else {
                    zoomToggleBtn.innerHTML = '<i class="fas fa-search-plus"></i>';
                    // Resetear la posición del contenedor
                    if (imageContainer) {
                        imageContainer.scrollTo(0, 0);
                    }
                }
            }
            
            // Evento para el botón de zoom
            zoomToggleBtn.addEventListener('click', toggleZoom);
            
            // Permitir hacer zoom con doble clic en la imagen
            fullImage.addEventListener('dblclick', toggleZoom);
            
            // Habilitar arrastre (pan) cuando la imagen está ampliada
            let isDragging = false;
            let startX = 0;
            let startY = 0;
            let startScrollLeft = 0;
            let startScrollTop = 0;
            
            if (imageContainer) {
                fullImage.addEventListener('mousedown', function(e) {
                    if (!fullImage.classList.contains('zoomed')) return;
                    
                    isDragging = true;
                    startX = e.pageX;
                    startY = e.pageY;
                    startScrollLeft = imageContainer.scrollLeft;
                    startScrollTop = imageContainer.scrollTop;
                    
                    fullImage.style.cursor = 'grabbing';
                    e.preventDefault();
                });
                
                document.addEventListener('mousemove', function(e) {
                    if (!isDragging) return;
                    
                    const x = e.pageX;
                    const y = e.pageY;
                    const walkX = (x - startX) * 1.5;
                    const walkY = (y - startY) * 1.5;
                    
                    imageContainer.scrollLeft = startScrollLeft - walkX;
                    imageContainer.scrollTop = startScrollTop - walkY;
                });
                
                document.addEventListener('mouseup', function() {
                    isDragging = false;
                    if (fullImage.classList.contains('zoomed')) {
                        fullImage.style.cursor = 'zoom-out';
                    }
                });
                
                // Soporte para dispositivos táctiles
                let touchStartX = 0;
                let touchStartY = 0;
                
                fullImage.addEventListener('touchstart', function(e) {
                    if (!fullImage.classList.contains('zoomed')) return;
                    
                    const touch = e.touches[0];
                    touchStartX = touch.clientX;
                    touchStartY = touch.clientY;
                    startScrollLeft = imageContainer.scrollLeft;
                    startScrollTop = imageContainer.scrollTop;
                }, { passive: true });
                
                fullImage.addEventListener('touchmove', function(e) {
                    if (!fullImage.classList.contains('zoomed')) return;
                    
                    const touch = e.touches[0];
                    const touchX = touch.clientX;
                    const touchY = touch.clientY;
                    
                    const walkX = (touchStartX - touchX) * 1.5;
                    const walkY = (touchStartY - touchY) * 1.5;
                    
                    imageContainer.scrollLeft = startScrollLeft + walkX;
                    imageContainer.scrollTop = startScrollTop + walkY;
                    
                    // Evitar scroll de página si la imagen está ampliada
                    if (fullImage.classList.contains('zoomed')) {
                        e.preventDefault();
                    }
                }, { passive: false });
            }
        }
        
        // Funcionalidad de descarga de imagen
        if (downloadBtn && fullImage) {
            downloadBtn.addEventListener('click', function() {
                const imgSrc = fullImage.getAttribute('src');
                if (!imgSrc) return;
                
                // Crear un enlace temporal para la descarga
                const link = document.createElement('a');
                link.href = imgSrc;
                
                // Extraer el nombre del archivo de la URL
                const fileName = imgSrc.split('/').pop();
                link.download = fileName || 'imagen.jpg';
                
                // Añadir al DOM, disparar clic y remover
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }
    };
    
    // Inicializar funcionalidad del modal de imagen
    setupImageModal();
    
    // Solucionar problema del backdrop
    $(document).on('hidden.bs.modal', '#imageModal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
        $('body').css({
            'overflow': '',
            'position': '',
            'height': ''
        });
        
        // Resetear el zoom si estaba activo
        const fullImage = document.getElementById('fullImage');
        if (fullImage && fullImage.classList.contains('zoomed')) {
            fullImage.classList.remove('zoomed');
        }
        
        // Resetear el icono de zoom
        const zoomBtn = document.getElementById('zoomToggleBtn');
        if (zoomBtn) {
            zoomBtn.innerHTML = '<i class="fas fa-search-plus"></i>';
        }
    });
});
</script> 