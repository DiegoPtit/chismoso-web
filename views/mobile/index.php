<?php
// Archivo index.php para la vista mobile
use yii\helpers\Html;

$this->title = 'Chismoso App';

// Incluir los estilos CSS para las tarjetas
require '_partials/_styles.php';
?>

<div class="container-fluid p-0 my-3">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center fw-bold my-4">Chismes</h1>
            
            <div id="posts-container">
                <?php if (empty($posts)): ?>
                    <div class="modal fade" id="noPostsModal" tabindex="-1" aria-labelledby="noPostsModalLabel" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="noPostsModalLabel">
                                        <i class="fas fa-info-circle me-2"></i>Información
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    No hay posts disponibles.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center p-4">
                        No hay posts disponibles.
                    </div>
                    <?php 
                    $this->registerJs("
                        document.addEventListener('DOMContentLoaded', function() {
                            var noPostsModal = new bootstrap.Modal(document.getElementById('noPostsModal'));
                            noPostsModal.show();
                        });
                    ");
                    ?>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <?php require '_partials/_post_card.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Spinner de carga -->
            <div id="loading-spinner" class="text-center d-none my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando más posts...</p>
            </div>
        </div>
    </div>
</div>

<!-- Botón flotante para crear post -->
<a href="<?= Yii::$app->urlManager->createUrl(['mobile/create-post']) ?>" class="floating-action-button">
    <i class="fas fa-plus"></i>
</a>

<style>
    .floating-action-button {
        position: fixed;
        bottom: 80px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        z-index: 1040;
        text-decoration: none;
    }
    
    .floating-action-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        color: white;
        text-decoration: none;
    }
</style>

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
// Incluir el script para la funcionalidad de los posts
require '_partials/_posts_script.php';
?>

<!-- Script para el scroll infinito -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        let isLoading = false;
        let hasMorePosts = true;
        let scrollDebounceTimer = null;
        
        console.log("Script de scroll infinito cargado"); // Depuración
        
        // Función para cargar más posts con mejor manejo de errores y renderizado
        function loadMorePosts() {
            if (isLoading || !hasMorePosts) return;
            
            isLoading = true;
            const loadingSpinner = document.getElementById('loading-spinner');
            if (loadingSpinner) loadingSpinner.classList.remove('d-none');
            
            console.log("Cargando página:", currentPage + 1); // Depuración
            
            // Construir la URL correctamente
            const baseUrl = "<?= Yii::$app->urlManager->createUrl(['mobile/load-more-posts']) ?>";
            const url = baseUrl + "?page=" + (currentPage + 1);
            
            // Crear un objeto AbortController para cancelar la solicitud si es necesario
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000); // Timeout de 15 segundos
            
            // Usar fetch con mejor manejo de errores
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache' // Evitar caché
                },
                signal: controller.signal
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error de red: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    // Procesar la respuesta
                    const response = JSON.parse(text);
                    
                    if (response.success) {
                        if (response.posts && response.posts.length > 0) {
                            // Crear un contenedor temporal para procesar el HTML
                            const tempContainer = document.createElement('div');
                            tempContainer.innerHTML = response.posts;
                            
                            // Añadir los nuevos posts al contenedor
                            const postsContainer = document.getElementById('posts-container');
                            
                            // Usar DocumentFragment para mejor rendimiento
                            const fragment = document.createDocumentFragment();
                            tempContainer.childNodes.forEach(node => {
                                if (node.nodeType === 1) { // Solo elementos Node.ELEMENT_NODE
                                    fragment.appendChild(node);
                                }
                            });
                            
                            // Insertar todos los nodos de una vez
                            postsContainer.appendChild(fragment);
                            
                            currentPage++;
                            hasMorePosts = response.hasMore;
                            
                            console.log("Posts añadidos, página actual:", currentPage); // Depuración
                            console.log("¿Hay más posts?:", hasMorePosts); // Depuración
                        } else {
                            hasMorePosts = false;
                            console.log("No hay más posts disponibles"); // Depuración
                        }
                    } else {
                        console.error('Error al cargar más posts:', response.message);
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta del servidor:', e);
                    hasMorePosts = false; // Detener intentos futuros en caso de error
                }
            })
            .catch(error => {
                console.error('Error de conexión al servidor:', error);
                if (error.name === 'AbortError') {
                    console.warn('La solicitud se canceló por timeout');
                }
                // No marcar hasMorePosts como false aquí para permitir reintentos
            })
            .finally(() => {
                clearTimeout(timeoutId);
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                isLoading = false;
            });
        }
        
        // Función debounce para mejorar el rendimiento del scroll
        function debounce(func, delay) {
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(scrollDebounceTimer);
                scrollDebounceTimer = setTimeout(() => func.apply(context, args), delay);
            };
        }
        
        // Usar IntersectionObserver en lugar de eventos de scroll para mejor rendimiento
        if ('IntersectionObserver' in window) {
            const loadingSpinner = document.getElementById('loading-spinner');
            
            const loadMoreObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !isLoading && hasMorePosts) {
                        console.log("Observador detectó que el spinner está visible");
                        loadMorePosts();
                    }
                });
            }, { rootMargin: '200px 0px' });
            
            if (loadingSpinner) {
                loadingSpinner.classList.remove('d-none');
                loadMoreObserver.observe(loadingSpinner);
            }
        } else {
            // Fallback para navegadores que no soportan IntersectionObserver
            const handleScroll = debounce(function() {
                if (isLoading) return;
                
                const scrollPosition = window.innerHeight + window.scrollY;
                const documentHeight = document.body.offsetHeight;
                const threshold = documentHeight - 800;
                
                if (scrollPosition >= threshold && hasMorePosts) {
                    console.log("Llegamos al umbral de carga"); // Depuración
                    loadMorePosts();
                }
            }, 150); // Debounce de 150ms
            
            window.addEventListener('scroll', handleScroll, { passive: true });
        }
    });
</script>