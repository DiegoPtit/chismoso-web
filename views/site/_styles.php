<?php
/**
 * Archivo de estilos CSS para posts y comentarios
 */
?>

<style>
/* Estilos tipo foro */
.forum-container {
    background-color: #f9f9f9;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}

.forum-post {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    background-color: #fff;
}

.forum-post:last-child {
    border-bottom: none;
}

.forum-post-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    color: white;
    flex-shrink: 0;
}

/* Estilos para los avatares según rol_id */
.avatar.superadmin {
    background-color: #ffc107; /* Amarillo */
}

.avatar.admin {
    background-color: #fd7e14; /* Naranja */
}

.avatar.mod {
    background-color: #28a745; /* Verde */
}

.avatar.user-neutral {
    background-color: #6c757d; /* Gris */
}

.avatar.user-female {
    background-color: #007bff; /* Azul */
}

.avatar.user-male {
    background-color: #e83e8c; /* Rosado */
}

.avatar.small {
    width: 30px;
    height: 30px;
    font-size: 0.8rem;
}

.post-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.username {
    font-weight: 600;
    color: #444;
    font-size: 0.9rem;
}

.post-date, .comment-date {
    color: #777;
    font-size: 0.8rem;
}

.post-stats {
    display: flex;
    color: #666;
    font-size: 0.85rem;
}

.stat-item {
    margin-left: 10px;
}

.forum-post-content {
    margin-bottom: 15px;
    color: #333;
    line-height: 1.5;
    padding-left: 50px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

.forum-post-actions {
    padding-left: 50px;
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.btn-forum {
    border: none;
    background: #f1f1f1;
    color: #555;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-forum:hover {
    background: #e5e5e5;
}

.btn-forum.like-button:hover {
    color: #4267B2;
}

.btn-forum.dislike-button:hover {
    color: #e74c3c;
}

.btn-forum.active,
.btn-forum.like-button.active {
    background: #e9f5ff;
    color: #4267B2;
    transform: scale(1.05);
    transition: all 0.2s;
}

.btn-forum.dislike-button.active {
    background: #ffeeee;
    color: #e74c3c;
    transform: scale(1.05);
    transition: all 0.2s;
}

.forum-comments {
    padding-left: 50px;
    margin-top: 15px;
    border-top: 1px solid #f1f1f1;
    padding-top: 10px;
}

.forum-comments-header {
    color: #6c5ce7;
    cursor: pointer;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.forum-comments-header:hover {
    text-decoration: underline;
}

.comments-list {
    background-color: #f9f9f9;
    border-radius: 3px;
    border-left: 3px solid #e0e0e0;
    margin-top: 5px;
}

.forum-comment {
    padding: 10px;
    border-bottom: 1px solid #e9e9e9;
}

.forum-comment:last-child {
    border-bottom: none;
}

.forum-comment-header {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.comment-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.forum-comment-content {
    padding-left: 40px;
    color: #444;
    font-size: 0.9rem;
    margin-bottom: 10px;
    line-height: 1.5;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

.forum-comment-actions {
    padding-left: 40px;
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.comments-nested {
    margin-left: 30px;
    margin-top: 10px;
    border-left: 2px solid #e0e0e0;
    padding-left: 10px;
}

.nivel-1 {
    border-left-color: #6c5ce7;
}

.nivel-2 {
    border-left-color: #fd79a8;
}

.nivel-3 {
    border-left-color: #00b894;
}

.nivel-4 {
    border-left-color: #fdcb6e;
}

.nivel-5 {
    border-left-color: #e17055;
}

@media (max-width: 768px) {
    .forum-post-header, .forum-comment-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .post-stats {
        margin-top: 5px;
        margin-left: 50px;
    }
    
    .forum-post-content, 
    .forum-post-actions,
    .forum-comments,
    .forum-comment-content,
    .forum-comment-actions {
        padding-left: 0;
    }
}

/* Estilos para formularios de comentarios */
.comment-form-container {
    margin: 10px 0;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.comments-nested .comment-form-container {
    margin-left: 40px;
}

.comment-form .form-group {
    margin-bottom: 10px;
}

.comment-form label {
    font-weight: 500;
    font-size: 0.85rem;
    color: #495057;
}

.comment-form .form-control {
    border: 1px solid #ced4da;
    border-radius: 3px;
    font-size: 0.9rem;
}

.comment-form textarea {
    resize: vertical;
    min-height: 70px;
}

.character-count {
    display: block;
    text-align: right;
    color: #6c757d;
    font-size: 0.75rem;
    margin-top: 3px;
}

.comment-form .btn {
    padding: 5px 15px;
    font-size: 0.85rem;
}

.comment-form .btn-primary {
    background-color: #6c5ce7;
    border-color: #6c5ce7;
}

.comment-form .btn-primary:hover {
    background-color: #5f4dd0;
    border-color: #5f4dd0;
}

.comment-form .btn-secondary {
    background-color: #e9ecef;
    border-color: #e9ecef;
    color: #495057;
}

.comment-form .btn-secondary:hover {
    background-color: #dee2e6;
    border-color: #dee2e6;
}

/* Estilos para las imágenes de los posts */
.post-image-item {
    display: inline-block;
    margin: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 3px;
    background-color: white;
}

.post-image {
    max-width: 120px;
    max-height: 120px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.post-image:hover {
    transform: scale(1.05);
}

.post-images {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
    margin-bottom: 10px;
}

/* Para una sola imagen, que ocupe más espacio */
.post-images:has(.post-image-item:only-child) .post-image-item {
    flex: 0 0 auto;
    max-width: 250px;
}

.post-images:has(.post-image-item:only-child) .post-image {
    max-width: 250px;
    max-height: 250px;
    object-fit: contain;
}

/* Estilos para la funcionalidad "Ver más" */
.expandable {
    position: relative;
}

.content-preview {
    margin-bottom: 5px;
}

.btn-ver-mas {
    background: none;
    border: none;
    color: #6c5ce7;
    padding: 0;
    font-size: 0.85rem;
    cursor: pointer;
    margin-top: 5px;
    font-weight: 500;
}

.btn-ver-mas:hover {
    text-decoration: underline;
}

.content-full {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Estilos para el carrusel de imágenes */
.post-carousel-container {
    width: 100%;
    margin: 15px 0;
    border-radius: 4px;
    overflow: hidden;
}

.post-carousel {
    position: relative;
    width: 100%;
    background-color: #f1f1f1;
    border-radius: 6px;
    margin-bottom: 15px;
}

.carousel-inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    height: 350px;
    background-color: #000;
    border-radius: 6px;
}

.carousel-item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.3s ease;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-item.active {
    opacity: 1;
    z-index: 1;
}

.carousel-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    cursor: pointer;
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
    opacity: 0;
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
    transform: none;
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
</style> 