<?php
/**
 * Archivo de script para la funcionalidad de los posts
 */
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Definir las URLs base para las acciones
    const likeUrl = '<?= Yii::$app->urlManager->createUrl(['mobile/like', 'id' => '']) ?>';
    const dislikeUrl = '<?= Yii::$app->urlManager->createUrl(['mobile/dislike', 'id' => '']) ?>';
    const banPostUrl = '<?= Yii::$app->urlManager->createUrl(['mobile/ban-post', 'id' => '']) ?>';
    const banCommentUrl = '<?= Yii::$app->urlManager->createUrl(['mobile/ban-comment', 'id' => '']) ?>';
    const banUserUrl = '<?= Yii::$app->urlManager->createUrl(['mobile/ban-user', 'id' => '']) ?>';
    
    // Función para mostrar un mensaje de error
    function showError(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '1060';
        toast.innerHTML = `
            <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        const toastElement = new bootstrap.Toast(toast.querySelector('.toast'));
        toastElement.show();
        
        // Eliminar el toast después de que se oculte
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    }

    // Función para mostrar un mensaje de éxito
    function showSuccess(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '1060';
        toast.innerHTML = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        const toastElement = new bootstrap.Toast(toast.querySelector('.toast'));
        toastElement.show();
        
        // Eliminar el toast después de que se oculte
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    }

    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Funcionalidad del carrusel de imágenes
    function setupCarousels() {
        document.querySelectorAll('.post-carousel').forEach(carousel => {
            // Evitar reinicializar carruseles ya configurados
            if (carousel.dataset.initialized === 'true') return;
            
            const items = carousel.querySelectorAll('.carousel-item');
            const dots = carousel.querySelectorAll('.carousel-dot');
            const prevBtn = carousel.querySelector('.carousel-control.prev');
            const nextBtn = carousel.querySelector('.carousel-control.next');
            
            if (items.length <= 1) {
                // Para carruseles con una sola imagen, marcar como inicializado y salir
                carousel.dataset.initialized = 'true';
                
                // Asegurar que la única imagen esté visible
                if (items.length === 1) {
                    items[0].classList.add('active');
                    
                    // Asegurarse de que la imagen se carga correctamente
                    const img = items[0].querySelector('img');
                    if (img) {
                        // Si la imagen ya está cargada, aplicar estilos
                        if (img.complete) {
                            applyImageStyles(img);
                        } else {
                            // Si no está cargada, esperar a que se cargue
                            img.onload = function() {
                                applyImageStyles(img);
                            };
                        }
                    }
                }
                return;
            }
            
            // Marcar el carrusel como inicializado
            carousel.dataset.initialized = 'true';
            
            let currentIndex = 0;
            
            // Función para aplicar estilos según las dimensiones de la imagen
            function applyImageStyles(img) {
                if (img.naturalHeight && img.naturalWidth) {
                    const ratio = img.naturalHeight / img.naturalWidth;
                    img.setAttribute('data-ratio', ratio.toFixed(2));
                    
                    // Notificar al navegador que queremos realizar cambios visuales
                    requestAnimationFrame(() => {
                        if (ratio > 1.2) { // Imagen vertical
                            img.style.height = '100%';
                            img.style.width = 'auto';
                        } else if (ratio < 0.8) { // Imagen horizontal
                            img.style.width = '100%';
                            img.style.height = 'auto';
                        } else { // Imagen casi cuadrada
                            img.style.maxHeight = '100%';
                            img.style.maxWidth = '100%';
                        }
                    });
                }
            }
            
            // Función para mostrar una imagen específica con mejor gestión de carga
            function showSlide(index) {
                // Normalizar el índice
                if (index < 0) index = items.length - 1;
                if (index >= items.length) index = 0;
                
                // Ocultar todas las imágenes y mostrar la actual
                requestAnimationFrame(() => {
                    items.forEach(item => item.classList.remove('active'));
                    items[index].classList.add('active');
                    
                    // Actualizar dots
                    dots.forEach(dot => dot.classList.remove('active'));
                    dots[index].classList.add('active');
                    
                    // Asegurarse de que la imagen actual se cargue correctamente
                    const currentImg = items[index].querySelector('img');
                    if (currentImg) {
                        if (currentImg.complete) {
                            applyImageStyles(currentImg);
                        } else {
                            currentImg.onload = function() {
                                applyImageStyles(currentImg);
                            };
                        }
                    }
                });
                
                currentIndex = index;
            }
            
            // Event listeners para los botones
            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    showSlide(currentIndex - 1);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    showSlide(currentIndex + 1);
                });
            }
            
            // Event listeners para los dots
            dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const index = parseInt(dot.getAttribute('data-index'));
                    showSlide(index);
                });
            });
            
            // Pre-cargar imágenes para mejorar la experiencia
            items.forEach((item, idx) => {
                const img = item.querySelector('img');
                if (img) {
                    if (img.complete) {
                        applyImageStyles(img);
                    } else {
                        img.onload = function() {
                            applyImageStyles(img);
                        };
                    }
                }
            });
            
            // Swipe para móviles con mejor manejo de eventos
            let touchStartX = 0;
            let touchEndX = 0;
            let touchHandled = false;
            
            carousel.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
                touchHandled = false;
            }, { passive: true });
            
            carousel.addEventListener('touchmove', e => {
                // Prevenir scroll de página durante swipe horizontal
                if (Math.abs(e.changedTouches[0].screenX - touchStartX) > 10) {
                    e.stopPropagation();
                }
            }, { passive: true });
            
            carousel.addEventListener('touchend', e => {
                if (touchHandled) return;
                
                touchEndX = e.changedTouches[0].screenX;
                const swipeDistance = Math.abs(touchEndX - touchStartX);
                
                // Solo procesar swipes claros (más de 50px)
                if (swipeDistance > 50) {
                    touchHandled = true;
                    if (touchStartX - touchEndX > 0) {
                        // Swipe izquierda -> Siguiente
                        showSlide(currentIndex + 1);
                    } else {
                        // Swipe derecha -> Anterior
                        showSlide(currentIndex - 1);
                    }
                }
            }, { passive: true });
            
            // Iniciar con la primera imagen
            showSlide(0);
        });
    }
    
    // Inicializar carruseles después de cargar la página
    setupCarousels();
    
    // Re-inicializar carruseles cuando hay cambios en el DOM (para scroll infinito)
    // Usar MutationObserver para detectar cambios en el DOM
    const postsContainer = document.getElementById('posts-container');
    if (postsContainer) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Ejecutar con un pequeño retraso para asegurar que el DOM esté completo
                    setTimeout(function() {
                        setupCarousels();
                    }, 200);
                }
            });
        });
        
        observer.observe(postsContainer, { childList: true, subtree: true });
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
    
    // Manejo de imágenes en el modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('carousel-image') || e.target.classList.contains('post-image')) {
            const imgSrc = e.target.getAttribute('data-img-src') || e.target.getAttribute('src');
            const fullImage = document.getElementById('fullImage');
            
            if (fullImage) {
                fullImage.setAttribute('src', imgSrc);
                fullImage.classList.remove('zoomed');
                
                // Actualizar el icono del botón de zoom
                const zoomBtn = document.getElementById('zoomToggleBtn');
                if (zoomBtn) {
                    zoomBtn.innerHTML = '<i class="fas fa-search-plus"></i>';
                }
                
                const modal = new bootstrap.Modal(document.getElementById('imageModal'));
                modal.show();
            }
        }
    });
    
    // Funcionalidad de zoom para la imagen en el modal
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
    
    // Solucionar problema del backdrop
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('hidden.bs.modal', function () {
            // Eliminar todos los backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            
            // Restaurar el scroll
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.height = '';
            
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
    }

    // Manejador para los formularios de like
    document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (<?= Yii::$app->user->isGuest ? 'true' : 'false' ?>) {
                showError('Debes iniciar sesión para interactuar con los posts.');
                return;
            }
            
            const submitButton = this.querySelector('button[type="submit"]');
            const likesCountElement = submitButton.querySelector('.likes-count');
            const postId = this.action.split('/').pop();
            
            // Deshabilitar el botón y mostrar carga
            submitButton.disabled = true;
            const originalContent = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Cargando...';
            
            // Realizar la solicitud AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', likeUrl + postId, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-CSRF-Token', '<?= Yii::$app->request->getCsrfToken() ?>');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            likesCountElement.textContent = response.likes;
                            submitButton.innerHTML = `<i class="fas fa-thumbs-up me-1"></i> <strong class="likes-count">${response.likes}</strong>`;
                            showSuccess('¡Like registrado con éxito!');
                        } else {
                            submitButton.innerHTML = originalContent;
                            showError(response.message || 'No se pudo procesar el like.');
                        }
                    } catch (e) {
                        submitButton.innerHTML = originalContent;
                        showError('Error al procesar la respuesta del servidor.');
                    }
                } else {
                    submitButton.innerHTML = originalContent;
                    showError('Error de conexión al servidor.');
                }
                submitButton.disabled = false;
            };
            
            xhr.onerror = function() {
                submitButton.innerHTML = originalContent;
                showError('Error de conexión al servidor.');
                submitButton.disabled = false;
            };
            
            xhr.send();
        });
    });

    // Manejador para los formularios de dislike
    document.querySelectorAll('.dislike-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (<?= Yii::$app->user->isGuest ? 'true' : 'false' ?>) {
                showError('Debes iniciar sesión para interactuar con los posts.');
                return;
            }
            
            const submitButton = this.querySelector('button[type="submit"]');
            const dislikesCountElement = submitButton.querySelector('.dislikes-count');
            const postId = this.action.split('/').pop();
            
            // Deshabilitar el botón y mostrar carga
            submitButton.disabled = true;
            const originalContent = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Cargando...';
            
            // Realizar la solicitud AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', dislikeUrl + postId, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-CSRF-Token', '<?= Yii::$app->request->getCsrfToken() ?>');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            dislikesCountElement.textContent = response.dislikes;
                            submitButton.innerHTML = `<i class="fas fa-thumbs-down me-1"></i> <strong class="dislikes-count">${response.dislikes}</strong>`;
                            showSuccess('¡Dislike registrado con éxito!');
                        } else {
                            submitButton.innerHTML = originalContent;
                            showError(response.message || 'No se pudo procesar el dislike.');
                        }
                    } catch (e) {
                        submitButton.innerHTML = originalContent;
                        showError('Error al procesar la respuesta del servidor.');
                    }
                } else {
                    submitButton.innerHTML = originalContent;
                    showError('Error de conexión al servidor.');
                }
                submitButton.disabled = false;
            };
            
            xhr.onerror = function() {
                submitButton.innerHTML = originalContent;
                showError('Error de conexión al servidor.');
                submitButton.disabled = false;
            };
            
            xhr.send();
        });
    });

    // Manejador para los botones de mostrar/ocultar comentarios
    document.querySelectorAll('.toggle-comments').forEach(button => {
        button.addEventListener('click', function() {
            const showText = this.getAttribute('data-show-text');
            const hideText = this.getAttribute('data-hide-text');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Cambia el texto del botón según el estado
            if (isExpanded) {
                this.innerHTML = showText;
            } else {
                this.innerHTML = hideText;
            }
        });
        
        // Agregar listener al evento de Bootstrap para manejar cambios externos
        const targetId = button.getAttribute('data-bs-target').substring(1);
        const collapseElement = document.getElementById(targetId);
        
        if (collapseElement) {
            collapseElement.addEventListener('hidden.bs.collapse', function() {
                const btn = document.querySelector(`[data-bs-target="#${targetId}"]`);
                if (btn) {
                    btn.innerHTML = btn.getAttribute('data-show-text');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
            
            collapseElement.addEventListener('shown.bs.collapse', function() {
                const btn = document.querySelector(`[data-bs-target="#${targetId}"]`);
                if (btn) {
                    btn.innerHTML = btn.getAttribute('data-hide-text');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });

    // Manejador para los botones de banear posts
    document.querySelectorAll('.ban-post-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Mostrar un modal de confirmación con opciones de motivo de baneo
            const postId = this.getAttribute('data-post-id');
            const originalText = this.innerHTML;
            
            // Crear un modal para seleccionar el motivo
            const modalHtml = `
                <div class="modal fade" id="banReasonModal" tabindex="-1" aria-labelledby="banReasonModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="banReasonModalLabel">Banear Post</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Seleccione el motivo del baneo:</p>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason1" value="HATE_LANG">
                                    <label class="form-check-label" for="reason1">Lenguaje que incita al odio</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason2" value="KIDS_HASSARAMENT">
                                    <label class="form-check-label" for="reason2">Pedofilia</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason3" value="SENSIBLE_CONTENT">
                                    <label class="form-check-label" for="reason3">Contenido inapropiado (Incluso para un mayor de edad)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason4" value="SCAM">
                                    <label class="form-check-label" for="reason4">Estafa</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason5" value="SPAM">
                                    <label class="form-check-label" for="reason5">Spam</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason6" value="RACIST_LANG">
                                    <label class="form-check-label" for="reason6">Racismo o Xenofobia</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason7" value="MODERATED" checked>
                                    <label class="form-check-label" for="reason7">Moderado (default)</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" id="confirmBanBtn">Confirmar Baneo</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar el modal al DOM
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHtml;
            document.body.appendChild(modalContainer);
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('banReasonModal'));
            modal.show();
            
            // Manejar el evento de confirmación de baneo
            document.getElementById('confirmBanBtn').addEventListener('click', () => {
                // Obtener el motivo seleccionado
                const motivo = document.querySelector('input[name="banReason"]:checked').value;
                
                // Ocultar el modal
                modal.hide();
                
                // Cambiar apariencia para indicar carga
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
                this.style.pointerEvents = 'none';
                
                // Realizar solicitud AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', banPostUrl + postId, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-Token', '<?= Yii::$app->request->getCsrfToken() ?>');
                
                xhr.onload = () => {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                showSuccess('Post baneado con éxito.');
                                
                                // Ocultar la tarjeta del post o aplicar algún estilo visual
                                const postCard = this.closest('.card-material');
                                if (postCard) {
                                    // Recargar la página para mostrar el contenido baneado
                                    window.location.reload();
                                }
                            } else {
                                showError(response.message || 'No se pudo banear el post.');
                                this.innerHTML = originalText;
                                this.style.pointerEvents = 'auto';
                            }
                        } catch (e) {
                            showError('Error al procesar la respuesta del servidor.');
                            this.innerHTML = originalText;
                            this.style.pointerEvents = 'auto';
                        }
                    } else {
                        showError('Error de conexión al servidor.');
                        this.innerHTML = originalText;
                        this.style.pointerEvents = 'auto';
                    }
                    
                    // Eliminar el modal del DOM una vez completada la acción
                    setTimeout(() => {
                        document.body.removeChild(modalContainer);
                    }, 500);
                };
                
                xhr.onerror = () => {
                    showError('Error de conexión al servidor.');
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                    
                    // Eliminar el modal del DOM
                    document.body.removeChild(modalContainer);
                };
                
                // Enviar datos incluyendo el motivo
                xhr.send('motivo=' + motivo);
            });
            
            // Limpiar el DOM si se cierra el modal
            document.getElementById('banReasonModal').addEventListener('hidden.bs.modal', function () {
                document.body.removeChild(modalContainer);
            });
        });
    });
    
    // Manejador para los botones de banear comentarios
    document.querySelectorAll('.ban-comment-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Mostrar un modal de confirmación con opciones de motivo de baneo
            const commentId = this.getAttribute('data-comment-id');
            const originalText = this.innerHTML;
            
            // Crear un modal para seleccionar el motivo
            const modalHtml = `
                <div class="modal fade" id="banReasonModal" tabindex="-1" aria-labelledby="banReasonModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="banReasonModalLabel">Banear Comentario</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Seleccione el motivo del baneo:</p>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason1" value="HATE_LANG">
                                    <label class="form-check-label" for="reason1">Lenguaje que incita al odio</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason2" value="KIDS_HASSARAMENT">
                                    <label class="form-check-label" for="reason2">Pedofilia</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason3" value="SENSIBLE_CONTENT">
                                    <label class="form-check-label" for="reason3">Contenido inapropiado (Incluso para un mayor de edad)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason4" value="SCAM">
                                    <label class="form-check-label" for="reason4">Estafa</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason5" value="SPAM">
                                    <label class="form-check-label" for="reason5">Spam</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason6" value="RACIST_LANG">
                                    <label class="form-check-label" for="reason6">Racismo o Xenofobia</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="banReason" id="reason7" value="MODERATED" checked>
                                    <label class="form-check-label" for="reason7">Moderado (default)</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" id="confirmBanBtn">Confirmar Baneo</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar el modal al DOM
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHtml;
            document.body.appendChild(modalContainer);
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('banReasonModal'));
            modal.show();
            
            // Manejar el evento de confirmación de baneo
            document.getElementById('confirmBanBtn').addEventListener('click', () => {
                // Obtener el motivo seleccionado
                const motivo = document.querySelector('input[name="banReason"]:checked').value;
                
                // Ocultar el modal
                modal.hide();
                
                // Cambiar apariencia para indicar carga
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
                this.style.pointerEvents = 'none';
                
                // Realizar solicitud AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', banCommentUrl + commentId, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-Token', '<?= Yii::$app->request->getCsrfToken() ?>');
                
                xhr.onload = () => {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                showSuccess('Comentario baneado con éxito.');
                                
                                // Recargar la página para mostrar el contenido baneado
                                window.location.reload();
                            } else {
                                showError(response.message || 'No se pudo banear el comentario.');
                                this.innerHTML = originalText;
                                this.style.pointerEvents = 'auto';
                            }
                        } catch (e) {
                            showError('Error al procesar la respuesta del servidor.');
                            this.innerHTML = originalText;
                            this.style.pointerEvents = 'auto';
                        }
                    } else {
                        showError('Error de conexión al servidor.');
                        this.innerHTML = originalText;
                        this.style.pointerEvents = 'auto';
                    }
                    
                    // Eliminar el modal del DOM una vez completada la acción
                    setTimeout(() => {
                        document.body.removeChild(modalContainer);
                    }, 500);
                };
                
                xhr.onerror = () => {
                    showError('Error de conexión al servidor.');
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                    
                    // Eliminar el modal del DOM
                    document.body.removeChild(modalContainer);
                };
                
                // Enviar datos incluyendo el motivo
                xhr.send('motivo=' + motivo);
            });
            
            // Limpiar el DOM si se cierra el modal
            document.getElementById('banReasonModal').addEventListener('hidden.bs.modal', function () {
                document.body.removeChild(modalContainer);
            });
        });
    });
    
    // Manejador para los botones de banear usuarios
    document.querySelectorAll('.ban-user-link').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            if (!confirm('¿Estás seguro de que deseas banear a este usuario? Esta acción no se puede deshacer.')) {
                return;
            }
            
            const userId = this.getAttribute('data-user-id');
            const originalText = this.innerHTML;
            
            // Cambiar apariencia para indicar carga
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
            this.style.pointerEvents = 'none';
            
            // Realizar solicitud AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', banUserUrl + userId, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-CSRF-Token', '<?= Yii::$app->request->getCsrfToken() ?>');
            
            xhr.onload = () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            showSuccess('Usuario baneado con éxito.');
                            
                            // Aplicar cambios visuales a todas las tarjetas del usuario
                            const userId = this.getAttribute('data-user-id');
                            document.querySelectorAll(`[data-user-id="${userId}"]`).forEach(element => {
                                const card = element.closest('.card-material');
                                if (card) {
                                    card.style.opacity = '0.5';
                                    card.style.filter = 'grayscale(100%)';
                                    card.querySelector('.card-body').innerHTML += '<div class="alert alert-danger mt-2 mb-0 p-2 text-center">Este usuario ha sido baneado.</div>';
                                }
                            });
                        } else {
                            showError(response.message || 'No se pudo banear al usuario.');
                            this.innerHTML = originalText;
                            this.style.pointerEvents = 'auto';
                        }
                    } catch (e) {
                        showError('Error al procesar la respuesta del servidor.');
                        this.innerHTML = originalText;
                        this.style.pointerEvents = 'auto';
                    }
                } else {
                    showError('Error de conexión al servidor.');
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                }
            };
            
            xhr.onerror = () => {
                showError('Error de conexión al servidor.');
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            };
            
            xhr.send();
        });
    });
});
</script> 