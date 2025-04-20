<?php
/**
 * Vista parcial para mostrar comentarios
 * 
 * @var $comentarios array
 * @var $nivel int
 */

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

if (empty($comentarios)) {
    return;
}

foreach ($comentarios as $comentario): ?>
    <div class="forum-comment nivel-<?= $nivel ?>">
        <div class="forum-comment-header">
            <?php
            // Determinar el género para el icono del comentario
            $commentIcon = 'fa-user';
            switch ($comentario->genre) {
                case 0: $commentIcon = 'fa-user'; break;
                case 1: $commentIcon = 'fa-user-circle'; break;
                case 2: $commentIcon = 'fa-user-tie'; break;
            }
            
            // Determinar la clase del avatar según el rol_id del usuario del comentario
            $commentRoleClass = '';
            $comentarioRolId = $comentario->usuario->rol_id;
            
            if ($comentarioRolId == 1313) {
                $commentRoleClass = 'superadmin';
            } elseif ($comentarioRolId == 1314) {
                $commentRoleClass = 'admin';
            } elseif ($comentarioRolId == 1315) {
                $commentRoleClass = 'mod';
            } elseif ($comentarioRolId == 1316) {
                switch ($comentario->genre) {
                    case 0: $commentRoleClass = 'user-neutral'; break;
                    case 1: $commentRoleClass = 'user-female'; break;
                    case 2: $commentRoleClass = 'user-male'; break;
                }
            }
            ?>
            <div class="avatar small <?= $commentRoleClass ?>">
                <i class="fas <?= $commentIcon ?>"></i>
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
        
        <?php // Procesar contenido
        $contenido = HtmlPurifier::process($comentario->contenido);
        $longitud = mb_strlen(strip_tags($contenido));
        $esLargo = $longitud > 300;
        ?>
        
        <div class="forum-comment-content<?= $esLargo ? ' expandable' : '' ?>">
            <?php if ($esLargo): ?>
                <div class="content-preview"><?= mb_substr(strip_tags($contenido), 0, 300) ?>...</div>
                <div class="content-full" style="display: none;"><?= $contenido ?></div>
                <button class="btn-ver-mas" data-action="expand">Ver más</button>
            <?php else: ?>
                <?= $contenido ?>
            <?php endif; ?>
        </div>
        
        <?php // Mostrar imágenes adjuntas si existen
        if ($comentario->img_routes) {
            try {
                $images = json_decode($comentario->img_routes, true);
                if (is_array($images) && !empty($images)) {
                    echo '<div class="post-images mt-2 mb-2">';
                    foreach ($images as $image) {
                        if (isset($image['file'])) {
                            echo '<div class="post-image-item">';
                            echo Html::img(Yii::getAlias('@web') . $image['file'], [
                                'alt' => 'Imagen adjunta',
                                'class' => 'img-fluid post-image',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#imageModal',
                                'data-img-src' => Yii::getAlias('@web') . $image['file']
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
        
        <!-- Botones de acción para los comentarios -->
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
                    <?= $this->render('_comments', [
                        'comentarios' => $comentario->subcomentarios,
                        'nivel' => $nivel + 1,
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
<?php endforeach; ?> 