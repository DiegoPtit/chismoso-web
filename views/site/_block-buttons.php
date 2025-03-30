<?php
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity;
$userRole = $user ? $user->rol_id : null;
$currentUserId = $user ? $user->id : null;

// Debug info
echo "<!-- Debug: User ID: " . ($user ? $user->id : 'not logged in') . " -->";
echo "<!-- Debug: User Role: " . $userRole . " -->";
echo "<!-- Debug: Post ID: " . (isset($post_id) ? $post_id : 'not set') . " -->";
echo "<!-- Debug: Usuario ID: " . (isset($usuario_id) ? $usuario_id : 'not set') . " -->";

// Verificar si el usuario tiene los roles permitidos
$showBlockPost = $userRole && in_array($userRole, [1313, 1314, 1315]);
$showBlockUser = $userRole && in_array($userRole, [1313, 1314, 1315]);

// Verificar si el usuario está intentando banear su propio contenido
$canBlockPost = $showBlockPost && isset($post_id) && isset($usuario_id) && $usuario_id != $currentUserId;
$canBlockUser = $showBlockUser && isset($usuario_id) && $usuario_id != $currentUserId;

echo "<!-- Debug: Show Block Post: " . ($showBlockPost ? 'true' : 'false') . " -->";
echo "<!-- Debug: Show Block User: " . ($showBlockUser ? 'true' : 'false') . " -->";
?>

<?php if ($canBlockPost || $canBlockUser): ?>
    <div class="d-flex gap-2">
        <?php if ($canBlockPost): ?>
            <?= Html::button(
                '<i class="fa fa-ban"></i>',
                [
                    'class' => 'btn btn-danger btn-sm ban-post-btn',
                    'data-post-id' => $post_id,
                    'data-user-id' => $usuario_id,
                    'title' => 'Bloquear Post',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top'
                ]
            ) ?>
        <?php endif; ?>

        <?php if ($canBlockUser): ?>
            <?= Html::button(
                '<i class="fa fa-user-slash"></i>',
                [
                    'class' => 'btn btn-danger btn-sm ban-user-btn',
                    'data-user-id' => $usuario_id,
                    'title' => 'Bloquear Usuario',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top'
                ]
            ) ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
.ban-post-btn,
.ban-user-btn {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.ban-post-btn i,
.ban-user-btn i {
    font-size: 1rem;
    margin: 0;
}

.ban-post-btn:hover,
.ban-user-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

@media (max-width: 767px) {
    .ban-post-btn,
    .ban-user-btn {
        width: 32px;
        height: 32px;
    }
    
    .ban-post-btn i,
    .ban-user-btn i {
        font-size: 0.9rem;
    }
}
</style>

<?php
$csrfToken = Yii::$app->request->csrfToken;
$banPostUrl = Url::to(['site/ban-post']);
$banUserUrl = Url::to(['site/ban-user']);

$this->registerJs(<<<JS
    $(document).ready(function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Inicializar modales
        const confirmBanModal = new bootstrap.Modal(document.getElementById('confirmBanModal'));
        const banModal = new bootstrap.Modal(document.getElementById('banModal'));
        
        let currentAction = null;
        let currentData = null;
        
        function showModal(message, type) {
            const modalContent = $('#banModalContent');
            const modalTitle = $('#banModal').find('.modal-title');
            
            modalContent.html(message);
            modalTitle.text(type === 'success' ? '¡Éxito!' : '¡Error!');
            
            if (type === 'success') {
                modalTitle.removeClass('text-danger').addClass('text-success');
            } else {
                modalTitle.removeClass('text-success').addClass('text-danger');
            }
            
            banModal.show();
            
            if (type === 'success') {
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        }

        function handleBanAction() {
            if (!currentAction || !currentData) return;
            
            confirmBanModal.hide();
            
            $.ajax({
                url: currentAction,
                type: 'POST',
                data: currentData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showModal(response.message, 'success');
                    } else {
                        showModal(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showModal('Error al procesar la solicitud: ' + error, 'error');
                }
            });
        }

        // Manejo del botón de bloqueo de post
        $(document).on('click', '.ban-post-btn', function(e) {
            e.preventDefault();
            const postId = $(this).data('post-id');
            
            if (!postId) {
                showModal('Error: No se encontró el ID del post', 'error');
                return;
            }
            
            currentAction = '$banPostUrl';
            currentData = { 
                post_id: postId,
                _csrf: '$csrfToken'
            };
            
            confirmBanModal.show();
        });

        // Manejo del botón de bloqueo de usuario
        $(document).on('click', '.ban-user-btn', function(e) {
            e.preventDefault();
            const userId = $(this).data('user-id');
            
            if (!userId) {
                showModal('Error: No se encontró el ID del usuario', 'error');
                return;
            }
            
            currentAction = '$banUserUrl';
            currentData = { 
                usuario_id: userId,
                _csrf: '$csrfToken'
            };
            
            confirmBanModal.show();
        });

        // Manejar la confirmación
        $('#confirmBanButton').on('click', handleBanAction);
    });
JS
);
?> 