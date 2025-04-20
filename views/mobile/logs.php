<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'Dashboard de Actividad';

if (Yii::$app->user->isGuest) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['mobile/login']);
}

// URL para la API de logs
$apiUrl = Url::to(['mobile/api-logs']);
?>

<div class="container-fluid py-3">
    <h2 class="mb-3 text-center"><?= Html::encode($this->title) ?></h2>

    <!-- Filtros -->
    <div class="filter-container mb-3">
        <div class="card filter-card">
            <div class="card-body p-2">
                <form id="log-filter-form" class="row g-2">
                    <div class="col-6">
                        <select class="form-select form-select-sm" id="accion-filtro">
                            <option value="">Todas las acciones</option>
                            <option value="login">Inicios de sesión</option>
                            <option value="post">Publicaciones</option>
                            <option value="comment">Comentarios</option>
                            <option value="moderation">Moderación</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-select form-select-sm" id="fecha-filtro">
                            <option value="hoy">Hoy</option>
                            <option value="semana" selected>Esta semana</option>
                            <option value="mes">Este mes</option>
                            <option value="todo">Todo el tiempo</option>
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-3" id="stats-container">
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card">
                <h3 class="stats-number" id="total-logs">--</h3>
                <p class="stats-label">Total logs</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card bg-info">
                <h3 class="stats-number" id="logins-hoy">--</h3>
                <p class="stats-label">Logins hoy</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card bg-success">
                <h3 class="stats-number" id="posts-hoy">--</h3>
                <p class="stats-label">Posts hoy</p>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="stats-card bg-warning">
                <h3 class="stats-number" id="comentarios-hoy">--</h3>
                <p class="stats-label">Comentarios</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Logs -->
    <div class="dashboard-card card mb-4">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-sm logs-table">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="logs-container">
                        <tr>
                            <td colspan="4" class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando logs...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <nav aria-label="Navegación de logs">
        <ul class="pagination pagination-sm justify-content-center" id="pagination-container">
            <!-- Paginación generada por JavaScript -->
        </ul>
    </nav>
    
    <!-- Botón para recargar -->
    <div class="text-center">
        <button class="btn btn-primary mb-4" id="btn-reload">
            <i class="fas fa-sync-alt me-2"></i>Recargar
        </button>
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
    .filter-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        background-color: #f8f9fa;
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
        padding: 0.5rem 0.25rem;
    }
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 20px;
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
    .stats-card.bg-info {
        background: linear-gradient(45deg, #0984e3, #74b9ff);
    }
    .stats-card.bg-success {
        background: linear-gradient(45deg, #00b894, #55efc4);
    }
    .stats-card.bg-warning {
        background: linear-gradient(45deg, #fdcb6e, #ffeaa7);
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
    .logs-table {
        font-size: 0.85rem;
    }
    .logs-table td, .logs-table th {
        padding: 0.5rem 0.25rem;
    }
    .pagination {
        margin-bottom: 1rem;
    }
    .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        color: #6c5ce7;
    }
    .page-item.active .page-link {
        background-color: #6c5ce7;
        border-color: #6c5ce7;
    }
    #btn-reload {
        border-radius: 10px;
        padding: 0.5rem 1.5rem;
        margin-bottom: 3.5rem;
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        border: none;
        box-shadow: 0 4px 10px rgba(108, 92, 231, 0.3);
        transition: all 0.3s ease;
    }
    #btn-reload:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
    }
    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>

<?php
$script = <<<JS
    $(document).ready(function() {
        console.log('Dashboard de logs cargado');
        
        // Variables para paginación
        let currentPage = 0;
        let totalPages = 0;
        let pageSize = 15;
        let accionFiltro = '';
        let fechaFiltro = 'semana';
        
        // Cargar logs desde la API
        function loadLogs() {
            // Mostrar indicador de carga
            $('#logs-container').html(`
                <tr>
                    <td colspan="4" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando logs...</p>
                    </td>
                </tr>
            `);
            
            // Realizar solicitud AJAX
            $.ajax({
                url: '{$apiUrl}',
                type: 'GET',
                data: {
                    accion: accionFiltro,
                    fecha: fechaFiltro,
                    page: currentPage,
                    pageSize: pageSize
                },
                dataType: 'json',
                success: function(response) {
                    // Actualizar variables de paginación
                    totalPages = response.pagination.totalPages;
                    
                    // Actualizar estadísticas
                    $('#total-logs').text(response.stats.total_logs);
                    $('#logins-hoy').text(response.stats.logins_hoy);
                    $('#posts-hoy').text(response.stats.posts_hoy);
                    $('#comentarios-hoy').text(response.stats.comentarios_hoy);
                    
                    // Renderizar logs
                    renderLogs(response.logs);
                    
                    // Renderizar paginación
                    renderPagination();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar logs:', error);
                    $('#logs-container').html(`
                        <tr>
                            <td colspan="4" class="text-center py-3 text-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error al cargar los logs. Por favor, intente de nuevo.
                            </td>
                        </tr>
                    `);
                }
            });
        }
        
        // Renderizar logs en la tabla
        function renderLogs(logs) {
            if (logs.length === 0) {
                $('#logs-container').html(`
                    <tr>
                        <td colspan="4" class="text-center py-3">
                            No hay logs que mostrar
                        </td>
                    </tr>
                `);
                return;
            }
            
            let html = '';
            
            logs.forEach(function(log) {
                const fecha = new Date(log.fecha_hora);
                const fechaFormateada = fecha.toLocaleString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                html += `
                    <tr>
                        <td class="text-nowrap">\${log.ip || '-'}</td>
                        <td class="text-truncate" style="max-width: 80px;">\${log.usuario ? log.usuario.user : 'Anónimo'}</td>
                        <td class="text-truncate" style="max-width: 100px;">\${log.accion || '-'}</td>
                        <td class="text-nowrap">\${fechaFormateada}</td>
                    </tr>
                `;
            });
            
            $('#logs-container').html(html);
        }
        
        // Renderizar controles de paginación
        function renderPagination() {
            if (totalPages <= 1) {
                $('#pagination-container').empty();
                return;
            }
            
            let html = '';
            
            // Botón Anterior
            html += `
                <li class="page-item \${currentPage <= 0 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="\${currentPage - 1}" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `;
            
            // Números de página
            const startPage = Math.max(0, currentPage - 2);
            const endPage = Math.min(startPage + 4, totalPages - 1);
            
            for (let i = startPage; i <= endPage; i++) {
                html += `
                    <li class="page-item \${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="\${i}">\${i + 1}</a>
                    </li>
                `;
            }
            
            // Botón Siguiente
            html += `
                <li class="page-item \${currentPage >= totalPages - 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="\${currentPage + 1}" aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `;
            
            $('#pagination-container').html(html);
            
            // Añadir handlers a los enlaces de paginación
            $('.page-link').on('click', function(e) {
                e.preventDefault();
                
                const page = $(this).data('page');
                if (page !== undefined && page >= 0 && page < totalPages) {
                    currentPage = page;
                    loadLogs();
                }
            });
        }
        
        // Manejar envío del formulario de filtro
        $('#log-filter-form').on('submit', function(e) {
            e.preventDefault();
            
            accionFiltro = $('#accion-filtro').val();
            fechaFiltro = $('#fecha-filtro').val();
            currentPage = 0; // Volver a la primera página
            
            loadLogs();
        });
        
        // Manejar clic en botón de recarga
        $('#btn-reload').on('click', function() {
            loadLogs();
        });
        
        // Verificar si hay filtros en la URL y aplicarlos
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('accion')) {
            accionFiltro = urlParams.get('accion');
            $('#accion-filtro').val(accionFiltro);
        }
        
        if (urlParams.has('fecha')) {
            fechaFiltro = urlParams.get('fecha');
            $('#fecha-filtro').val(fechaFiltro);
        }
        
        if (urlParams.has('page')) {
            currentPage = parseInt(urlParams.get('page'));
        }
        
        // Cargar los logs al iniciar
        loadLogs();
    });
JS;
$this->registerJs($script);
?> 