<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Administración de Usuarios';

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;

// Generar las URLs antes del script
$cambiarRolUrl = Url::to(['site/cambiar-rol']);
$eliminarUsuarioUrl = Url::to(['site/eliminar-usuario']);
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

    /* Estilos base para el modal y backdrop */
    .modal-backdrop {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: transparent !important;
        z-index: 1040 !important;
        pointer-events: none !important;
    }

    .modal {
        z-index: 1050 !important;
        padding-top: 80px !important;
    }

    .modal-dialog {
        z-index: 1051 !important;
    }

    .modal-content {
        z-index: 1052 !important;
        position: relative;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .modal-backdrop.show {
        pointer-events: none !important;
        background-color: transparent !important;
    }

    .modal, .modal-dialog, .modal-content {
        pointer-events: auto !important;
    }

    .modal.fade .modal-dialog {
        transform: scale(0.8);
        transition: transform 0.3s ease-in-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }

    @media (max-width: 767px) {
        .modal {
            padding-top: 60px !important;
        }
        
        .modal-dialog {
            margin: 1rem;
            max-width: calc(100% - 2rem);
        }
        
        .modal-body {
            padding: 1rem;
            max-height: 80vh;
        }
        
        .modal-header,
        .modal-footer {
            padding: 1rem;
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
    // Verificar que jQuery esté cargado
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está cargado!');
    } else {
        console.log('jQuery está cargado correctamente');
    }

    // Variables globales
    let confirmModal;
    let resultToast;
    let currentUsuarioId = null;
    let currentRolId = null;

    // URLs para las acciones
    const cambiarRolUrl = '$cambiarRolUrl';
    const eliminarUsuarioUrl = '$eliminarUsuarioUrl';

    // Inicialización
    $(document).ready(function() {
        console.log('Documento listo, inicializando...');
        
        confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        resultToast = new bootstrap.Toast(document.getElementById('resultToast'));

        // Manejar clic en cambiar rol
        $(document).on('click', '.cambiar-rol', function(e) {
            console.log('Click en cambiar rol detectado');
            e.preventDefault();
            currentUsuarioId = $(this).closest('tr').find('td:first').text();
            currentRolId = $(this).data('rol');
            
            console.log('Usuario ID:', currentUsuarioId);
            console.log('Rol ID:', currentRolId);
            
            $('#confirmMessage').text('¿Estás seguro de que deseas cambiar el rol de este usuario?');
            $('#confirmButton').off('click').on('click', function() {
                console.log('Botón confirmar clickeado');
                cambiarRol();
            });
            
            confirmModal.show();
        });

        // Manejar clic en eliminar usuario
        $(document).on('click', '.eliminar-usuario', function(e) {
            console.log('Click en eliminar usuario detectado');
            e.preventDefault();
            currentUsuarioId = $(this).data('id');
            
            console.log('Usuario ID a eliminar:', currentUsuarioId);
            
            $('#confirmMessage').text('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');
            $('#confirmButton').off('click').on('click', function() {
                console.log('Botón confirmar clickeado');
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
        
        // Manejar clics en los modales
        $('.modal').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Manejar clics en el contenido de los modales
        $('.modal .modal-content').on('click', function(e) {
            e.stopPropagation();
        });
        
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
    });

    // Función para cambiar rol
    function cambiarRol() {
        console.log('Iniciando cambio de rol...');
        console.log('Usuario ID:', currentUsuarioId);
        console.log('Rol ID:', currentRolId);
        console.log('URL:', cambiarRolUrl);
        
        $.ajax({
            url: cambiarRolUrl,
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
                console.log('Status:', status);
                console.log('XHR:', xhr);
                confirmModal.hide();
                showResult('Error al procesar la solicitud: ' + error, false);
            }
        });
    }

    // Función para eliminar usuario
    function eliminarUsuario() {
        console.log('Iniciando eliminación de usuario...');
        console.log('Usuario ID:', currentUsuarioId);
        console.log('URL:', eliminarUsuarioUrl);
        
        $.ajax({
            url: eliminarUsuarioUrl,
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
                console.log('Status:', status);
                console.log('XHR:', xhr);
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
JS;
$this->registerJs($script);
?> 