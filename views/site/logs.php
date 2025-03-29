<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'Dashboard de Actividad';

if (Yii::$app->user->isGuest) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['site/login']);
}
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
    .stats-card {
        background: linear-gradient(45deg, #00b894, #00cec9);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .table {
        margin: 0;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #6c5ce7;
    }
    .badge {
        padding: 0.5em 1em;
        border-radius: 20px;
        font-weight: 500;
    }
    .pagination {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }
    .page-item {
        margin: 0 2px;
    }
    .page-link {
        padding: 8px 12px;
        border-radius: 20px;
        color: #6c5ce7;
        background: white;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .page-item.active .page-link {
        background: #6c5ce7;
        color: white;
        border-color: #6c5ce7;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    .page-link:hover {
        background: #f8f9fa;
        color: #6c5ce7;
        border-color: #6c5ce7;
    }
    .page-item.active .page-link:hover {
        background: #6c5ce7;
        color: white;
        border-color: #6c5ce7;
    }
    #logsMap {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 400px;
    }
    .modal-content {
        border-radius: 15px;
        border: none;
    }
    .modal-header {
        background: linear-gradient(45deg, #6c5ce7, #a8a4e6);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .btn-close {
        filter: brightness(0) invert(1);
    }
    .leaflet-popup-content-wrapper {
        border-radius: 15px;
        background: white;
    }
    .leaflet-popup-tip {
        background: white;
    }
    .leaflet-control-zoom {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .leaflet-control-zoom-in,
    .leaflet-control-zoom-out {
        background: white;
        color: #6c5ce7;
        width: 30px;
        height: 30px;
        line-height: 30px;
    }
    .leaflet-control-zoom-in:hover,
    .leaflet-control-zoom-out:hover {
        background: #f8f9fa;
    }
    .custom-popup .leaflet-popup-content-wrapper {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .custom-popup .leaflet-popup-tip {
        background: rgba(255, 255, 255, 0.95);
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card card mb-4">
                <div class="card-header">
                    <h6>Dashboard de Actividad</h6>
                </div>
                <div class="card-body">
                    <!-- Estadísticas Rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number" id="totalAcciones">0</h3>
                                <p class="stats-label">Total de Acciones</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number" id="usuariosActivos">0</h3>
                                <p class="stats-label">Usuarios Activos</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number" id="paisesUnicos">0</h3>
                                <p class="stats-label">Países Únicos</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <h3 class="stats-number" id="accionesHoy">0</h3>
                                <p class="stats-label">Acciones Hoy</p>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <!-- Gráfico de Distribución por País -->
                        <div class="col-xl-6 mb-4">
                            <div class="dashboard-card card">
                                <div class="card-header">
                                    <h6>Distribución por País</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChartUbicacion" height="300"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Actividad por Usuario -->
                        <div class="col-xl-6 mb-4">
                            <div class="dashboard-card card">
                                <div class="card-header">
                                    <h6>Actividad por Usuario</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="pieChartUsuario" height="300"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Useragents -->
                        <div class="col-xl-12 mb-4">
                            <div class="dashboard-card card">
                                <div class="card-header">
                                    <h6>Distribución de Useragents</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="barChartUseragent" height="300"></canvas>
                                </div>
        </div>
    </div>

                        <!-- Mapa de Ubicaciones -->
                        <div class="col-xl-12 mb-4">
                            <div class="dashboard-card card">
                                <div class="card-header">
                                    <h6>Mapa de Actividad Global</h6>
                                </div>
                                <div class="card-body">
                                    <div id="logsMap" style="height: 400px;"></div>
                                </div>
        </div>
    </div>

                        <!-- Tabla de Logs -->
                        <div class="col-xl-12">
                            <div class="dashboard-card card">
                                <div class="card-header">
                                    <h6>Registro de Actividades</h6>
                                </div>
        <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Usuario</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Fecha/Hora</th>
                                                    <th class="text-center">Ubicación</th>
                    </tr>
                </thead>
                <tbody id="accionesTableBody">
                                                <!-- Se actualizará vía JavaScript -->
                </tbody>
            </table>
                                    </div>
                                    <div class="d-flex justify-content-center mt-4">
            <ul id="accionesPagination" class="pagination"></ul>
        </div>
    </div>
                            </div>
                        </div>
                    </div>
        </div>
    </div>
        </div>
    </div>
</div>

<!-- Modal para Detalles de Useragent -->
<div class="modal fade" id="useragentModal" tabindex="-1" aria-labelledby="useragentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
                <h5 class="modal-title" id="useragentModalLabel">Detalles del Useragent</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="useragentModalBody">
        <!-- Se llenará dinámicamente -->
      </div>
    </div>
  </div>
</div>

<!-- Modal para Detalles de Post -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
                <h5 class="modal-title" id="postModalLabel">Detalles del Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="postModalBody">
                <!-- Se llenará dinámicamente -->
      </div>
    </div>
  </div>
</div>

<!-- Scripts necesarios -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Variables globales
let pieChartUbicacion, pieChartUsuario, barChartUseragent, map, markers = [];
let accionesData = [];
let accionesPage = 1;
const registrosPorPagina = 20;
let ipCache = new Map(); // Cache para IPs
let lastIpApiCall = 0; // Timestamp de la última llamada a IP-API
const IP_API_DELAY = 1000; // Delay entre llamadas (1 segundo)

// Inicialización del mapa
function initMap() {
    map = L.map('logsMap', {
        maxZoom: 8,
        minZoom: 3,
        zoomControl: true,
        attributionControl: false
    }).setView([-34.6083, -58.3712], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 8,
        minZoom: 3,
        tileSize: 256,
        updateWhenIdle: true,
        updateWhenZooming: false,
        updateInterval: 1000,
        zIndex: 1
    }).addTo(map);

    L.control.zoom({
        position: 'bottomright',
        zoomInTitle: 'Acercar',
        zoomOutTitle: 'Alejar'
    }).addTo(map);
}

// Función para obtener la ubicación de una IP con caché
async function getIpLocation(ip) {
    // Verificar si la IP está en caché
    if (ipCache.has(ip)) {
        return ipCache.get(ip);
    }

    // Esperar si es necesario para respetar el límite de velocidad
    const now = Date.now();
    const timeSinceLastCall = now - lastIpApiCall;
    if (timeSinceLastCall < IP_API_DELAY) {
        await new Promise(resolve => setTimeout(resolve, IP_API_DELAY - timeSinceLastCall));
    }

    try {
        const response = await fetch(`http://ip-api.com/json/${encodeURIComponent(ip)}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        lastIpApiCall = Date.now();

        // Guardar en caché solo si la consulta fue exitosa
        if (data.status === "success") {
            ipCache.set(ip, data);
            return data;
        }
        return null;
    } catch (error) {
        console.error(`Error al obtener ubicación para IP ${ip}:`, error);
        return null;
    }
}

// Función para paginar datos
function paginate(dataArray, page) {
    const start = (page - 1) * registrosPorPagina;
    return dataArray.slice(start, start + registrosPorPagina);
}

// Renderizado de paginación
function renderPagination(containerId, dataArray, currentPage, callback) {
    const totalPages = Math.ceil(dataArray.length / registrosPorPagina);
    const container = document.getElementById(containerId);
    container.innerHTML = "";
    
    // Botón Anterior
    const prev = document.createElement("li");
    prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prev.innerHTML = `
        <a class="page-link" href="#" onclick="if(${currentPage} > 1) ${callback}(${currentPage - 1})">
            <i class="fas fa-chevron-left"></i> Anterior
        </a>
    `;
    container.appendChild(prev);
    
    // Números de página con ellipsis
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        const first = document.createElement("li");
        first.className = "page-item";
        first.innerHTML = `<a class="page-link" href="#" onclick="${callback}(1)">1</a>`;
        container.appendChild(first);
        
        if (startPage > 2) {
            const ellipsis = document.createElement("li");
            ellipsis.className = "page-item disabled";
            ellipsis.innerHTML = '<span class="page-link">...</span>';
            container.appendChild(ellipsis);
        }
    }
    
    for(let i = startPage; i <= endPage; i++) {
        const li = document.createElement("li");
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `
            <a class="page-link" href="#" onclick="${callback}(${i})">${i}</a>
        `;
        container.appendChild(li);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const ellipsis = document.createElement("li");
            ellipsis.className = "page-item disabled";
            ellipsis.innerHTML = '<span class="page-link">...</span>';
            container.appendChild(ellipsis);
        }
        
        const last = document.createElement("li");
        last.className = "page-item";
        last.innerHTML = `<a class="page-link" href="#" onclick="${callback}(${totalPages})">${totalPages}</a>`;
        container.appendChild(last);
    }
    
    // Botón Siguiente
    const next = document.createElement("li");
    next.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    next.innerHTML = `
        <a class="page-link" href="#" onclick="if(${currentPage} < ${totalPages}) ${callback}(${currentPage + 1})">
            Siguiente <i class="fas fa-chevron-right"></i>
        </a>
    `;
    container.appendChild(next);
}

// Actualización de marcadores en el mapa
async function updateMapMarkers(data) {
    // Limpiar marcadores existentes
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    // Limitar el número de marcadores mostrados
    const maxMarkers = 100; // Aumentado para mostrar más ubicaciones
    const limitedData = data.slice(0, maxMarkers);
    
    // Procesar marcadores en lotes
    for (const item of limitedData) {
        if (item.ip) {
            const locationData = await getIpLocation(item.ip);
            if (locationData) {
                const marker = L.circleMarker([locationData.lat, locationData.lon], {
                    radius: 6,
                    fillColor: "#6c5ce7",
                    color: "#fff",
                    weight: 1,
                    opacity: 0.8,
                    fillOpacity: 0.6
                }).addTo(map);

                const popupContent = `
                    <div style="padding: 8px; font-size: 0.9em;">
                        <strong>Acción:</strong> ${item.accion}<br>
                        <strong>Usuario:</strong> ${item.usuario ? item.usuario.user : 'Anónimo'}<br>
                        <strong>Fecha:</strong> ${item.fecha_hora}<br>
                        <strong>Ubicación:</strong> ${item.ubicacion}
                    </div>
                `;

                marker.bindPopup(popupContent, {
                    maxWidth: 250,
                    className: 'custom-popup'
                });
                markers.push(marker);
            }
        }
    }
    
    // Ajustar el zoom para mostrar todos los marcadores
    if (markers.length > 0) {
        const bounds = L.latLngBounds(markers.map(m => m.getLatLng()));
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}

// Actualización de la tabla de acciones
function updateAccionesTable(data) {
    accionesData = data;
    const paginated = paginate(accionesData, accionesPage);
    const tbody = document.getElementById('accionesTableBody');
    tbody.innerHTML = "";
    
    paginated.forEach(item => {
        const usuario = item.usuario ? item.usuario.user : 'Anónimo';
        const badge = item.status == 1 
            ? '<span class="badge bg-success">Correcto</span>' 
                                     : '<span class="badge bg-danger">Error</span>';
        
        let accionDisplay = item.accion;
        if(item.accion === "site/create-post" || item.accion === "site/comment") {
            accionDisplay = `
                <a href="javascript:void(0);" 
                   onclick="fetchPostForAction(${item.usuario ? item.usuario.id : 0}, '${item.fecha_hora}')"
                   class="text-primary">
                    ${item.accion}
                </a>`;
        }
        
        const row = `
            <tr>
                <td class="align-middle">
                    <span class="text-secondary text-xs font-weight-bold">${accionDisplay}</span>
                </td>
                <td class="align-middle">
                    <span class="text-secondary text-xs font-weight-bold">${usuario}</span>
                </td>
                <td class="align-middle text-center">
                    ${badge}
                </td>
                <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">${item.fecha_hora}</span>
                </td>
                <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">${item.ubicacion}</span>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    renderPagination("accionesPagination", accionesData, accionesPage, page => {
        accionesPage = page;
        updateAccionesTable(accionesData);
    });
}

// Función para obtener detalles del post
function fetchPostForAction(usuarioId, fechaHora) {
    fetch("<?= Url::to(['site/get-post']) ?>?usuario_id=" + usuarioId + "&fecha_hora=" + encodeURIComponent(fechaHora))
    .then(response => {
        if (!response.ok) throw new Error('Error en la red');
        return response.json();
    })
    .then(result => {
            document.getElementById('postModalBody').innerHTML = result.error 
                ? `<div class="alert alert-danger">${result.error}</div>`
                : result.contenido || "No se encontró contenido.";
        new bootstrap.Modal(document.getElementById('postModal')).show();
    })
    .catch(err => {
        console.error("Error al obtener post:", err);
            document.getElementById('postModalBody').innerHTML = 
                '<div class="alert alert-danger">Error al cargar el contenido.</div>';
        new bootstrap.Modal(document.getElementById('postModal')).show();
    });
}

// Actualización de gráficos
function updateCharts(data) {
    // Actualizar estadísticas rápidas
    document.getElementById('totalAcciones').textContent = data.length;
    
    const usuariosUnicos = new Set(data.map(item => item.usuario ? item.usuario.user : 'Anónimo')).size;
    document.getElementById('usuariosActivos').textContent = usuariosUnicos;
    
    const paisesUnicos = new Set(data.map(item => item.ubicacion ? item.ubicacion.split(',')[1]?.trim() : '').filter(Boolean)).size;
    document.getElementById('paisesUnicos').textContent = paisesUnicos;
    
    const hoy = new Date().toISOString().split('T')[0];
    const accionesHoy = data.filter(item => item.fecha_hora.startsWith(hoy)).length;
    document.getElementById('accionesHoy').textContent = accionesHoy;

    // Gráfico de pastel: Ubicación por país
    const countryData = {};
    data.forEach(item => {
        if(item.ubicacion) {
            const parts = item.ubicacion.split(',');
            const localidad = parts[0].trim();
            const pais = parts.length > 1 ? parts[1].trim() : localidad;
            
            if(!countryData[pais]) {
                countryData[pais] = { count: 0, localities: {} };
            }
            countryData[pais].count++;
            countryData[pais].localities[localidad] = (countryData[pais].localities[localidad] || 0) + 1;
        }
    });
    
    const paises = Object.keys(countryData);
    const paisData = paises.map(p => countryData[p].count);
    
    const ctxPieUbicacion = document.getElementById('pieChartUbicacion').getContext('2d');
    if(pieChartUbicacion) {
        pieChartUbicacion.data.labels = paises;
        pieChartUbicacion.data.datasets[0].data = paisData;
        pieChartUbicacion.update();
    } else {
        pieChartUbicacion = new Chart(ctxPieUbicacion, {
            type: 'pie',
            data: {
                labels: paises,
                datasets: [{
                    data: paisData,
                    backgroundColor: [
                        '#6c5ce7', '#a8a4e6', '#00b894', '#00cec9',
                        '#fdcb6e', '#ff7675', '#74b9ff', '#a29bfe'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const pais = context.label;
                                const locs = countryData[pais].localities;
                                let locText = "";
                                for(const loc in locs) {
                                    locText += `${loc} (${locs[loc]}), `;
                                }
                                locText = locText.slice(0,-2);
                                return `${pais}: ${context.parsed} registros\nLocalidades: ${locText}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico de pastel: Usuario
    const usuarioCounts = {};
    data.forEach(item => {
        const key = item.usuario ? item.usuario.user : 'Anónimo';
        usuarioCounts[key] = (usuarioCounts[key] || 0) + 1;
    });
    
    const usuarios = Object.keys(usuarioCounts);
    const usuarioData = Object.values(usuarioCounts);
    
    const ctxPieUsuario = document.getElementById('pieChartUsuario').getContext('2d');
    if(pieChartUsuario) {
        pieChartUsuario.data.labels = usuarios;
        pieChartUsuario.data.datasets[0].data = usuarioData;
        pieChartUsuario.update();
    } else {
        pieChartUsuario = new Chart(ctxPieUsuario, {
            type: 'pie',
            data: {
                labels: usuarios,
                datasets: [{
                    data: usuarioData,
                    backgroundColor: [
                        '#6c5ce7', '#a8a4e6', '#00b894', '#00cec9',
                        '#fdcb6e', '#ff7675', '#74b9ff', '#a29bfe'
                    ]
                }]
            },
            options: { responsive: true }
        });
    }

    // Gráfico de barras: Useragent
    const useragentCounts = {};
    data.forEach(item => {
        if(item.useragent) {
            useragentCounts[item.useragent] = (useragentCounts[item.useragent] || 0) + 1;
        }
    });
    
    const fullUseragents = Object.keys(useragentCounts);
    const truncatedLabels = fullUseragents.map(ua => 
        ua.length > 15 ? ua.substring(0,15) + "…" : ua
    );
    const useragentData = Object.values(useragentCounts);
    
    const ctxBarUseragent = document.getElementById('barChartUseragent').getContext('2d');
    if(barChartUseragent) {
        barChartUseragent.data.labels = truncatedLabels;
        barChartUseragent.data.datasets[0].data = useragentData;
        barChartUseragent.update();
    } else {
        barChartUseragent = new Chart(ctxBarUseragent, {
            type: 'bar',
            data: {
                labels: truncatedLabels,
                datasets: [{
                    label: 'Cantidad por Useragent',
                    data: useragentData,
                    backgroundColor: '#6c5ce7'
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                return `${fullUseragents[index]}: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                onClick: function(evt) {
                    const points = barChartUseragent.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                    if(points.length) {
                        const index = points[0].index;
                        const uaFull = fullUseragents[index];
                        const count = useragentData[index];
                        document.getElementById('useragentModalBody').innerHTML = `
                            <strong>Useragent:</strong> ${uaFull}<br>
                            <strong>Registros:</strong> ${count}
                        `;
                        new bootstrap.Modal(document.getElementById('useragentModal')).show();
                    }
                }
            }
        });
    }
}

// Actualizar la función fetchLogs para manejar errores mejor
function fetchLogs() {
    fetch("<?= Url::to(['site/api-logs']) ?>")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
    .then(data => {
        updateCharts(data);
        updateAccionesTable(data);
            updateMapMarkers(data);
    })
        .catch(error => {
            console.error('Error al obtener logs:', error);
            // Mostrar mensaje de error al usuario si es necesario
        });
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initMap();
fetchLogs();
    // Aumentar el intervalo de actualización a 60 segundos
    setInterval(() => fetchLogs(), 60000);
});
</script>
