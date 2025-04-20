<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administración de Usuarios';

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;

// Generar las URLs antes del script
$cambiarRolUrl = Url::to(['mobile/cambiar-rol']);
$eliminarUsuarioUrl = Url::to(['mobile/eliminar-usuario']);
?>

<div class="container-fluid py-3">
    <h2 class="mb-3 text-center"><?= Html::encode($this->title) ?></h2>
    
    <!-- Estadísticas Rápidas -->
    <div class="row mb-3">
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card">
                <h3 class="stats-number"><?= count($usuarios) ?></h3>
                <p class="stats-label">Total</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card">
                <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1313)) ?></h3>
                <p class="stats-label">SUPERSU</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card">
                <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1314)) ?></h3>
                <p class="stats-label">ADMIN</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card">
                <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1315)) ?></h3>
                <p class="stats-label">MOD</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="dashboard-card card mb-4">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-sm">
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmButton">Confirmar</button>
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
    .dashboard-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .table {
        margin-bottom: 0;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #6c5ce7;
        border-top: none;
        font-size: 0.85rem;
    }
    .table td {
        font-size: 0.85rem;
        vertical-align: middle;
        padding: 0.5rem;
    }
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 20px;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .dropdown-menu {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 0.5rem 0;
        min-width: 10rem;
        font-size: 0.85rem;
    }
    .dropdown-item {
        padding: 0.4rem 1rem;
    }
    .dropdown-item i {
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }
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
    .stats-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .stats-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin: 0.25rem 0 0;
    }
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    .modal-header {
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1rem;
    }
    .modal-header .modal-title {
        font-size: 1rem;
    }
    .modal-body {
        padding: 1rem;
        font-size: 0.9rem;
    }
    .modal-footer {
        padding: 0.75rem 1rem;
    }
    .toast-container {
        bottom: 70px !important;
        z-index: 1100;
    }
    .toast {
        width: 280px;
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .toast-header {
        background-color: #6c5ce7;
        color: white;
        padding: 0.5rem 1rem;
    }
    .toast-body {
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }
</style>

<?php
$script = <<<JS
    $(document).ready(function() {
        console.log('Documento listo, inicializando...');
        
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const resultToast = new bootstrap.Toast(document.getElementById('resultToast'));
        let currentUsuarioId = null;
        let currentRolId = null;

        // Manejar clic en cambiar rol
        $(document).on('click', '.cambiar-rol', function(e) {
            e.preventDefault();
            currentUsuarioId = $(this).data('usuario');
            currentRolId = $(this).data('rol');
            
            console.log('Usuario ID:', currentUsuarioId);
            console.log('Rol ID:', currentRolId);
            
            $('#confirmMessage').text('¿Estás seguro de que deseas cambiar el rol de este usuario?');
            $('#confirmButton').off('click').on('click', function() {
                cambiarRol();
            });
            
            confirmModal.show();
        });

        // Manejar clic en eliminar usuario
        $(document).on('click', '.eliminar-usuario', function(e) {
            e.preventDefault();
            currentUsuarioId = $(this).data('id');
            
            console.log('Usuario ID a eliminar:', currentUsuarioId);
            
            $('#confirmMessage').text('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');
            $('#confirmButton').off('click').on('click', function() {
                eliminarUsuario();
            });
            
            confirmModal.show();
        });

        // Limpiar modales y backdrops al cargar la página
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        
        // Asegurar que los modales sean clickeables
        $('.modal').css('pointer-events', 'auto');
        $('.modal .modal-content').css('pointer-events', 'auto');
        
        // Manejar la apertura de modales
        $('.modal').on('show.bs.modal', function() {
            $('.modal-backdrop').remove();
            
            $('<div>')
                .addClass('modal-backdrop fade show')
                .css({
                    'z-index': '1040',
                    'background-color': 'transparent',
                    'pointer-events': 'none'
                })
                .appendTo('body');
        });
        
        // Manejar el cierre de modales
        $('.modal').on('hidden.bs.modal', function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });

        // Función para cambiar rol
        function cambiarRol() {
            console.log('Iniciando cambio de rol...');
            
            $.ajax({
                url: '{$cambiarRolUrl}',
                type: 'POST',
                data: {
                    usuario_id: currentUsuarioId,
                    rol_id: currentRolId,
                    _csrf: '$csrfToken'
                },
                success: function(response) {
                    console.log('Respuesta:', response);
                    confirmModal.hide();
                    showResult(response.message, response.success);
                    if (response.success) {
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    confirmModal.hide();
                    showResult('Error al procesar la solicitud: ' + error, false);
                }
            });
        }

        // Función para eliminar usuario
        function eliminarUsuario() {
            console.log('Iniciando eliminación de usuario...');
            
            $.ajax({
                url: '{$eliminarUsuarioUrl}',
                type: 'POST',
                data: {
                    usuario_id: currentUsuarioId,
                    _csrf: '$csrfToken'
                },
                success: function(response) {
                    console.log('Respuesta:', response);
                    confirmModal.hide();
                    showResult(response.message, response.success);
                    if (response.success) {
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    confirmModal.hide();
                    showResult('Error al procesar la solicitud: ' + error, false);
                }
            });
        }

        // Función para mostrar resultados
        function showResult(message, success) {
            $('#resultMessage').text(message);
            $('#resultToast').removeClass('bg-success bg-danger text-white')
                .addClass(success ? 'bg-success text-white' : 'bg-danger text-white');
            resultToast.show();
        }
    });
JS;
$this->registerJs($script);
?> 