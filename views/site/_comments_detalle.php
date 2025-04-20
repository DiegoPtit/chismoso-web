<?php
/**
 * Vista parcial para mostrar los comentarios en la vista de detalles
 * 
 * @var $comentarios array|app\models\Posts[]
 * @var $commentId integer ID del comentario a resaltar (opcional)
 * @var $nivel integer Nivel de anidamiento
 */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>

<div class="forum-comments-section">
    <h3 class="comments-title">Comentarios (<?= count($comentarios) ?>)</h3>
    
    <?php if (empty($comentarios)): ?>
        <div class="no-comments-message">
            <i class="far fa-comment-dots"></i>
            <p>No hay comentarios. ¡Sé el primero en comentar!</p>
        </div>
    <?php else: ?>
        <div class="forum-comments">
            <?php foreach ($comentarios as $comentario): ?>
                <?php 
                // Determinar si este comentario debe ser resaltado
                $isHighlighted = isset($commentId) && $comentario->id == $commentId;
                $commentClass = $isHighlighted ? 'forum-comment highlighted nivel-' . $nivel : 'forum-comment nivel-' . $nivel;
                
                // Determinar el género para el icono
                $genreIcon = 'fa-user';
                switch ($comentario->genre) {
                    case 0: $genreIcon = 'fa-user'; break;
                    case 1: $genreIcon = 'fa-user-circle'; break;
                    case 2: $genreIcon = 'fa-user-tie'; break;
                }
                
                // Determinar la clase del avatar según el rol_id del usuario
                $userRoleClass = '';
                if (isset($comentario->usuario)) {
                    $rolId = $comentario->usuario->rol_id;
                    
                    if ($rolId == 1313) {
                        $userRoleClass = 'superadmin';
                    } elseif ($rolId == 1314) {
                        $userRoleClass = 'admin';
                    } elseif ($rolId == 1315) {
                        $userRoleClass = 'mod';
                    } elseif ($rolId == 1316) {
                        switch ($comentario->genre) {
                            case 0: $userRoleClass = 'user-neutral'; break;
                            case 1: $userRoleClass = 'user-female'; break;
                            case 2: $userRoleClass = 'user-male'; break;
                        }
                    }
                }
                ?>
                
                <div id="comment-<?= $comentario->id ?>" class="<?= $commentClass ?>">
                    <div class="forum-comment-header">
                        <div class="avatar small <?= $userRoleClass ?>">
                            <i class="fas <?= $genreIcon ?>"></i>
                        </div>
                        <div class="comment-info">
                            <span class="username">Anónimo</span>
                            <span class="comment-date">
                                <i class="far fa-clock"></i> 
                                <?= Yii::$app->formatter->asRelativeTime($comentario->created_at) ?>
                                <?php if (isset($comentario->age)): ?>
                                    <span class="ms-2"><i class="fas fa-birthday-cake"></i> <?= $comentario->age ?> años</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="post-stats">
                            <span class="stat-item"><i class="far fa-thumbs-up"></i> <?= $comentario->likes ?? 0 ?></span>
                            <span class="stat-item"><i class="far fa-thumbs-down"></i> <?= $comentario->dislikes ?? 0 ?></span>
                            <span class="stat-item"><i class="far fa-comment"></i> <?= count($comentario->subcomentarios ?? []) ?></span>
                        </div>
                    </div>
                    
                    <div class="forum-comment-content">
                        <?php 
                        $contenido = HtmlPurifier::process($comentario->contenido);
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
                    </div>
                    
                    <?php
                    // Mostrar imágenes adjuntas si existen
                    if ($comentario->img_routes) {
                        try {
                            $images = json_decode($comentario->img_routes, true);
                            if (is_array($images) && !empty($images)) {
                                echo '<div class="post-images mt-2 mb-2">';
                                foreach ($images as $image) {
                                    if (isset($image['file'])) {
                                        echo '<div class="post-image-item">';
                                        echo Html::img($image['file'], [
                                            'alt' => 'Imagen adjunta',
                                            'class' => 'img-fluid post-image',
                                            'data-bs-toggle' => 'modal',
                                            'data-bs-target' => '#imageModal',
                                            'data-img-src' => $image['file']
                                        ]);
                                        echo '</div>';
                                    }
                                }
                                echo '</div>';
                            }
                        } catch (\Exception $e) {
                            // Si hay un error al procesar JSON, lo ignoramos
                            Yii::error('Error al procesar imágenes del comentario #' . $comentario->id . ': ' . $e->getMessage());
                        }
                    }
                    ?>
                    
                    <div class="forum-comment-actions">
                        <button type="button" class="btn-forum like-button" data-post-id="<?= $comentario->id ?>">
                            <i class="far fa-thumbs-up"></i> Me gusta
                        </button>
                        <button type="button" class="btn-forum dislike-button" data-post-id="<?= $comentario->id ?>">
                            <i class="far fa-thumbs-down"></i> No me gusta
                        </button>
                        <button type="button" class="btn-forum reply-button" data-post-id="<?= $comentario->id ?>">
                            <i class="far fa-comment"></i> Responder
                        </button>
                    </div>
                    
                    <!-- Formulario de respuesta para comentarios -->
                    <?= $this->render('_comment_form', [
                        'model' => $comentario,
                        'formId' => 'form-comment-' . $comentario->id,
                        'inputId' => 'content-comment-' . $comentario->id,
                        'type' => 'comment',
                    ]) ?>
                    
                    <!-- Si tiene subcomentarios, renderizarlos recursivamente -->
                    <?php if (!empty($comentario->subcomentarios)): ?>
                        <div class="comments-nested">
                            <div class="forum-comments-header" id="subcomments-toggle-<?= $comentario->id ?>">
                                <i class="fas fa-comments"></i> 
                                <span>Ver <?= count($comentario->subcomentarios) ?> respuestas</span>
                            </div>
                            <div class="comments-list" id="subcomments-container-<?= $comentario->id ?>" style="display: none;">
                                <?= $this->render('_comments_detalle', [
                                    'comentarios' => $comentario->subcomentarios,
                                    'nivel' => $nivel + 1,
                                    'commentId' => $commentId,
                                ]) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div> 