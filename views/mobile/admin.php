<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Panel de Administración';

// Verificar que el usuario está autenticado y tiene rol adecuado
if (Yii::$app->user->isGuest || !in_array(Yii::$app->user->identity->rol_id, [1313, 1314, 1315])) {
    Yii::$app->session->setFlash('error', 'No tienes permiso para acceder a esta sección.');
    return Yii::$app->response->redirect(['mobile/index']);
}

$controllerUrl = Yii::$app->controller->id;
?>

<div class="container-fluid py-3">
    <h2 class="mb-3 text-center"><?= Html::encode($this->title) ?></h2>

    <div class="row">
        <!-- Tarjeta de Dashboard -->
        <div class="col-6 mb-3">
            <a href="<?= Url::to(['mobile/logs']) ?>" class="text-decoration-none">
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
        <div class="col-6 mb-3">
            <a href="<?= Url::to(['mobile/gestion-contenido']) ?>" class="text-decoration-none">
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
        <div class="col-6 mb-3">
            <a href="<?= Url::to(['mobile/admin-usuarios']) ?>" class="text-decoration-none">
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
        <div class="col-6 mb-3">
            <a href="<?= Url::to(['mobile/configuracion']) ?>" class="text-decoration-none">
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
    <div class="dashboard-card card mt-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Vista general</h5>
            
            <div class="row g-2">
                <div class="col-6">
                    <div class="mini-stats-card bg-primary">
                        <div class="mini-stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="mini-stats-info">
                            <span class="mini-stats-number"><?= $stats['usuarios'] ?? 0 ?></span>
                            <span class="mini-stats-label">Usuarios</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stats-card bg-success">
                        <div class="mini-stats-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="mini-stats-info">
                            <span class="mini-stats-number"><?= $stats['posts'] ?? 0 ?></span>
                            <span class="mini-stats-label">Posts</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stats-card bg-danger">
                        <div class="mini-stats-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="mini-stats-info">
                            <span class="mini-stats-number"><?= $stats['baneados'] ?? 0 ?></span>
                            <span class="mini-stats-label">Baneados</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stats-card bg-info">
                        <div class="mini-stats-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="mini-stats-info">
                            <span class="mini-stats-number"><?= $stats['comentarios'] ?? 0 ?></span>
                            <span class="mini-stats-label">Comentarios</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 1.25rem;
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
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }
    .admin-card-icon i {
        font-size: 1.5rem;
        color: white;
    }
    .admin-card-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }
    .admin-card-description {
        font-size: 0.85rem;
        color: #7f8c8d;
    }
    .dashboard-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .mini-stats-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        height: 100%;
    }
    .mini-stats-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }
    .mini-stats-icon i {
        font-size: 1rem;
        color: white;
    }
    .mini-stats-info {
        display: flex;
        flex-direction: column;
    }
    .mini-stats-number {
        font-weight: 600;
        font-size: 1.1rem;
        color: white;
        line-height: 1;
    }
    .mini-stats-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
    }
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
</style> 