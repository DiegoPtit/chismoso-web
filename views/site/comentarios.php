<?php
/** @var yii\web\View $this */
/** @var app\models\Posts $post */
/** @var app\models\Posts[] $comentarios */
/** @var integer $commentId */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;

// Registrar los assets de SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

// Generar la URL para el endpoint check-subscription
$checkSubscriptionUrl = Yii::$app->urlManager->createUrl(['site/check-subscription']);

// Almacenar el nombre del parámetro CSRF para usarlo en JavaScript
$csrfParam = Yii::$app->request->csrfParam;

$this->title = 'Comentarios - ' . mb_substr(strip_tags($post->contenido), 0, 50) . '...';
?>

<div class="site-comentarios">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-comments"></i> El Chismoso</h1>
        <p class="lead text-center mb-4">Detalles del chisme y comentarios</p>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="forum-container">
                    <!-- Post original -->
                    <?= $this->render('_post_detalle', [
                        'post' => $post,
                    ]) ?>
                    
                    <!-- Comentarios -->
                    <div class="section-title">
                        <h3>Comentarios (<?= count($comentarios) ?>)</h3>
                    </div>
                    
                    <?php if (empty($comentarios)): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No hay comentarios todavía. ¡Sé el primero en comentar!
                        </div>
                    <?php else: ?>
                        <div class="forum-comments comments-container">
                            <?= $this->render('_comments_detalle', [
                                'comentarios' => $comentarios,
                                'nivel' => 0,
                                'commentId' => $commentId,
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>
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
echo $this->render('_styles_comentarios');

// Incluir scripts JS
echo $this->render('_scripts_comentarios', [
    'checkSubscriptionUrl' => $checkSubscriptionUrl,
    'csrfParam' => $csrfParam,
]);

// Registrar script para manejar las imágenes
$this->registerJs("
    // Manejo de imágenes en posts y comentarios
    $(document).on('click', '.post-image, .carousel-image', function() {
        var imgSrc = $(this).data('img-src') || $(this).attr('src');
        
        // Actualizar la imagen en el modal
        const fullImage = document.getElementById('fullImage');
        if (fullImage) {
            fullImage.setAttribute('src', imgSrc);
            fullImage.classList.remove('zoomed');
            
            // Actualizar el icono del botón de zoom
            const zoomBtn = document.getElementById('zoomToggleBtn');
            if (zoomBtn) {
                zoomBtn.innerHTML = '<i class=\"fas fa-search-plus\"></i>';
            }
        }
        
        // Mostrar el modal
        $('#imageModal').modal('show');
    });
    
    // Funcionalidad de zoom para la imagen en el modal
    (function() {
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
                    zoomToggleBtn.innerHTML = '<i class=\"fas fa-search-minus\"></i>';
                } else {
                    zoomToggleBtn.innerHTML = '<i class=\"fas fa-search-plus\"></i>';
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
            
            // Habilitar funcionalidad de arrastre cuando está en zoom
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
    })();
    
    // Solucionar problema del backdrop
    $(document).on('hidden.bs.modal', '#imageModal', function () {
        // Eliminar todos los backdrops
        $('.modal-backdrop').remove();
        
        // Restaurar el scroll
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
            zoomBtn.innerHTML = '<i class=\"fas fa-search-plus\"></i>';
        }
    });
");
?> 