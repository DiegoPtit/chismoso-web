<?php
// Vista parcial para mostrar una tarjeta de post
use yii\helpers\Html;

// Función para obtener el tiempo transcurrido
if (!function_exists('getTimeElapsed')) {
    function getTimeElapsed($date) {
        $now = new \DateTime();
        $posted = new \DateTime($date);
        $diff = $posted->diff($now);
        
        if ($diff->y > 0) {
            return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        } elseif ($diff->d > 0) {
            return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
        } else {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
        }
    }
}

// Definir colores según Material You
$bgColor = '#f9fafb'; // Color neutro para incógnito
$headerFooterColor = '#f3f4f6'; // Color más suave para cabecera/pie
$accentColor = '#9ca3af'; // Color acento
$textColor = '#374151'; // Color texto principal
$icon = '<i class="fas fa-user-secret"></i>'; // Icono incógnito por defecto
$cardClass = 'incognito'; // Clase para el tipo de tarjeta (incógnito)

// Verificar el rol_id del autor del post
if ($post->usuario && $post->usuario->rol_id == 1313) {
    // Aplicar estilo amarillo pastel pálido para posts de usuario con rol_id 1313
    $bgColor = '#fef9c3'; // Amarillo pastel pálido
    $headerFooterColor = '#fef08a'; // Tono más oscuro para header/footer
    $accentColor = '#eab308'; // Acento amarillo
    $textColor = '#854d0e'; // Texto amarillo oscuro
    $cardClass = 'yellow'; // Clase para el tipo de tarjeta (amarillo)
    
    // Mantener el icono según el género seleccionado por el usuario
    if ($post->genre == 1) {
        $icon = '<i class="fas fa-male"></i>'; // Icono hombre
    } elseif ($post->genre == 2) {
        $icon = '<i class="fas fa-female"></i>'; // Icono mujer
    }
} elseif ($post->genre == 1) {
    $bgColor = '#ecf4ff'; // Azul más suave
    $headerFooterColor = '#e0edff'; // Azul claro 
    $accentColor = '#1a73e8'; // Azul accent Material You
    $textColor = '#174ea6'; // Texto azul oscuro
    $icon = '<i class="fas fa-male"></i>'; // Icono hombre
    $cardClass = 'blue'; // Clase para el tipo de tarjeta (azul/hombre)
} elseif ($post->genre == 2) {
    $bgColor = '#fdf2f8'; // Rosa suave
    $headerFooterColor = '#fce7f3'; // Rosa más claro
    $accentColor = '#e84c88'; // Rosa accent Material You
    $textColor = '#9d174d'; // Texto rosa oscuro
    $icon = '<i class="fas fa-female"></i>'; // Icono mujer
    $cardClass = 'pink'; // Clase para el tipo de tarjeta (rosa/mujer)
}

// Determinar el icono del escudo según el rol_id del usuario
$shieldIcon = '';
$tooltipText = '';
if ($post->usuario && $post->usuario->rol_id) {
    if ($post->usuario->rol_id == 1313) {
        $shieldIcon = '<i class="fas fa-shield fa-solid" style="color: #ef4444;"></i>';
        $tooltipText = 'Creado por un Supervisor';
    } elseif ($post->usuario->rol_id == 1314) {
        $shieldIcon = '<i class="fas fa-shield fa-solid" style="color: #f97316;"></i>';
        $tooltipText = 'Creado por un Administrador';
    } elseif ($post->usuario->rol_id == 1315) {
        $shieldIcon = '<i class="fas fa-shield fa-solid" style="color: #10b981;"></i>';
        $tooltipText = 'Creado por un Moderador';
    }
}

// Verificar si el post está baneado
$isBanned = false;
$banReason = '';
$bannedPost = \app\models\BannedPosts::find()->where(['post_id' => $post->id])->one();
if ($bannedPost) {
    $isBanned = true;
    $banReason = $bannedPost->motivo;
}

?>

<div class="card mb-4 rounded-4 shadow border-0 overflow-hidden card-material <?= $cardClass ?><?= $isBanned ? ' banned' : '' ?>">
    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 px-4" 
         style="background-color: <?= $headerFooterColor ?>; color: <?= $textColor ?>;">
        <div class="fs-5 d-flex align-items-center">
            <?php if ($shieldIcon): ?>
                <span class="me-2" data-bs-toggle="tooltip" data-bs-title="<?= $tooltipText ?>"><?= $shieldIcon ?></span>
            <?php endif; ?>
            <span class="fw-medium">@[<?= $post->id ?>]</span>
            
        </div>
        <div class="text-center">
            <div class="text-muted fs-6"><i class="far fa-clock me-1"></i> Hace <?= getTimeElapsed($post->created_at) ?></div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="fs-5" style="color: <?= $accentColor ?>;"><?= $icon ?></div>
            <div class="fw-medium"><?= Html::encode($post->age) ?> años</div>
        </div>
    </div>
    <div class="card-body py-4 px-4" style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
        <?php if ($isBanned): ?>
            <div class="alert alert-danger mb-0 p-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Este contenido ha sido baneado por un moderador.</strong>
                <div class="mt-2">
                    <?php 
                    switch ($banReason) {
                        case 'HATE_LANG':
                            echo 'Motivo: Contenido con lenguaje que incita al odio.';
                            break;
                        case 'KIDS_HASSARAMENT':
                            echo 'Motivo: Contenido que involucra pedofilia o acoso a menores.';
                            break;
                        case 'SENSIBLE_CONTENT':
                            echo 'Motivo: Contenido inapropiado o extremadamente sensible.';
                            break;
                        case 'SCAM':
                            echo 'Motivo: Contenido fraudulento o de estafa.';
                            break;
                        case 'SPAM':
                            echo 'Motivo: Contenido de spam o publicidad no solicitada.';
                            break;
                        case 'RACIST_LANG':
                            echo 'Motivo: Contenido que promueve racismo o xenofobia.';
                            break;
                        case 'MODERATED':
                            echo 'Motivo: Este contenido ha sido moderado por violar las normas de la comunidad.';
                            break;
                        default:
                            echo 'Motivo: Violación de las normas de la comunidad.';
                    }
                    ?>
                </div>
            </div>
        <?php else: ?>
            <?php 
            // Procesar el contenido y comprobar si es largo
            $contenido = nl2br($post->contenido);
            $contenidoPlano = strip_tags($contenido);
            $longitud = mb_strlen($contenidoPlano);
            $esLargo = $longitud > 200; // Umbral más pequeño para móviles
            
            if ($esLargo): ?>
                <div class="expandable">
                    <div class="content-preview">
                        <?= mb_substr($contenidoPlano, 0, 200) ?>...
                    </div>
                    <div class="content-full" style="display: none;">
                        <?= $contenido ?>
                    </div>
                    <button class="btn-ver-mas mt-2" data-action="expand">Ver más</button>
                </div>
            <?php else: ?>
                <p class="card-text mb-0"><?= $contenido ?></p>
            <?php endif; ?>
            
            <?php
            // Mostrar imágenes adjuntas si existen
            if ($post->img_routes) {
                try {
                    $images = json_decode($post->img_routes, true);
                    if (is_array($images) && !empty($images)) {
                        echo '<div class="post-carousel-container mt-3">';
                        echo '<div class="post-carousel">';
                        
                        // Contenedor para las imágenes
                        echo '<div class="carousel-inner">';
                        $active = true;
                        foreach ($images as $index => $image) {
                            if (isset($image['file'])) {
                                echo '<div class="carousel-item' . ($active ? ' active' : '') . '" data-index="' . $index . '">';
                                echo Html::img(Yii::getAlias('@web') . $image['file'], [
                                    'alt' => 'Imagen adjunta',
                                    'class' => 'img-fluid carousel-image rounded',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#imageModal',
                                    'data-img-src' => Yii::getAlias('@web') . $image['file'],
                                    'loading' => 'lazy',
                                    'onload' => 'this.onload=null; this.setAttribute("data-ratio", this.naturalHeight/this.naturalWidth); if(this.naturalHeight > this.naturalWidth) { this.classList.add("vertical"); } else { this.classList.add("horizontal"); }',
                                    'onerror' => 'this.onerror=null; this.src="'.Yii::getAlias('@web').'/img/image-error.png";',
                                    'width' => '100%',
                                    'height' => 'auto',
                                    'fetchpriority' => $active ? 'high' : 'low',
                                    'decoding' => 'async'
                                ]);
                                echo '</div>';
                                $active = false;
                            }
                        }
                        echo '</div>';
                        
                        // Controles de navegación
                        if (count($images) > 1) {
                            echo '<button class="carousel-control prev" data-action="prev"><i class="fas fa-chevron-left"></i></button>';
                            echo '<button class="carousel-control next" data-action="next"><i class="fas fa-chevron-right"></i></button>';
                            
                            // Indicadores (dots)
                            echo '<div class="carousel-indicators">';
                            for ($i = 0; $i < count($images); $i++) {
                                echo '<button class="carousel-dot' . ($i === 0 ? ' active' : '') . '" data-index="' . $i . '"></button>';
                            }
                            echo '</div>';
                        }
                        
                        echo '</div>'; // .post-carousel
                        echo '</div>'; // .post-carousel-container
                    }
                } catch (\Exception $e) {
                    // Si hay un error al procesar JSON, lo ignoramos
                    Yii::error('Error al procesar imágenes de post #' . $post->id . ': ' . $e->getMessage());
                }
            }
            ?>
        <?php endif; ?>
    </div>
    <div class="card-footer border-0 py-3 px-4" style="background-color: <?= $headerFooterColor ?>;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <?= Html::beginForm(['/mobile/like', 'id' => $post->id], 'post', ['class' => 'like-form']) ?>
                    <button type="submit" class="btn btn-sm rounded-pill px-3 me-2" 
                            style="background-color: rgba(<?= $post->genre == 1 ? '26, 115, 232, 0.1' : ($post->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                                   color: <?= $accentColor ?>;">
                        <i class="fas fa-thumbs-up me-1"></i> <strong class="likes-count"><?= $post->likes ?? 0 ?></strong>
                    </button>
                <?= Html::endForm() ?>

                <?= Html::beginForm(['/mobile/dislike', 'id' => $post->id], 'post', ['class' => 'dislike-form']) ?>
                    <button type="submit" class="btn btn-sm rounded-pill px-3 me-2" 
                            style="background-color: rgba(<?= $post->genre == 1 ? '26, 115, 232, 0.1' : ($post->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                                   color: <?= $accentColor ?>;">
                        <i class="fas fa-thumbs-down me-1"></i> <strong class="dislikes-count"><?= $post->dislikes ?? 0 ?></strong>
                    </button>
                <?= Html::endForm() ?>

                <a href="<?= Yii::$app->urlManager->createUrl(['mobile/comentarios', 'id' => $post->id]) ?>" 
                   class="btn btn-sm rounded-pill px-3" 
                   style="background-color: rgba(<?= $post->genre == 1 ? '26, 115, 232, 0.1' : ($post->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                          color: <?= $accentColor ?>;">
                    <i class="fas fa-comment me-1"></i> Comentarios
                </a>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm rounded-circle p-2" 
                        style="background-color: rgba(<?= $post->genre == 1 ? '26, 115, 232, 0.1' : ($post->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                               color: <?= $accentColor ?>;"
                        type="button" id="dropdownMenu<?= $post->id ?>" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-sm border-0 mt-2" aria-labelledby="dropdownMenu<?= $post->id ?>">
                    <?php if (Yii::$app->user->identity && in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])): ?>
                        <li><a class="dropdown-item py-2 px-3 ban-post-link" href="#" data-post-id="<?= $post->id ?>"><i class="fas fa-ban me-2"></i> Banear Post</a></li>
                        <li><a class="dropdown-item py-2 px-3 ban-user-link" href="#" data-post-id="<?= $post->id ?>" data-user-id="<?= $post->usuario_id ?>"><i class="fas fa-user-slash me-2"></i> Banear Usuario</a></li>
                    <?php elseif (Yii::$app->user->identity && Yii::$app->user->identity->rol_id == 1316): ?>
                        <li><a class="dropdown-item py-2 px-3" href="#"><i class="fas fa-flag me-2"></i> Reportar Post</a></li>
                        <li><a class="dropdown-item py-2 px-3" href="#"><i class="fas fa-user-times me-2"></i> Reportar Usuario</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div> 
</div> 