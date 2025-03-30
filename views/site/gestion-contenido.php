<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Gestión de Contenido';

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Botón de toggle para móvil -->
        <div class="col-12 d-md-none mb-3">
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
                <i class="fas fa-bars"></i> Menú
            </button>
        </div>

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#posts" data-bs-toggle="tab">
                            <i class="fas fa-file-alt"></i> Posts Baneados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#usuarios" data-bs-toggle="tab">
                            <i class="fas fa-users"></i> Usuarios Baneados
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="tab-content">
                <!-- Tab de Posts Baneados -->
                <div class="tab-pane fade show active" id="posts">
                    <div class="dashboard-card card">
                        <div class="card-header">
                            <h6>Posts Baneados</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Contenido</th>
                                            <th>Usuario</th>
                                            <th>Razón</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($postsBaneados as $bannedPost): ?>
                                            <tr>
                                                <td><?= Html::encode($bannedPost->post_id) ?></td>
                                                <td><?= Html::encode($bannedPost->post->contenido) ?></td>
                                                <td><?= Html::encode($bannedPost->post->usuario->user) ?></td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        <?= Html::encode($motivos[$bannedPost->motivo]) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-sm desbloquear-post" 
                                                            data-id="<?= $bannedPost->id ?>">
                                                        <i class="fas fa-unlock"></i> Desbloquear
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab de Usuarios Baneados -->
                <div class="tab-pane fade" id="usuarios">
                    <div class="dashboard-card card">
                        <div class="card-header">
                            <h6>Usuarios Baneados</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Rol</th>
                                            <th>Usuario</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuariosBaneados as $bannedUser): ?>
                                            <tr>
                                                <td><?= Html::encode($bannedUser->usuario_id) ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= Html::encode($bannedUser->usuario->rol_id) ?>
                                                    </span>
                                                </td>
                                                <td><?= Html::encode($bannedUser->usuario->user) ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm desbloquear-usuario" 
                                                            data-id="<?= $bannedUser->id ?>">
                                                        <i class="fas fa-unlock"></i> Desbloquear
                                                    </button>
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
        </main>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Está seguro de que desea desbloquear este elemento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de resultado -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="resultMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<<JS
$(document).ready(function() {
    console.log('Documento listo');
    
    // Inicializar modales
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
    
    // Función para mostrar mensaje en el modal de resultado
    function showResult(message, isSuccess) {
        const modal = document.getElementById('resultModal');
        const header = modal.querySelector('.modal-header');
        const messageEl = document.getElementById('resultMessage');
        
        header.className = 'modal-header ' + (isSuccess ? 'bg-success' : 'bg-danger');
        header.querySelector('.modal-title').className = 'modal-title text-white';
        messageEl.textContent = message;
        messageEl.className = isSuccess ? 'text-success' : 'text-danger';
        
        resultModal.show();
    }
    
    // Manejar clic en botón de desbloquear post
    $(document).on('click', '.desbloquear-post', function(e) {
        console.log('Click en desbloquear post');
        e.preventDefault();
        const id = $(this).data('id');
        console.log('ID del post:', id);
        
        // Mostrar modal de confirmación
        confirmModal.show();
        
        // Manejar confirmación
        $('#confirmButton').off('click').on('click', function() {
            confirmModal.hide();
            
            $.ajax({
                url: 'index.php?r=site/desbloquear-post',
                type: 'POST',
                data: {
                    id: id,
                    _csrf: '$csrfToken'
                },
                success: function(response) {
                    console.log('Respuesta:', response);
                    if (response.success) {
                        showResult(response.message, true);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showResult(response.message, false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Estado:', status);
                    console.error('Respuesta:', xhr.responseText);
                    showResult('Error al procesar la solicitud', false);
                }
            });
        });
    });

    // Manejar clic en botón de desbloquear usuario
    $(document).on('click', '.desbloquear-usuario', function(e) {
        console.log('Click en desbloquear usuario');
        e.preventDefault();
        const id = $(this).data('id');
        console.log('ID del usuario:', id);
        
        // Mostrar modal de confirmación
        confirmModal.show();
        
        // Manejar confirmación
        $('#confirmButton').off('click').on('click', function() {
            confirmModal.hide();
            
            $.ajax({
                url: 'index.php?r=site/desbloquear-usuario',
                type: 'POST',
                data: {
                    id: id,
                    _csrf: '$csrfToken'
                },
                success: function(response) {
                    console.log('Respuesta:', response);
                    if (response.success) {
                        showResult(response.message, true);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showResult(response.message, false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Estado:', status);
                    console.error('Respuesta:', xhr.responseText);
                    showResult('Error al procesar la solicitud', false);
                }
            });
        });
    });

    // Manejar el cierre del sidebar en móvil al hacer clic en un enlace
    $('.sidebar .nav-link').on('click', function() {
        if (window.innerWidth < 768) {
            $('.sidebar').removeClass('show');
            $('.sidebar-backdrop').removeClass('show');
        }
    });

    // Manejar el cierre del sidebar al hacer clic en el backdrop
    $('.sidebar-backdrop').on('click', function() {
        $('.sidebar').removeClass('show');
        $(this).removeClass('show');
    });

    // Manejar el toggle del sidebar
    $('[data-bs-toggle="collapse"]').on('click', function() {
        if (window.innerWidth < 768) {
            $('.sidebar').toggleClass('show');
            $('.sidebar-backdrop').toggleClass('show');
        }
    });

    // Ajustar el sidebar en el cambio de tamaño de ventana
    $(window).on('resize', function() {
        if (window.innerWidth >= 768) {
            $('.sidebar').removeClass('show');
            $('.sidebar-backdrop').removeClass('show');
        }
    });
});
JS;
$this->registerJs($script);
?>

<style>
    .dashboard-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        margin-bottom: 1.5rem;
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
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 1000;
        padding: 120px 0 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background: white;
        transition: all 0.3s ease;
    }
    .sidebar .nav-link {
        font-weight: 500;
        color: #333;
        padding: .75rem 1rem;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 0.5rem 1rem;
    }
    .sidebar .nav-link.active {
        color: #6c5ce7;
        background-color: rgba(108, 92, 231, 0.1);
    }
    .sidebar .nav-link:hover {
        color: #6c5ce7;
        background-color: rgba(108, 92, 231, 0.05);
    }
    .sidebar .nav-link i {
        margin-right: 8px;
        width: 20px;
        text-align: center;
        color: #6c5ce7;
    }
    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #6c5ce7;
        border-top: none;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-sm {
        padding: .4rem .8rem;
        font-size: .875rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .badge {
        padding: .5em .8em;
        font-weight: 500;
        border-radius: 20px;
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
    .modal-footer {
        border-radius: 0 0 15px 15px;
    }
    .btn-close {
        filter: brightness(0) invert(1);
    }
    .text-primary {
        color: #6c5ce7 !important;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: .75rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        color: #6c5ce7;
        border-bottom: 2px solid #6c5ce7;
    }
    @media (max-width: 767px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            bottom: 0;
            width: 80%;
            max-width: 300px;
            z-index: 1050;
            transition: left 0.3s ease;
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar.show {
            left: 0;
        }

        /* Overlay para el fondo oscuro cuando el sidebar está abierto */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }

        .sidebar-backdrop.show {
            display: block;
        }

        /* Ajuste del contenido principal */
        main {
            width: 100%;
            margin-left: 0 !important;
        }

        /* Botón de toggle */
        .btn-toggle-sidebar {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1060;
            background: #6c5ce7;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .btn-toggle-sidebar:hover {
            background: #5b4cc4;
            transform: translateY(-1px);
        }

        /* Ajuste del padding del contenido principal */
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }

    /* Ajustes para pantallas más grandes */
    @media (min-width: 768px) {
        .sidebar {
            position: fixed;
            width: 16.666667%;
        }

        main {
            margin-left: 16.666667%;
        }
    }
</style> 