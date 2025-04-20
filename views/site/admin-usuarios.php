<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administración de Usuarios';

// Verificar que el usuario está autenticado y tiene rol adecuado
if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['site/index']);
}

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;

// Generar las URLs antes del script
$cambiarRolUrl = Url::to(['admin/cambiar-rol']);
$eliminarUsuarioUrl = Url::to(['admin/eliminar-usuario']);
?>

<div class="site-admin-usuarios">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-users"></i> Administración de Usuarios</h1>
        <p class="lead text-center mb-4">Gestiona los usuarios del sistema</p>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="forum-container">
                    <div class="forum-post">
                        <!-- Estadísticas Rápidas -->
                        <div class="stats-overview mb-4">
                            <div class="row">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card">
                                        <h3 class="stats-number"><?= count($usuarios) ?></h3>
                                        <p class="stats-label">Total</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-danger">
                                        <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1313)) ?></h3>
                                        <p class="stats-label">SUPERSU</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-warning">
                                        <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1314)) ?></h3>
                                        <p class="stats-label">ADMIN</p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-info">
                                        <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1315)) ?></h3>
                                        <p class="stats-label">MOD</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Usuarios -->
                        <div class="data-container">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td><?= Html::encode($usuario->id) ?></td>
                                                <td><?= Html::encode($usuario->user) ?></td>
                                                <td>
                                                    <?php
                                                    $rolClass = '';
                                                    $rolText = '';
                                                    switch($usuario->rol_id) {
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
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-user-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1313" data-usuario="<?= $usuario->id ?>"><i class="fas fa-crown"></i> SUPERSU</a></li>
                                                            <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1314" data-usuario="<?= $usuario->id ?>"><i class="fas fa-shield-alt"></i> ADMIN</a></li>
                                                            <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1315" data-usuario="<?= $usuario->id ?>"><i class="fas fa-user-shield"></i> MOD</a></li>
                                                            <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1316" data-usuario="<?= $usuario->id ?>"><i class="fas fa-user"></i> USER</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger eliminar-usuario" href="#" data-id="<?= $usuario->id ?>"><i class="fas fa-trash"></i> Eliminar</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast para mostrar resultados -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="resultToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Resultado</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
        <div class="toast-body" id="resultMessage"></div>
    </div>
</div>

<style>
/* Estilos para tarjetas de estadísticas */
.stats-card {
    background: linear-gradient(45deg, #00b894, #00cec9);
    color: white;
    border-radius: 12px;
    padding: 1rem;
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.stats-card.bg-danger {
    background: linear-gradient(45deg, #d63031, #ff7675);
}

.stats-card.bg-warning {
    background: linear-gradient(45deg, #fdcb6e, #ffeaa7);
}

.stats-card.bg-info {
    background: linear-gradient(45deg, #0984e3, #74b9ff);
}

.stats-number {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0.25rem 0 0;
}

/* Estilos para la tabla */
.data-container {
    background-color: #fff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

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

.badge {
    padding: 0.5em 0.8em;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 20px;
}

/* Estilos para botones y menús */
.btn-group .btn {
    padding: 0.4rem 0.7rem;
    border-radius: 8px;
}

.dropdown-menu {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
    min-width: 10rem;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 8px;
}

/* Estilos para el modal */
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
    .stats-number {
        font-size: 1.4rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
    }
    
    .btn-group .btn {
        padding: 0.3rem 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para los modales y toast
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const resultToast = new bootstrap.Toast(document.getElementById('resultToast'));
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmButton = document.getElementById('confirmButton');
    const resultMessage = document.getElementById('resultMessage');
    
    // Datos para la acción actual
    let currentAction = '';
    let currentUserId = '';
    let currentRoleId = '';
    
    // Manejar clics en cambiar rol
    document.querySelectorAll('.cambiar-rol').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            currentAction = 'cambiar-rol';
            currentUserId = this.getAttribute('data-usuario');
            currentRoleId = this.getAttribute('data-rol');
            
            // Mostrar mensaje de confirmación
            confirmMessage.innerHTML = `¿Estás seguro de que deseas cambiar el rol del usuario ${currentUserId} a ${getRoleName(currentRoleId)}?`;
            confirmModal.show();
        });
    });
    
    // Manejar clics en eliminar usuario
    document.querySelectorAll('.eliminar-usuario').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            currentAction = 'eliminar-usuario';
            currentUserId = this.getAttribute('data-id');
            
            // Mostrar mensaje de confirmación
            confirmMessage.innerHTML = `¿Estás seguro de que deseas eliminar al usuario ${currentUserId}? Esta acción no se puede deshacer.`;
            confirmModal.show();
        });
    });
    
    // Manejar clic en el botón de confirmación
    confirmButton.addEventListener('click', function() {
        // Ocultar el modal de confirmación
        confirmModal.hide();
        
        if (currentAction === 'cambiar-rol') {
            // Realizar petición AJAX para cambiar el rol
            fetch('<?= $cambiarRolUrl ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?= $csrfToken ?>'
                },
                body: `usuario_id=${currentUserId}&rol_id=${currentRoleId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    resultMessage.innerHTML = data.message || 'Rol cambiado con éxito.';
                    resultToast.show();
                    
                    // Recargar la página después de un breve retraso
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Mostrar mensaje de error
                    resultMessage.innerHTML = data.message || 'Error al cambiar el rol.';
                    resultToast.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultMessage.innerHTML = 'Error al procesar la solicitud.';
                resultToast.show();
            });
        } else if (currentAction === 'eliminar-usuario') {
            // Realizar petición AJAX para eliminar usuario
            fetch('<?= $eliminarUsuarioUrl ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?= $csrfToken ?>'
                },
                body: `usuario_id=${currentUserId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    resultMessage.innerHTML = data.message || 'Usuario eliminado con éxito.';
                    resultToast.show();
                    
                    // Recargar la página después de un breve retraso
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Mostrar mensaje de error
                    resultMessage.innerHTML = data.message || 'Error al eliminar el usuario.';
                    resultToast.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultMessage.innerHTML = 'Error al procesar la solicitud.';
                resultToast.show();
            });
        }
    });
    
    // Función auxiliar para obtener el nombre del rol
    function getRoleName(roleId) {
        switch(roleId) {
            case '1313': return 'SUPERSU';
            case '1314': return 'ADMIN';
            case '1315': return 'MOD';
            case '1316': return 'USER';
            default: return 'Desconocido';
        }
    }
});
</script> 