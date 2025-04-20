<?php
/**
 * Vista parcial para mostrar un post individual
 * 
 * @var $post app\models\Posts
 */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>

<div class="forum-post">
    <div class="forum-post-header">
        <?php 
        // Determinar el género para el icono
        $genreIcon = 'fa-user';
        switch ($post->genre) {
            case 0: $genreIcon = 'fa-user'; break;
            case 1: $genreIcon = 'fa-user-circle'; break;
            case 2: $genreIcon = 'fa-user-tie'; break;
        }
        
        // Determinar la clase del avatar según el rol_id del usuario
        $userRoleClass = '';
        $rolId = $post->usuario->rol_id;
        
        if ($rolId == 1313) {
            $userRoleClass = 'superadmin'; // Amarillo
        } elseif ($rolId == 1314) {
            $userRoleClass = 'admin'; // Naranja
        } elseif ($rolId == 1315) {
            $userRoleClass = 'mod'; // Verde
        } elseif ($rolId == 1316) {
            // Para usuarios normales, el color depende del género
            switch ($post->genre) {
                case 0: $userRoleClass = 'user-neutral'; break; // Gris
                case 1: $userRoleClass = 'user-female'; break;  // Azul
                case 2: $userRoleClass = 'user-male'; break;    // Rosado
            }
        }
        ?>
        <div class="avatar <?= $userRoleClass ?>">
            <i class="fas <?= $genreIcon ?>"></i>
        </div>
        <div class="post-info">
            <span class="username">Anónimo</span>
            <span class="post-date">
                <i class="far fa-clock"></i> 
                <?= Yii::$app->formatter->asRelativeTime($post->created_at) ?>
                <?php if (isset($post->age)): ?>
                    <span class="ms-2"><i class="fas fa-birthday-cake"></i> <?= $post->age ?> años</span>
                <?php endif; ?>
            </span>
        </div>
        <div class="post-stats">
            <span class="stat-item"><i class="far fa-thumbs-up"></i> <?= $post->likes ?? 0 ?></span>
            <span class="stat-item"><i class="far fa-thumbs-down"></i> <?= $post->dislikes ?? 0 ?></span>
            <span class="stat-item"><i class="far fa-comment"></i> <?= count($post->subcomentarios ?? []) ?></span>
        </div>
    </div>
    
    <div class="forum-post-content">
        <?php 
        $contenido = HtmlPurifier::process($post->contenido);
        $longitud = mb_strlen(strip_tags($contenido));
        $esLargo = $longitud > 300;
        
        if ($esLargo): ?>
            <div class="expandable">
                <div class="content-preview"><?= mb_substr(strip_tags($contenido), 0, 300) ?>...</div>
                <div class="content-full" style="display: none;"><?= $contenido ?></div>
                <button class="btn-ver-mas" data-action="expand">Ver más</button>
            </div>
        <?php else: ?>
            <?= $contenido ?>
        <?php endif; ?>
        
        <?php
        // Mostrar imágenes adjuntas si existen
        if ($post->img_routes) {
            try {
                $images = json_decode($post->img_routes, true);
                if (is_array($images) && !empty($images)) {
                    echo '<div class="post-carousel-container mt-2 mb-3">';
                    echo '<div class="post-carousel">';
                    
                    // Contenedor para las imágenes
                    echo '<div class="carousel-inner">';
                    $active = true;
                    foreach ($images as $index => $image) {
                        if (isset($image['file'])) {
                            echo '<div class="carousel-item' . ($active ? ' active' : '') . '" data-index="' . $index . '">';
                            echo Html::img(Yii::getAlias('@web') . $image['file'], [
                                'alt' => 'Imagen adjunta',
                                'class' => 'img-fluid carousel-image',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#imageModal',
                                'data-img-src' => Yii::getAlias('@web') . $image['file']
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
    </div>
    
    <div class="forum-post-actions">
        <button type="button" class="btn-forum like-button" data-post-id="<?= $post->id ?>">
            <i class="far fa-thumbs-up"></i> Me gusta
        </button>
        <button type="button" class="btn-forum dislike-button" data-post-id="<?= $post->id ?>">
            <i class="far fa-thumbs-down"></i> No me gusta
        </button>
        <button type="button" class="btn-forum comment-button" data-post-id="<?= $post->id ?>">
            <i class="far fa-comment"></i> Comentar
        </button>
    </div>
    
    <!-- Formulario para comentar en posts -->
    <?= $this->render('_comment_form', [
        'model' => $post,
        'formId' => 'form-post-' . $post->id,
        'inputId' => 'content-post-' . $post->id,
        'type' => 'post',
    ]) ?>

    <?php if ($post->subcomentarios): ?>
    <div class="forum-comments">
        <div class="forum-comments-header" id="comments-toggle-<?= $post->id ?>">
            <i class="fas fa-comments"></i> 
            <span>Ver <?= count($post->subcomentarios) ?> comentarios</span>
        </div>
        
        <div class="comments-list" id="comments-container-<?= $post->id ?>" style="display: none;">
            <?= $this->render('_comments', [
                'comentarios' => $post->subcomentarios,
                'nivel' => 0,
            ]) ?>
        </div>
    </div>
    <?php endif; ?>
</div> 