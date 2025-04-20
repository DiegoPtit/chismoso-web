<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Gestión de Contenido';

// Verificar que el usuario está autenticado y tiene rol adecuado
if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['site/index']);
}

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;

// URLs para las acciones AJAX
$desbloquearPostUrl = Url::to(['site/desbloquear-post']);
$desbloquearUsuarioUrl = Url::to(['site/desbloquear-usuario']);
?>

<div class="site-admin-contenido">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-shield-alt"></i> Gestión de Contenido</h1>
        <p class="lead text-center mb-4">Administra el contenido bloqueado de la plataforma</p>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="forum-container">
                    <div class="forum-post">
                        <!-- Tabs para navegación -->
                        <ul class="nav nav-tabs nav-fill mb-4" id="contentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="true">
                                    <i class="fas fa-file-alt me-2"></i> Posts Bloqueados
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="false">
                                    <i class="fas fa-users me-2"></i> Usuarios Bloqueados
                                </button>
                            </li>
                        </ul>

                        <!-- Contenido de tabs -->
                        <div class="tab-content" id="contentTabsContent">
                            <!-- Tab de Posts Bloqueados -->
                            <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                                <?php if (empty($postsBaneados)): ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle me-2"></i> No hay posts bloqueados
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Contenido</th>
                                                    <th>Motivo</th>
                                                    <th>Fecha</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($postsBaneados as $bannedPost): ?>
                                                    <tr>
                                                        <td><?= Html::encode($bannedPost->post_id) ?></td>
                                                        <td>
                                                            <div class="content-preview"><?= Html::encode($bannedPost->post->contenido) ?></div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-danger">
                                                                <?= Html::encode($motivos[$bannedPost->motivo] ?? 'Desconocido') ?>
                                                            </span>
                                                        </td>
                                                        <td><?= Yii::$app->formatter->asDatetime($bannedPost->at_time) ?></td>
                                                        <td>
                                                            <button class="btn btn-success btn-sm desbloquear-post" 
                                                                    data-id="<?= $bannedPost->id ?>">
                                                                <i class="fas fa-unlock me-1"></i> Desbloquear
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tab de Usuarios Bloqueados -->
                            <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
                                <?php if (empty($usuariosBaneados)): ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle me-2"></i> No hay usuarios bloqueados
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Usuario</th>
                                                    <th>Rol</th>
                                                    <th>Motivo</th>
                                                    <th>Fecha</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($usuariosBaneados as $bannedUser): ?>
                                                    <tr>
                                                        <td><?= Html::encode($bannedUser->usuario_id) ?></td>
                                                        <td><?= Html::encode($bannedUser->usuario->user) ?></td>
                                                        <td>
                                                            <?php
                                                            $rolClass = '';
                                                            $rolText = '';
                                                            switch($bannedUser->usuario->rol_id) {
                                                                case 1313:
                                                                    $rolClass = 'bg-danger';
                                                                    $rolText = 'SUPERSU';
                                                                    break;
                                                                case 1314:
                                                                    $rolClass = 'bg-warning';
                                                                    $rolText = 'ADMIN';
                                                                    break;
                                                                case 1315:
                                                                    $rolClass = 'bg-info';
                                                                    $rolText = 'MOD';
                                                                    break;
                                                                case 1316:
                                                                    $rolClass = 'bg-success';
                                                                    $rolText = 'USER';
                                                                    break;
                                                                default:
                                                                    $rolClass = 'bg-secondary';
                                                                    $rolText = 'DESC';
                                                            }
                                                            ?>
                                                            <span class="badge <?= $rolClass ?>"><?= $rolText ?></span>
                                                        </td>
                                                        <td><?= Yii::$app->formatter->asDatetime($bannedUser->at_time) ?></td>
                                                        <td>
                                                            <button class="btn btn-success btn-sm desbloquear-usuario" 
                                                                    data-id="<?= $bannedUser->id ?>">
                                                                <i class="fas fa-unlock me-1"></i> Desbloquear
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">¿Está seguro de que desea desbloquear este elemento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificaciones -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="resultToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notificación</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

<style>
/* Estilos para tabs */
.nav-tabs {
    border-bottom: 1px solid #e0e0e0;
}

.nav-tabs .nav-item {
    margin-bottom: -1px;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem;
    border-radius: 0;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #6c5ce7;
}

.nav-tabs .nav-link.active {
    color: #6c5ce7;
    background-color: transparent;
    border-bottom: 3px solid #6c5ce7;
}

/* Estilos para tablas */
.table {
    margin-bottom: 0;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #6c5ce7;
    border-top: none;
    padding: 1rem;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 1rem;
}

.table tr:hover {
    background-color: #f8f9fa;
}

.content-preview {
    max-width: 300px;
    max-height: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Estilos para badges y botones */
.badge {
    padding: 0.5em 0.8em;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 20px;
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
    border-radius: 6px;
}

.btn-success {
    background-color: #00b894;
    border-color: #00b894;
}

.btn-success:hover {
    background-color: #00a884;
    border-color: #00a884;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Estilos para alertas */
.alert {
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 0;
}

/* Modal */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.modal-header {
    border-bottom: 1px solid #f1f1f1;
    padding: 1.25rem 1.5rem;
}

.modal-footer {
    border-top: 1px solid #f1f1f1;
    padding: 1.25rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

/* Toast */
.toast {
    background-color: #fff;
    border: none;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Responsivo */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
    }
    
    .content-preview {
        max-width: 150px;
    }
    
    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal y toast
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const resultToast = new bootstrap.Toast(document.getElementById('resultToast'));
    
    // Elementos DOM
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmButton = document.getElementById('confirmButton');
    const toastMessage = document.getElementById('toastMessage');
    
    // Variables para la acción actual
    let currentAction = '';
    let currentId = '';
    
    // Evento para desbloquear posts
    document.querySelectorAll('.desbloquear-post').forEach(button => {
        button.addEventListener('click', function() {
            currentAction = 'post';
            currentId = this.getAttribute('data-id');
            
            confirmMessage.textContent = '¿Está seguro de que desea desbloquear este post?';
            confirmModal.show();
        });
    });
    
    // Evento para desbloquear usuarios
    document.querySelectorAll('.desbloquear-usuario').forEach(button => {
        button.addEventListener('click', function() {
            currentAction = 'usuario';
            currentId = this.getAttribute('data-id');
            
            confirmMessage.textContent = '¿Está seguro de que desea desbloquear este usuario?';
            confirmModal.show();
        });
    });
    
    // Evento para confirmar acción
    confirmButton.addEventListener('click', function() {
        confirmModal.hide();
        
        if (currentAction === 'post') {
            // Desbloquear post
            fetch('<?= $desbloquearPostUrl ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?= $csrfToken ?>'
                },
                body: 'id=' + currentId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    toastMessage.innerHTML = '<div class="text-success"><i class="fas fa-check-circle me-2"></i>' + 
                                           (data.message || 'Post desbloqueado con éxito') + '</div>';
                    resultToast.show();
                    
                    // Eliminar la fila de la tabla
                    const row = document.querySelector(`.desbloquear-post[data-id="${currentId}"]`).closest('tr');
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        
                        // Si no quedan elementos, mostrar mensaje
                        const tbody = document.querySelector('#posts table tbody');
                        if (tbody && tbody.children.length === 0) {
                            document.querySelector('#posts').innerHTML = `
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i> No hay posts bloqueados
                                </div>
                            `;
                        }
                    }, 500);
                } else {
                    // Mostrar mensaje de error
                    toastMessage.innerHTML = '<div class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>' + 
                                           (data.message || 'Error al desbloquear el post') + '</div>';
                    resultToast.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastMessage.innerHTML = '<div class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>' + 
                                       'Error al procesar la solicitud' + '</div>';
                resultToast.show();
            });
        } else if (currentAction === 'usuario') {
            // Desbloquear usuario
            fetch('<?= $desbloquearUsuarioUrl ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?= $csrfToken ?>'
                },
                body: 'id=' + currentId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    toastMessage.innerHTML = '<div class="text-success"><i class="fas fa-check-circle me-2"></i>' + 
                                           (data.message || 'Usuario desbloqueado con éxito') + '</div>';
                    resultToast.show();
                    
                    // Eliminar la fila de la tabla
                    const row = document.querySelector(`.desbloquear-usuario[data-id="${currentId}"]`).closest('tr');
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        
                        // Si no quedan elementos, mostrar mensaje
                        const tbody = document.querySelector('#usuarios table tbody');
                        if (tbody && tbody.children.length === 0) {
                            document.querySelector('#usuarios').innerHTML = `
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i> No hay usuarios bloqueados
                                </div>
                            `;
                        }
                    }, 500);
                } else {
                    // Mostrar mensaje de error
                    toastMessage.innerHTML = '<div class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>' + 
                                           (data.message || 'Error al desbloquear el usuario') + '</div>';
                    resultToast.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastMessage.innerHTML = '<div class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>' + 
                                       'Error al procesar la solicitud' + '</div>';
                resultToast.show();
            });
        }
    });
    
    // Hacer que todos los elementos de la tabla tengan transición
    document.querySelectorAll('tr').forEach(row => {
        row.style.transition = 'opacity 0.5s ease';
    });
});
</script> 