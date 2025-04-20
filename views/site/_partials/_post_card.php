<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $post app\models\Posts */
?>

<div class="forum-post mb-4" id="post-<?= $post->id ?>">
    <div class="d-flex align-items-start">
        <?php
        $avatarClass = 'user-neutral';
        if ($post->usuario) {
            $rolId = $post->usuario->rol_id;
            if ($rolId == 1313) {
                $avatarClass = 'superadmin';
            } elseif ($rolId == 1314) {
                $avatarClass = 'admin';
            } elseif ($rolId == 1315) {
                $avatarClass = 'mod';
            } else {
                switch ($post->genre) {
                    case 1: $avatarClass = 'user-male'; break;
                    case 2: $avatarClass = 'user-female'; break;
                    default: $avatarClass = 'user-neutral';
                }
            }
        }
        ?>
        <div class="avatar <?= $avatarClass ?>">
            <i class="fas fa-user"></i>
        </div>
        <div class="post-content ms-2 flex-grow-1">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="text-muted">Anónimo</span>
                    <?php if ($post->age): ?>
                        <span class="badge rounded-pill bg-secondary ms-1"><?= $post->age ?> años</span>
                    <?php endif; ?>
                </div>
                <div class="post-date">
                    <small class="text-muted"><?= Yii::$app->formatter->asRelativeTime($post->created_at) ?></small>
                    <?php if (Yii::$app->user->identity && in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger ban-post-btn" data-post-id="<?= $post->id ?>">
                            <i class="fas fa-ban"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="post-text mt-2 mb-2">
                <?= nl2br(Html::encode($post->contenido)) ?>
            </div>
            
            <?php
            // Mostrar imágenes adjuntas si existen
            if ($post->img_routes) {
                try {
                    $images = json_decode($post->img_routes, true);
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
                    Yii::error('Error al procesar imágenes de post #' . $post->id . ': ' . $e->getMessage());
                }
            }
            ?>
            
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="post-actions">
                    <a href="<?= Url::to(['site/comentarios', 'id' => $post->id]) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-comment-alt"></i> 
                        <?= count($post->posts) ?> Comentarios
                    </a>
                    <a href="#" class="like-btn btn btn-sm btn-outline-success" data-post-id="<?= $post->id ?>">
                        <i class="fas fa-thumbs-up"></i> 
                        <span class="like-count"><?= $post->likes ?></span>
                    </a>
                    <a href="#" class="dislike-btn btn btn-sm btn-outline-danger" data-post-id="<?= $post->id ?>">
                        <i class="fas fa-thumbs-down"></i> 
                        <span class="dislike-count"><?= $post->dislikes ?></span>
                    </a>
                    <a href="<?= Url::to(['site/reportar', 'post_id' => $post->id]) ?>" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-flag"></i> Reportar
                    </a>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?= $post->id ?>" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton<?= $post->id ?>">
                        <li><a class="dropdown-item share-post" href="#" data-post-id="<?= $post->id ?>"><i class="fas fa-share-alt"></i> Compartir</a></li>
                        <?php if (Yii::$app->user->identity && $post->usuario_id && $post->usuario_id !== Yii::$app->user->id): ?>
                            <li><a class="dropdown-item" href="<?= Url::to(['site/reportar', 'usuario_id' => $post->usuario_id]) ?>"><i class="fas fa-user-slash"></i> Reportar usuario</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <?php if (count($post->posts) > 0 && isset($post->posts[0])): ?>
                <div class="top-comment mt-3 p-3 border-start">
                    <div class="d-flex align-items-start">
                        <?php
                        $commentAvatarClass = 'user-neutral';
                        if ($post->posts[0]->usuario) {
                            $commentRolId = $post->posts[0]->usuario->rol_id;
                            if ($commentRolId == 1313) {
                                $commentAvatarClass = 'superadmin';
                            } elseif ($commentRolId == 1314) {
                                $commentAvatarClass = 'admin';
                            } elseif ($commentRolId == 1315) {
                                $commentAvatarClass = 'mod';
                            } else {
                                switch ($post->posts[0]->genre) {
                                    case 1: $commentAvatarClass = 'user-male'; break;
                                    case 2: $commentAvatarClass = 'user-female'; break;
                                    default: $commentAvatarClass = 'user-neutral';
                                }
                            }
                        }
                        ?>
                        <div class="avatar avatar-sm <?= $commentAvatarClass ?>">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="ms-2">
                            <div class="d-flex align-items-center">
                                <span class="text-muted small">Anónimo</span>
                                <small class="ms-2 text-muted"><?= Yii::$app->formatter->asRelativeTime($post->posts[0]->created_at) ?></small>
                            </div>
                            <div class="mt-1 small">
                                <?= Html::encode($post->posts[0]->contenido) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.post-image-item {
    display: inline-block;
    margin: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 3px;
    background-color: white;
}

.post-image {
    max-width: 120px;
    max-height: 120px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.post-image:hover {
    transform: scale(1.05);
}

.post-images {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

/* Para una sola imagen, que ocupe más espacio */
.post-images:has(.post-image-item:only-child) .post-image-item {
    flex: 0 0 auto;
    max-width: 250px;
}

.post-images:has(.post-image-item:only-child) .post-image {
    max-width: 250px;
    max-height: 250px;
    object-fit: contain;
}
</style> 