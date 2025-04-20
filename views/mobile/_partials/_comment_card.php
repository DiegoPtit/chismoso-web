<?php
// Vista parcial recursiva para mostrar comentarios y subcomentarios
use yii\helpers\Html;

// Función para obtener el tiempo transcurrido si no está definida globalmente
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

// Verificar el rol_id del autor del comentario
if ($comentario->usuario && $comentario->usuario->rol_id == 1313) {
    // Aplicar estilo amarillo pastel pálido para comentarios de usuario con rol_id 1313
    $bgColor = '#fef9c3'; // Amarillo pastel pálido
    $headerFooterColor = '#fef08a'; // Tono más oscuro para header/footer
    $accentColor = '#eab308'; // Acento amarillo
    $textColor = '#854d0e'; // Texto amarillo oscuro
    $cardClass = 'yellow'; // Clase para el tipo de tarjeta (amarillo)
    
    // Mantener el icono según el género seleccionado por el usuario
    if ($comentario->genre == 1) {
        $icon = '<i class="fas fa-male"></i>'; // Icono hombre
    } elseif ($comentario->genre == 2) {
        $icon = '<i class="fas fa-female"></i>'; // Icono mujer
    }
} elseif ($comentario->genre == 1) {
    $bgColor = '#ecf4ff'; // Azul más suave
    $headerFooterColor = '#e0edff'; // Azul claro 
    $accentColor = '#1a73e8'; // Azul accent Material You
    $textColor = '#174ea6'; // Texto azul oscuro
    $icon = '<i class="fas fa-male"></i>'; // Icono hombre
    $cardClass = 'blue'; // Clase para el tipo de tarjeta (azul/hombre)
} elseif ($comentario->genre == 2) {
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
if ($comentario->usuario && $comentario->usuario->rol_id) {
    if ($comentario->usuario->rol_id == 1313) {
        $shieldIcon = '<i class="fas fa-shield fa-solid" style="color: #ef4444;"></i>';
        $tooltipText = 'Creado por un Supervisor';
    } elseif ($comentario->usuario->rol_id == 1314) {
        $shieldIcon = '<i class="fas fa-shield fa-solid" style="color: #f97316;"></i>';
        $tooltipText = 'Creado por un Administrador';
    } elseif ($comentario->usuario->rol_id == 1315) {
        $shieldIcon = '<i class="fas fa-shield fa-solid" style="color: #10b981;"></i>';
        $tooltipText = 'Creado por un Moderador';
    }
}

// Verificar si el comentario está baneado
$isBanned = false;
$banReason = '';
$bannedComment = \app\models\BannedPosts::find()->where(['post_id' => $comentario->id])->one();
if ($bannedComment) {
    $isBanned = true;
    $banReason = $bannedComment->motivo;
}

?>

<div class="card mb-3 rounded-4 shadow-sm border-0 overflow-hidden card-material <?= $cardClass ?><?= $isBanned ? ' banned' : '' ?>">
    <a href="<?= Yii::$app->urlManager->createUrl(['mobile/comentarios', 'id' => $comentario->id]) ?>" 
       class="text-decoration-none" style="color: inherit;">
        <div class="card-header border-0 d-flex justify-content-between align-items-center py-2 px-3" 
             style="background-color: <?= $headerFooterColor ?>; color: <?= $textColor ?>; cursor: pointer;">
            <div class="fs-6 d-flex align-items-center">
                <?php if ($shieldIcon): ?>
                    <span class="me-2" data-bs-toggle="tooltip" data-bs-title="<?= $tooltipText ?>"><?= $shieldIcon ?></span>
                <?php endif; ?>
                <span class="fw-medium">@[<?= $comentario->id ?>]</span>
                <?php if ($isBanned): ?>
                    <span class="badge bg-danger ms-2">Baneado</span>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <div class="text-muted fs-6"><i class="far fa-clock me-1"></i> Hace <?= getTimeElapsed($comentario->created_at) ?></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="fs-6" style="color: <?= $accentColor ?>;"><?= $icon ?></div>
                <div class="fw-medium"><?= Html::encode($comentario->age) ?> años</div>
            </div>
        </div>
    </a>
    <div class="card-body py-3 px-3" style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>;">
        <?php if ($isBanned): ?>
            <div class="alert alert-danger mb-0 p-2">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Este comentario ha sido baneado por un moderador.</strong>
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
            $contenido = nl2br($comentario->contenido);
            $contenidoPlano = strip_tags($contenido);
            $longitud = mb_strlen($contenidoPlano);
            $esLargo = $longitud > 150; // Umbral más pequeño para comentarios en móviles
            
            if ($esLargo): ?>
                <div class="expandable">
                    <div class="content-preview">
                        <?= mb_substr($contenidoPlano, 0, 150) ?>...
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
            if ($comentario->img_routes) {
                try {
                    $images = json_decode($comentario->img_routes, true);
                    if (is_array($images) && !empty($images)) {
                        echo '<div class="post-images mt-3">';
                        foreach ($images as $image) {
                            if (isset($image['file'])) {
                                echo '<div class="post-image-item">';
                                echo Html::img(Yii::getAlias('@web') . $image['file'], [
                                    'alt' => 'Imagen adjunta',
                                    'class' => 'img-fluid rounded post-image',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#imageModal',
                                    'data-img-src' => Yii::getAlias('@web') . $image['file'],
                                    'loading' => 'lazy'
                                ]);
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                    }
                } catch (\Exception $e) {
                    // Si hay un error al procesar JSON, lo ignoramos
                    Yii::error('Error al procesar imágenes de comentario #' . $comentario->id . ': ' . $e->getMessage());
                }
            }
            ?>
        <?php endif; ?>
    </div>
    <div class="card-footer border-0 py-2 px-3" style="background-color: <?= $headerFooterColor ?>;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <?= Html::beginForm(['/mobile/like', 'id' => $comentario->id], 'post', ['class' => 'like-form']) ?>
                    <button type="submit" class="btn btn-sm rounded-pill px-2 me-2" 
                            style="background-color: rgba(<?= $comentario->genre == 1 ? '26, 115, 232, 0.1' : ($comentario->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                                   color: <?= $accentColor ?>;">
                        <i class="fas fa-thumbs-up me-1"></i> <strong class="likes-count"><?= $comentario->likes ?? 0 ?></strong>
                    </button>
                <?= Html::endForm() ?>

                <?= Html::beginForm(['/mobile/dislike', 'id' => $comentario->id], 'post', ['class' => 'dislike-form']) ?>
                    <button type="submit" class="btn btn-sm rounded-pill px-2 me-2" 
                            style="background-color: rgba(<?= $comentario->genre == 1 ? '26, 115, 232, 0.1' : ($comentario->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                                   color: <?= $accentColor ?>;">
                        <i class="fas fa-thumbs-down me-1"></i> <strong class="dislikes-count"><?= $comentario->dislikes ?? 0 ?></strong>
                    </button>
                <?= Html::endForm() ?>

                <a href="<?= Yii::$app->urlManager->createUrl(['mobile/comentarios', 'id' => $comentario->id]) ?>" class="btn btn-sm rounded-pill px-2" 
                        style="background-color: rgba(<?= $comentario->genre == 1 ? '26, 115, 232, 0.1' : ($comentario->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                               color: <?= $accentColor ?>;">
                    <i class="fas fa-reply me-1"></i> Responder
                </a>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm rounded-circle p-1" 
                        style="background-color: rgba(<?= $comentario->genre == 1 ? '26, 115, 232, 0.1' : ($comentario->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                               color: <?= $accentColor ?>;"
                        type="button" id="dropdownMenu<?= $comentario->id ?>" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-sm border-0 mt-2" aria-labelledby="dropdownMenu<?= $comentario->id ?>">
                    <?php if (Yii::$app->user->identity && in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])): ?>
                        <li><a class="dropdown-item py-2 px-3 ban-comment-link" href="#" data-comment-id="<?= $comentario->id ?>"><i class="fas fa-ban me-2"></i> Banear Comentario</a></li>
                        <li><a class="dropdown-item py-2 px-3 ban-user-link" href="#" data-user-id="<?= $comentario->usuario_id ?>"><i class="fas fa-user-slash me-2"></i> Banear Usuario</a></li>
                    <?php elseif (Yii::$app->user->identity && Yii::$app->user->identity->rol_id == 1316): ?>
                        <li><a class="dropdown-item py-2 px-3" href="#"><i class="fas fa-flag me-2"></i> Reportar Post</a></li>
                        <li><a class="dropdown-item py-2 px-3" href="#"><i class="fas fa-user-times me-2"></i> Reportar Usuario</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Subcomentarios recursivos -->
    <?php if (!empty($comentario->subcomentariosRecursivos)): ?>
        <div class="card-footer border-0 py-2 px-3" style="background-color: <?= $headerFooterColor ?>; border-top: none !important;">
            <button class="btn btn-sm rounded-pill w-100 text-center toggle-comments" 
                    style="background-color: rgba(<?= $comentario->genre == 1 ? '26, 115, 232, 0.1' : ($comentario->genre == 2 ? '232, 76, 136, 0.1' : '156, 163, 175, 0.1') ?>); 
                            color: <?= $accentColor ?>;"
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#subcomentarios<?= $comentario->id ?>" 
                    aria-expanded="false" 
                    aria-controls="subcomentarios<?= $comentario->id ?>"
                    data-show-text="<i class='fas fa-chevron-down me-2'></i> Mostrar <?= count($comentario->subcomentariosRecursivos) ?> respuestas"
                    data-hide-text="<i class='fas fa-chevron-up me-2'></i> Ocultar respuestas">
                <i class="fas fa-chevron-down me-2"></i> Mostrar <?= count($comentario->subcomentariosRecursivos) ?> respuestas
            </button>
        </div>
        <div class="collapse" id="subcomentarios<?= $comentario->id ?>">
            <div class="subcomentarios ms-4 mt-2 mb-2 px-2">
                <?php foreach ($comentario->subcomentariosRecursivos as $subcomentario): ?>
                    <?php 
                    // Llamada recursiva para mostrar subcomentarios
                    $comentario = $subcomentario;
                    require '_comment_card.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div> 