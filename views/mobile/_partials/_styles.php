<?php
/**
 * Archivo con los estilos CSS para las tarjetas de posts
 */
?>
<style>
    .card-material {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    .card-material:hover {
        transform: translateY(-6px) scale(1.01);
        box-shadow: 0 8px 17px rgba(0, 0, 0, 0.15);
    }
    
    .card-material.incognito:hover {
        background-color: #f8f9fa !important;
    }
    
    .card-material.blue:hover .card-body {
        background-color: #dbeafe !important;
    }
    
    .card-material.pink:hover .card-body {
        background-color: #fbcfe8 !important;
    }
    
    .card-material.yellow:hover .card-body {
        background-color: #fef9c3 !important;
    }
    
    .card-material.blue:hover .card-header,
    .card-material.blue:hover .card-footer {
        background-color: #bfdbfe !important;
    }
    
    .card-material.pink:hover .card-header,
    .card-material.pink:hover .card-footer {
        background-color: #f9a8d4 !important;
    }
    
    .card-material.yellow:hover .card-header,
    .card-material.yellow:hover .card-footer {
        background-color: #fef08a !important;
    }
    
    .card-material.incognito:hover .card-header,
    .card-material.incognito:hover .card-footer {
        background-color: #e5e7eb !important;
    }
    
    /* Estilos para las imágenes de los posts */
    .post-images {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 16px;
    }
    
    .post-image-item {
        flex: 0 0 calc(50% - 4px);
        overflow: hidden;
        border-radius: 8px;
        position: relative;
    }
    
    .post-image {
        width: 100%;
        height: auto;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
    .post-image:hover {
        transform: scale(1.05);
    }
    
    /* Para una sola imagen, que ocupe todo el ancho */
    .post-images:has(.post-image-item:only-child) .post-image-item {
        flex: 0 0 100%;
        max-height: 300px;
        overflow: hidden;
    }
    
    .post-images:has(.post-image-item:only-child) .post-image {
        object-fit: contain;
        max-height: 300px;
        width: 100%;
    }
    
    /* Estilos para el carrusel de imágenes */
    .post-carousel-container {
        width: 100%;
        margin: 15px 0;
        border-radius: 8px;
        overflow: hidden;
        min-height: 100px; /* Altura mínima para evitar colapso durante la carga */
        /* Prevenir problemas de renderizado */
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        perspective: 1000px;
        -webkit-perspective: 1000px;
    }

    .post-carousel {
        position: relative;
        width: 100%;
        background-color: #f1f1f1;
        border-radius: 8px;
        margin-bottom: 0;
        aspect-ratio: 16/9;
        max-height: 450px;
        overflow: hidden; /* Asegurar que el contenido no se desborde */
        /* Optimizaciones de renderizado */
        will-change: transform;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transform-style: preserve-3d;
        -webkit-transform-style: preserve-3d;
    }

    .carousel-inner {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: #000;
        border-radius: 8px;
        /* Optimizaciones para renderizado */
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    .carousel-item {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        visibility: hidden;
        pointer-events: none; /* Deshabilitar eventos en items inactivos */
    }

    .carousel-item.active {
        opacity: 1;
        z-index: 1;
        visibility: visible;
        pointer-events: auto; /* Habilitar eventos solo en el item activo */
    }

    .carousel-image {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        cursor: pointer;
        /* Optimizaciones de renderizado */
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        transition: transform 0.2s ease-out;
    }
    
    /* Fix para Safari y Chrome en ciertos dispositivos */
    @supports (-webkit-touch-callout: none) {
        .carousel-image {
            transform: translate3d(0, 0, 0);
            -webkit-transform: translate3d(0, 0, 0);
        }
    }

    /* Optimización para imágenes verticales y horizontales usando atributos */
    /* Usar data-ratio para calcular la orientación de la imagen */
    .carousel-item img[data-ratio="1"] {
        /* Imagen cuadrada */
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
    }
    
    .carousel-item img[data-ratio^="0."] {
        /* Imagen horizontal (ratio < 1) */
        width: 100%;
        height: auto;
    }
    
    .carousel-item img[data-ratio^="1."],
    .carousel-item img[data-ratio^="2."],
    .carousel-item img[data-ratio^="3."] {
        /* Imagen vertical (ratio > 1) */
        height: 100%;
        width: auto;
    }
    
    /* Fallback para navegadores que no soportan :has() */
    .carousel-item:has(img[src*="vertical"]) .carousel-image,
    .carousel-item:has(img[height][width][height>width]) .carousel-image {
        height: 100%;
        width: auto;
    }

    .carousel-item:has(img[src*="horizontal"]) .carousel-image,
    .carousel-item:has(img[height][width][width>height]) .carousel-image {
        width: 100%;
        height: auto;
    }
    
    /* Ajustar altura del carrusel cuando solo hay una imagen */
    .post-carousel:has(.carousel-item:only-child) {
        aspect-ratio: auto;
    }

    .carousel-control {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #444;
        font-size: 16px;
        cursor: pointer;
        z-index: 2;
        opacity: 0.7;
        transition: opacity 0.3s ease, background-color 0.3s ease;
    }

    .post-carousel:hover .carousel-control {
        opacity: 1;
    }

    .carousel-control:hover {
        background-color: rgba(255, 255, 255, 0.9);
    }

    .carousel-control.prev {
        left: 10px;
    }

    .carousel-control.next {
        right: 10px;
    }

    .carousel-indicators {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: fit-content;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        z-index: 10;
        background-color: rgba(0, 0, 0, 0.6);
        border-radius: 20px;
        padding: 6px 12px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        min-width: 60px;
    }

    .carousel-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.6);
        border: none;
        padding: 0;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
        margin: 0 3px;
    }

    .carousel-dot.active {
        background-color: #fff;
        transform: scale(1.3);
    }
    
    /* Estilos para el modal de imagen */
    .modal-image-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    
    .modal-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }
    
    .modal-image.zoomed {
        transform: scale(1.5);
        cursor: zoom-out;
    }
    
    .modal-image:not(.zoomed) {
        cursor: zoom-in;
    }
    
    .modal-controls {
        position: fixed;
        bottom: 20px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 10px;
        z-index: 1060;
    }
    
    .modal-control-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .modal-control-btn:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    /* Estilos para la funcionalidad "Ver más" */
    .expandable {
        position: relative;
    }
    
    .content-preview {
        margin-bottom: 5px;
        white-space: pre-line;
    }
    
    .content-full {
        animation: fadeIn 0.3s ease-in-out;
        white-space: pre-line;
    }
    
    .btn-ver-mas {
        background: none;
        border: none;
        padding: 0;
        color: #6c5ce7;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-block;
        text-decoration: none;
    }
    
    .btn-ver-mas:hover, .btn-ver-mas:focus {
        color: #5541d7;
        transform: translateY(-1px);
        text-decoration: none;
    }
    
    .btn-ver-mas:active {
        transform: translateY(0);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style> 