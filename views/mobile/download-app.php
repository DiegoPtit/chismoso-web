<?php
// Vista para descargar la aplicación Chismoso
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Descarga Chismoso App';

// Ruta al archivo APK
$apkPath = Url::to('@web/assets/apk/ChismosoApp_v1.apk');

// Ruta a la imagen promocional
$splashImage = Url::to('@web/assets/img/Splash.jpg');
?>

<div class="download-app-container">
    <div class="splash-container">
        <img src="<?= $splashImage ?>" alt="Chismoso App" class="splash-image">
        <div class="overlay-text">
            <h1 class="app-title">Chismoso App</h1>
            <p class="app-slogan">Social, libre, anónima.</p>
        </div>
    </div>
    
    <div class="download-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <div class="download-card">
                        <h2 class="download-title">¡Descarga la app y únete a la comunidad!</h2>
                        
                        <div class="features-container">
                            <div class="feature">
                                <i class="fas fa-user-secret feature-icon"></i>
                                <h3>100% Anónimo</h3>
                                <p>Comparte sin preocupaciones</p>
                            </div>
                            
                            <div class="feature">
                                <i class="fas fa-bolt feature-icon"></i>
                                <p>Rápido y ligero</p>
                            </div>
                            
                            <div class="feature">
                                <i class="fas fa-users feature-icon"></i>
                                <p>Comunidad vibrante</p>
                            </div>
                        </div>
                        
                        <div class="download-button-container">
                            <a href="<?= $apkPath ?>" class="download-button" download>
                                <i class="fas fa-download me-2"></i>Descargar APK
                            </a>
                            <p class="download-info">Versión 1.0 • 4.5 MB</p>
                        </div>
                        
                        <div class="installation-steps">
                            <h3>¿Cómo instalar?</h3>
                            <ol>
                                <li>Descarga el archivo APK</li>
                                <li>Activa "Instalación desde fuentes desconocidas" en tu Android</li>
                                <li>Abre el archivo descargado e instala</li>
                                <li>¡Empieza a compartir y conectar!</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="<?= Yii::$app->urlManager->createUrl(['mobile/index']) ?>" class="back-to-site">
                        <i class="fas fa-arrow-left me-2"></i>Volver a la web
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: #333;
    }
    
    .download-app-container {
        position: relative;
    }
    
    .splash-container {
        position: relative;
        height: 50vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .splash-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.8);
    }
    
    .overlay-text {
        position: absolute;
        text-align: center;
        color: white;
        z-index: 2;
    }
    
    .app-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .app-slogan {
        font-size: 1.5rem;
        font-weight: 300;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .download-section {
        padding: 2rem 1rem;
        background: white;
        border-radius: 20px 20px 0 0;
        margin-top: -20px;
        position: relative;
        z-index: 3;
        box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
    }
    
    .download-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .download-title {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        color: #333;
    }
    
    .features-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin: 2rem 0;
    }
    
    .feature {
        text-align: center;
        padding: 1rem;
        background: linear-gradient(145deg, #f8f9fa, #ffffff);
        border-radius: 12px;
        box-shadow: 5px 5px 10px rgba(0,0,0,0.05), -5px -5px 10px rgba(255,255,255,0.8);
        flex: 1 1 200px;
        max-width: 250px;
        transition: all 0.3s ease;
    }
    
    .feature:hover {
        transform: translateY(-5px);
    }
    
    .feature-icon {
        font-size: 2rem;
        color: #4a90e2;
        margin-bottom: 1rem;
    }
    
    .download-button-container {
        margin: 2rem 0;
    }
    
    .download-button {
        display: inline-block;
        padding: 1rem 2rem;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        background: linear-gradient(45deg, #4a90e2, #67b26f);
        color: white;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    .download-button:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        color: white;
        text-decoration: none;
    }
    
    .download-info {
        font-size: 0.9rem;
        color: #666;
        margin-top: 0.5rem;
    }
    
    .installation-steps {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 2rem;
        text-align: left;
    }
    
    .installation-steps h3 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .installation-steps ol {
        padding-left: 1.2rem;
    }
    
    .installation-steps li {
        margin-bottom: 0.8rem;
        line-height: 1.5;
    }
    
    .back-to-site {
        display: inline-block;
        padding: 0.5rem 1rem;
        color: #4a90e2;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .back-to-site:hover {
        transform: translateX(-5px);
        color: #67b26f;
    }
    
    /* Para dispositivos móviles */
    @media (max-width: 767px) {
        .app-title {
            font-size: 2.5rem;
        }
        
        .app-slogan {
            font-size: 1.2rem;
        }
        
        .download-title {
            font-size: 1.5rem;
        }
        
        .splash-container {
            height: 40vh;
        }
    }
</style>

<!-- Font Awesome para iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- Fuente Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"> 