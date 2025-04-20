<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Mi Perfil';

// Obtener el modelo del usuario actual
$usuario = Yii::$app->user->identity;
?>

<div class="mobile-profile-container">
    <div class="profile-header">
        <div class="profile-info">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h1 class="profile-username"><?= Html::encode($usuario->user) ?></h1>
        </div>
    </div>
    
    <div class="profile-card">
        <h2 class="section-title">Estado de Suscripción</h2>
        <div class="subscription-status-container" id="subscription-status">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>
    
    <div class="profile-card">
        <h2 class="section-title">Aplicación Móvil</h2>
        <p class="download-text">Descarga nuestra app para una mejor experiencia</p>
        <a href="<?= Url::to(['/mobile/download-app']) ?>" class="download-app-button">
            <i class="fas fa-download me-2"></i>Descargar App
        </a>
    </div>
    
    <div class="profile-card">
        <h2 class="section-title">Opciones de Cuenta</h2>
        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'logout-form']) ?>
            <?= Html::submitButton(
                '<i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión',
                ['class' => 'logout-button']
            ) ?>
        <?= Html::endForm() ?>
    </div>
</div>

<style>
    .mobile-profile-container {
        padding: 10px 15px;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .profile-header {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .profile-avatar {
        font-size: 60px;
        color: #4a90e2;
        margin-bottom: 5px;
    }
    
    .profile-username {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        padding: 12px 15px;
        margin-bottom: 12px;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .subscription-status-container {
        min-height: 60px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }
    
    .badge-container {
        width: 100%;
        text-align: center;
        margin-bottom: 15px;
    }
    
    .subscription-badge {
        display: block;
        padding: 8px 0;
        border-radius: 50px;
        font-weight: 500;
        font-size: 13px;
        text-align: center;
        width: 100%;
        box-sizing: border-box;
    }
    
    .subscription-active {
        background-color: #d1fae5;
        color: #047857;
    }
    
    .subscription-inactive {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .subscription-overdue {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .subscription-info {
        background: #f3f4f6;
        border-radius: 8px;
        padding: 12px;
        width: 100%;
        box-sizing: border-box;
    }
    
    .subscription-feature {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .subscription-feature i {
        color: #4a90e2;
        margin-right: 8px;
    }
    
    .download-text {
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    
    .download-app-button {
        display: block;
        width: 100%;
        padding: 10px;
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        color: white;
        text-align: center;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .download-app-button:hover, 
    .download-app-button:focus {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        color: white;
        text-decoration: none;
    }
    
    .logout-form {
        width: 100%;
    }
    
    .logout-button {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #f5f5f5;
        color: #dc3545;
        border: 1px solid #dc3545;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .logout-button:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Consultar la API para obtener el estado de la suscripción
        fetch('<?= Url::to(['/site/check-subscription']) ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('subscription-status');
            
            if (data.success) {
                const maxChars = data.maxChars || 480;
                const maxImages = data.maxImages || 1;
                const debugInfo = data.debug || {};
                
                // Obtener el estado de la suscripción 
                // 0: inactiva, 1: activa, 2: morosa
                const estadoSuscripcion = debugInfo.estado_suscripcion || 0;
                
                // Definir badge según estado
                let badgeClass, badgeIcon, badgeText;
                
                if (estadoSuscripcion === 1) {
                    badgeClass = "subscription-active";
                    badgeIcon = "fa-check-circle";
                    badgeText = "Suscripción Activa";
                } else if (estadoSuscripcion === 2) {
                    badgeClass = "subscription-overdue";
                    badgeIcon = "fa-exclamation-circle";
                    badgeText = "Suscripción Morosa";
                } else {
                    badgeClass = "subscription-inactive";
                    badgeIcon = "fa-times-circle";
                    badgeText = "Suscripción Básica";
                }
                
                // Limpiar contenedor
                container.innerHTML = "";
                
                // Crear el badge y añadirlo al contenedor
                const badgeContainer = document.createElement("div");
                badgeContainer.className = "badge-container";
                
                const badge = document.createElement("div");
                badge.className = "subscription-badge " + badgeClass;
                badge.innerHTML = `<i class="fas ${badgeIcon} me-1"></i>${badgeText}`;
                
                badgeContainer.appendChild(badge);
                container.appendChild(badgeContainer);
                
                // Crear el contenedor de información
                const infoContainer = document.createElement("div");
                infoContainer.className = "subscription-info";
                
                // Añadir características básicas
                infoContainer.innerHTML = `
                    <div class="subscription-feature">
                        <i class="fas fa-font"></i>
                        <span>Límite de caracteres: ${maxChars}</span>
                    </div>
                    <div class="subscription-feature">
                        <i class="fas fa-images"></i>
                        <span>Imágenes por post: ${maxImages}</span>
                    </div>
                `;
                
                // Añadir mensaje específico según estado
                if (estadoSuscripcion === 2) {
                    infoContainer.innerHTML += `
                        <div class="subscription-feature">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="text-warning">Tienes pagos pendientes. Actualiza tu suscripción para evitar la suspensión.</span>
                        </div>
                    `;
                } else if (estadoSuscripcion === 0) {
                    infoContainer.innerHTML += `
                        <div class="subscription-feature">
                            <i class="fas fa-star"></i>
                            <span>Mejora tu experiencia con una suscripción Premium</span>
                        </div>
                    `;
                }
                
                // Añadir el contenedor de información al contenedor principal
                container.appendChild(infoContainer);
                
            } else {
                container.innerHTML = `
                    <div class="alert alert-danger">
                        Error al cargar la información de suscripción
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('subscription-status').innerHTML = `
                <div class="alert alert-danger">
                    Error al cargar la información de suscripción
                </div>
            `;
        });
    });
</script> 