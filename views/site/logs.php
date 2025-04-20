<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard de Actividad';

// Verificar que el usuario está autenticado y tiene rol adecuado
if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['site/index']);
}

// URL para la API de logs
$apiUrl = Url::to(['site/api-logs']);
?>

<div class="site-logs">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-chart-line"></i> Dashboard de Actividad</h1>
        <p class="lead text-center mb-4">Monitorea la actividad de la plataforma</p>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="forum-container">
                    <div class="forum-post">
                        <!-- Filtros -->
                        <div class="filter-section mb-4">
                            <h4 class="mb-3">Filtros</h4>
                            <form id="log-filter-form" class="row g-3">
                                <div class="col-md-6">
                                    <select class="form-select" id="accion-filtro">
                                        <option value="">Todas las acciones</option>
                                        <option value="login">Inicios de sesión</option>
                                        <option value="post">Publicaciones</option>
                                        <option value="comment">Comentarios</option>
                                        <option value="moderation">Moderación</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select" id="fecha-filtro">
                                        <option value="hoy">Hoy</option>
                                        <option value="semana" selected>Esta semana</option>
                                        <option value="mes">Este mes</option>
                                        <option value="todo">Todo el tiempo</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-2"></i> Filtrar
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Estadísticas Rápidas -->
                        <div class="stats-section mb-4">
                            <h4 class="mb-3">Estadísticas</h4>
                            <div class="row">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card">
                                        <div class="stats-icon">
                                            <i class="fas fa-list"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number" id="total-logs">--</span>
                                            <span class="stats-label">Total logs</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-info">
                                        <div class="stats-icon">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number" id="logins-hoy">--</span>
                                            <span class="stats-label">Logins hoy</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-success">
                                        <div class="stats-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number" id="posts-hoy">--</span>
                                            <span class="stats-label">Posts hoy</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-warning">
                                        <div class="stats-icon">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number" id="comentarios-hoy">--</span>
                                            <span class="stats-label">Comentarios</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Logs -->
                        <div class="logs-section mb-4">
                            <h4 class="mb-3">Registros de Actividad</h4>
                            <div class="table-responsive">
                                <table class="table table-hover">
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
                                            <td colspan="4" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="mt-2">Cargando logs...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Paginación -->
                            <nav aria-label="Navegación de logs" class="mt-3">
                                <ul class="pagination justify-content-center" id="pagination-container">
                                    <!-- Paginación generada por JavaScript -->
                                </ul>
                            </nav>
                            
                            <!-- Botón para recargar -->
                            <div class="text-center mt-3">
                                <button class="btn btn-primary" id="btn-reload">
                                    <i class="fas fa-sync-alt me-2"></i> Recargar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para secciones */
.filter-section,
.stats-section,
.logs-section {
    background-color: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

/* Estilos para tarjetas de estadísticas */
.stats-card {
    background: linear-gradient(45deg, #00b894, #00cec9);
    color: white;
    border-radius: 12px;
    padding: 1rem;
    height: 100%;
    display: flex;
    align-items: center;
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

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background-color: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stats-icon i {
    font-size: 1.5rem;
    color: white;
}

.stats-info {
    flex: 1;
}

.stats-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
}

.stats-label {
    font-size: 0.85rem;
    opacity: 0.9;
}

/* Estilos para la tabla */
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

/* Paginación */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #6c5ce7;
    border: none;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 8px;
}

.page-link:hover {
    background-color: #f8f9fa;
    color: #5f4dd0;
}

.page-item.active .page-link {
    background-color: #6c5ce7;
    color: white;
}

/* Formularios */
.form-select {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    border: 1px solid #ced4da;
    font-size: 0.9rem;
}

.form-select:focus {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
}

.btn-primary {
    background-color: #6c5ce7;
    border-color: #6c5ce7;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #5f4dd0;
    border-color: #5f4dd0;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Responsivo */
@media (max-width: 768px) {
    .stats-number {
        font-size: 1.2rem;
    }
    
    .filter-section,
    .stats-section,
    .logs-section {
        padding: 1rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
    }
    
    .page-link {
        padding: 0.4rem 0.6rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const logFilterForm = document.getElementById('log-filter-form');
    const accionFiltro = document.getElementById('accion-filtro');
    const fechaFiltro = document.getElementById('fecha-filtro');
    const logsContainer = document.getElementById('logs-container');
    const paginationContainer = document.getElementById('pagination-container');
    const btnReload = document.getElementById('btn-reload');
    
    // Estadísticas
    const totalLogs = document.getElementById('total-logs');
    const loginsHoy = document.getElementById('logins-hoy');
    const postsHoy = document.getElementById('posts-hoy');
    const comentariosHoy = document.getElementById('comentarios-hoy');
    
    // Variables de control
    let currentPage = 1;
    const itemsPerPage = 25;
    
    // Cargar logs iniciales
    loadLogs();
    
    // Evento de filtro
    logFilterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadLogs();
    });
    
    // Evento de recarga
    btnReload.addEventListener('click', function() {
        loadLogs();
    });
    
    // Función para cargar logs
    function loadLogs() {
        showLoading();
        
        const params = new URLSearchParams({
            accion: accionFiltro.value,
            fecha: fechaFiltro.value,
            page: currentPage,
            items_per_page: itemsPerPage
        });
        
        fetch('<?= $apiUrl ?>?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderStats(data.stats);
                    renderLogs(data.logs);
                    renderPagination(data.total, data.page);
                } else {
                    showError(data.message || 'Error al cargar los logs');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error de conexión al cargar los logs');
            });
    }
    
    // Renderizar estadísticas
    function renderStats(stats) {
        totalLogs.textContent = stats.total || 0;
        loginsHoy.textContent = stats.logins_hoy || 0;
        postsHoy.textContent = stats.posts_hoy || 0;
        comentariosHoy.textContent = stats.comentarios_hoy || 0;
    }
    
    // Renderizar logs
    function renderLogs(logs) {
        if (!logs || logs.length === 0) {
            logsContainer.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay registros que coincidan con los filtros seleccionados
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        logs.forEach(log => {
            // Determinar clase según el tipo de acción
            let badgeClass = 'bg-secondary';
            switch(true) {
                case /login|iniciar sesión/i.test(log.accion):
                    badgeClass = 'bg-info';
                    break;
                case /post|publicar/i.test(log.accion):
                    badgeClass = 'bg-success';
                    break;
                case /comentario|comentar/i.test(log.accion):
                    badgeClass = 'bg-warning';
                    break;
                case /moderar|ban|eliminar/i.test(log.accion):
                    badgeClass = 'bg-danger';
                    break;
            }
            
            html += `
                <tr>
                    <td><span class="text-monospace">${log.ip || '-'}</span></td>
                    <td>${log.usuario || 'Anónimo'}</td>
                    <td><span class="badge ${badgeClass}">${log.accion}</span></td>
                    <td>${formatDate(log.fecha)}</td>
                </tr>
            `;
        });
        
        logsContainer.innerHTML = html;
    }
    
    // Renderizar paginación
    function renderPagination(total, currentPage) {
        const totalPages = Math.ceil(total / itemsPerPage);
        
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let html = '';
        
        // Botón anterior
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;
        
        // Páginas
        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        
        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }
        
        if (startPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
                ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
            `;
        }
        
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        if (endPage < totalPages) {
            html += `
                ${endPage < totalPages - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `;
        }
        
        // Botón siguiente
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;
        
        paginationContainer.innerHTML = html;
        
        // Agregar eventos a los enlaces de paginación
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (this.parentNode.classList.contains('disabled')) {
                    return;
                }
                
                const page = parseInt(this.getAttribute('data-page'));
                if (page !== currentPage) {
                    currentPage = page;
                    loadLogs();
                    
                    // Scroll hacia arriba
                    window.scrollTo({
                        top: document.querySelector('.logs-section').offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    // Mostrar mensaje de carga
    function showLoading() {
        logsContainer.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando logs...</p>
                </td>
            </tr>
        `;
    }
    
    // Mostrar mensaje de error
    function showError(message) {
        logsContainer.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                </td>
            </tr>
        `;
    }
    
    // Formatear fecha
    function formatDate(dateString) {
        const date = new Date(dateString);
        
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }
});
</script> 