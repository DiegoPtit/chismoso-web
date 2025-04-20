<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Gestión de Contenido';

// Almacenar el token CSRF en una variable PHP
$csrfToken = Yii::$app->request->csrfToken;
?>

<div class="container-fluid py-3">
    <h2 class="mb-3 text-center"><?= Html::encode($this->title) ?></h2>
    
    <!-- Tabs para navegación -->
    <ul class="nav nav-pills mb-3" id="contentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="true">
                <i class="fas fa-file-alt"></i> Posts Baneados
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="false">
                <i class="fas fa-users"></i> Usuarios Baneados
            </button>
        </li>
    </ul>

    <!-- Contenido de tabs -->
    <div class="tab-content" id="contentTabsContent">
        <!-- Tab de Posts Baneados -->
        <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
            <div class="dashboard-card card mb-4">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Contenido</th>
                                    <th>Razón</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($postsBaneados as $bannedPost): ?>
                                    <tr>
                                        <td><?= Html::encode($bannedPost->post_id) ?></td>
                                        <td class="text-truncate" style="max-width: 120px;"><?= Html::encode($bannedPost->post->contenido) ?></td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?= Html::encode($motivos[$bannedPost->motivo]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-success btn-sm desbloquear-post" 
                                                    data-id="<?= $bannedPost->id ?>">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($postsBaneados)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No hay posts baneados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab de Usuarios Baneados -->
        <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
            <div class="dashboard-card card mb-4">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm">
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
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($usuariosBaneados)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-3">No hay usuarios baneados</td>
                                    </tr>
                                <?php endif; ?>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">¿Está seguro de que desea desbloquear este elemento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm" id="confirmButton">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de resultado -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="resultMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
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
    .card-header {
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 1rem;
    }
    .card-header h6 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }
    .card-body {
        padding: 1rem;
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
    }
    .btn-sm {
        padding: .25rem .5rem;
        font-size: .75rem;
        border-radius: 6px;
    }
    .badge {
        padding: .3em .6em;
        font-weight: 500;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    .nav-pills {
        display: flex;
        justify-content: center;
        border-radius: 15px;
        background: #f8f9fa;
        padding: 0.25rem;
        margin-bottom: 1rem;
    }
    .nav-pills .nav-link {
        border-radius: 12px;
        padding: 0.5rem 1rem;
        margin: 0.25rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: #495057;
    }
    .nav-pills .nav-link.active {
        background-color: #6c5ce7;
        color: white;
    }
    .nav-pills .nav-link i {
        margin-right: 5px;
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
</style>

<?php
$script = <<<JS
$(document).ready(function() {
    console.log('Documento listo');
    
    // Obtener el token CSRF
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('Token CSRF encontrado:', csrfToken ? 'Sí' : 'No');
    
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
            
            const url = '/mobile/desbloquear-post';
            console.log('URL de desbloqueo de post:', url);
            
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    _csrf: csrfToken
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
                    showResult('Error al procesar la solicitud: ' + error, false);
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
            
            const url = '/mobile/desbloquear-usuario';
            console.log('URL de desbloqueo de usuario:', url);
            
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    _csrf: csrfToken
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
                    showResult('Error al procesar la solicitud: ' + error, false);
                }
            });
        });
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
});
JS;
$this->registerJs($script);
?> 