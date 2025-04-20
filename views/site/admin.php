<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Panel de Administración';

// Verificar que el usuario está autenticado y tiene rol adecuado
if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['site/index']);
}

$controllerUrl = Yii::$app->controller->id;
?>

<div class="site-admin">
    <div class="body-content">
        <h1 class="text-center mb-4"><i class="fas fa-cog"></i> Panel de Administración</h1>
        <p class="lead text-center mb-4">Administra tu sitio El Chismoso</p>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="forum-container">
                    <div class="forum-post">
                        <!-- Cards de acceso rápido -->
                        <div class="row">
                            <!-- Tarjeta de Dashboard -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <a href="<?= Url::to(['admin/logs']) ?>" class="text-decoration-none">
                                    <div class="admin-card">
                                        <div class="admin-card-icon bg-info">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="admin-card-title">Dashboard</div>
                                        <div class="admin-card-description">Visualizar logs y actividad</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Tarjeta de Gestión de Contenido -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <a href="<?= Url::to(['admin/contenido']) ?>" class="text-decoration-none">
                                    <div class="admin-card">
                                        <div class="admin-card-icon bg-warning">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="admin-card-title">Contenido</div>
                                        <div class="admin-card-description">Gestionar posts y contenido</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Tarjeta de Usuarios -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <a href="<?= Url::to(['admin/usuarios']) ?>" class="text-decoration-none">
                                    <div class="admin-card">
                                        <div class="admin-card-icon bg-success">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="admin-card-title">Usuarios</div>
                                        <div class="admin-card-description">Administrar usuarios</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Tarjeta de Configuración -->
                            <div class="col-md-6 col-lg-3 mb-4">
                                <a href="<?= Url::to(['admin/configuracion']) ?>" class="text-decoration-none">
                                    <div class="admin-card">
                                        <div class="admin-card-icon bg-secondary">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                        <div class="admin-card-title">Configuración</div>
                                        <div class="admin-card-description">Ajustes del sistema</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Estadísticas Rápidas -->
                        <div class="stats-container mt-3">
                            <h4 class="mb-3">Vista general</h4>
                            <div class="row">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-primary">
                                        <div class="stats-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number"><?= $stats['usuarios'] ?? 0 ?></span>
                                            <span class="stats-label">Usuarios</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-success">
                                        <div class="stats-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number"><?= $stats['posts'] ?? 0 ?></span>
                                            <span class="stats-label">Posts</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-danger">
                                        <div class="stats-icon">
                                            <i class="fas fa-ban"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number"><?= $stats['baneados'] ?? 0 ?></span>
                                            <span class="stats-label">Baneados</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="stats-card bg-info">
                                        <div class="stats-icon">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                        <div class="stats-info">
                                            <span class="stats-number"><?= $stats['comentarios'] ?? 0 ?></span>
                                            <span class="stats-label">Comentarios</span>
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
</div>

<style>
/* Estilos para las tarjetas de administración */
.admin-card {
    background-color: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.3s ease;
}

.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.12);
}

.admin-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.admin-card-icon i {
    font-size: 1.5rem;
    color: white;
}

.admin-card-title {
    font-weight: 600;
    font-size: 1.2rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.admin-card-description {
    font-size: 0.9rem;
    color: #7f8c8d;
}

/* Estilos para las tarjetas de estadísticas */
.stats-container {
    background-color: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.stats-card {
    border-radius: 10px;
    padding: 1rem;
    height: 100%;
    display: flex;
    align-items: center;
    color: white;
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

/* Colores para fondos degradados */
.bg-primary {
    background: linear-gradient(45deg, #4a90e2, #6aa9f1);
}

.bg-warning {
    background: linear-gradient(45deg, #f7b731, #fdcb6e);
}

.bg-success {
    background: linear-gradient(45deg, #00b894, #55efc4);
}

.bg-info {
    background: linear-gradient(45deg, #0984e3, #74b9ff);
}

.bg-danger {
    background: linear-gradient(45deg, #d63031, #ff7675);
}

.bg-secondary {
    background: linear-gradient(45deg, #636e72, #b2bec3);
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .admin-card {
        padding: 1rem;
    }
    
    .admin-card-icon {
        width: 50px;
        height: 50px;
    }
    
    .stats-card {
        padding: 0.75rem;
    }
    
    .stats-icon {
        width: 40px;
        height: 40px;
    }
    
    .stats-number {
        font-size: 1.2rem;
    }
}
</style> 