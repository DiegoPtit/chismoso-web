<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administración de Usuarios';

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;
?>

<style>
    .dashboard-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-header {
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 1.5rem;
    }
    .card-header h6 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .card-body {
        padding: 1.5rem;
    }
    .table {
        margin: 0;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #6c5ce7;
        background-color: #f8f9fa;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        padding: 0.5em 1em;
        border-radius: 20px;
        font-weight: 500;
    }
    .btn-group .btn {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-group .btn:hover {
        transform: translateY(-2px);
    }
    .dropdown-menu {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .modal-header {
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }
    .btn-close {
        filter: brightness(0) invert(1);
    }
    .toast {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .toast-header {
        border-radius: 15px 15px 0 0;
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        color: white;
    }
    .toast-header .btn-close {
        filter: brightness(0) invert(1);
    }
    .stats-card {
        background: linear-gradient(45deg, #00b894, #00cec9);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0.5rem 0;
    }
    .stats-detail {
        font-size: 0.8rem;
        opacity: 0.8;
        margin: 0;
        display: block;
    }
    @media (max-width: 767px) {
        .stats-card {
            padding: 1rem;
        }
        .stats-number {
            font-size: 1.5rem;
        }
        .stats-label {
            font-size: 0.8rem;
        }
        .stats-detail {
            font-size: 0.7rem;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <h6>Administración de Usuarios</h6>
                </div>
                <div class="card-body">
                    <!-- Estadísticas Rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number"><?= count($usuarios) ?></h3>
                                <p class="stats-label">Total de Usuarios</p>
                                <small class="stats-detail">Registrados en el sistema</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1313)) ?></h3>
                                <p class="stats-label">SUPERSU</p>
                                <small class="stats-detail">Administradores principales</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1314)) ?></h3>
                                <p class="stats-label">ADMIN</p>
                                <small class="stats-detail">Administradores</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number"><?= count(array_filter($usuarios, fn($u) => $u->rol_id == 1315)) ?></h3>
                                <p class="stats-label">MOD</p>
                                <small class="stats-detail">Moderadores</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Rol Actual</th>
                                    <th>Fecha de Registro</th>
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
                                                    $rolText = 'DESCONOCIDO';
                                            }
                                            ?>
                                            <span class="badge <?= $rolClass ?>"><?= $rolText ?></span>
                                        </td>
                                        <td><?= Html::encode($usuario->created_at) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-user-cog"></i> Cambiar Rol
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1313"><i class="fas fa-crown"></i> SUPERSU</a></li>
                                                    <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1314"><i class="fas fa-shield-alt"></i> ADMIN</a></li>
                                                    <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1315"><i class="fas fa-user-shield"></i> MOD</a></li>
                                                    <li><a class="dropdown-item cambiar-rol" href="#" data-rol="1316"><i class="fas fa-user"></i> USER</a></li>
                                                </ul>
                                                <button class="btn btn-danger btn-sm eliminar-usuario" data-id="<?= $usuario->id ?>">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
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

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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

<?php
$script = <<<JS
    // Variables globales
    let confirmModal;
    let resultToast;
    let currentUsuarioId = null;
    let currentRolId = null;

    // Inicialización
    $(document).ready(function() {
        confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        resultToast = new bootstrap.Toast(document.getElementById('resultToast'));

        // Manejar clic en cambiar rol
        $('.cambiar-rol').on('click', function(e) {
            e.preventDefault();
            currentUsuarioId = $(this).closest('tr').find('td:first').text();
            currentRolId = $(this).data('rol');
            
            $('#confirmMessage').text('¿Estás seguro de que deseas cambiar el rol de este usuario?');
            $('#confirmButton').off('click').on('click', function() {
                cambiarRol();
            });
            
            confirmModal.show();
        });

        // Manejar clic en eliminar usuario
        $('.eliminar-usuario').on('click', function(e) {
            e.preventDefault();
            currentUsuarioId = $(this).data('id');
            
            $('#confirmMessage').text('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');
            $('#confirmButton').off('click').on('click', function() {
                eliminarUsuario();
            });
            
            confirmModal.show();
        });
    });

    // Función para cambiar rol
    function cambiarRol() {
        $.ajax({
            url: 'index.php?r=site/cambiar-rol',
            type: 'POST',
            data: {
                usuario_id: currentUsuarioId,
                rol_id: currentRolId,
                _csrf: '$csrfToken'
            },
            success: function(response) {
                confirmModal.hide();
                showResult(response.message, response.success);
                if (response.success) {
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function() {
                confirmModal.hide();
                showResult('Error al procesar la solicitud', false);
            }
        });
    }

    // Función para eliminar usuario
    function eliminarUsuario() {
        $.ajax({
            url: 'index.php?r=site/eliminar-usuario',
            type: 'POST',
            data: {
                usuario_id: currentUsuarioId,
                _csrf: '$csrfToken'
            },
            success: function(response) {
                confirmModal.hide();
                showResult(response.message, response.success);
                if (response.success) {
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function() {
                confirmModal.hide();
                showResult('Error al procesar la solicitud', false);
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
JS;
$this->registerJs($script);
?> 