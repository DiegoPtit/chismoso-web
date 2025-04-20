<?php
/**
 * Estilos específicos para la vista de comentarios
 */
?>

<style>
/* Estilos para el post original en la vista de detalles */
.original-post {
    border-left: 5px solid #6c5ce7;
    background-color: #f8f9ff;
}

/* Estilos para la sección de título */
.section-title {
    margin: 20px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #e0e0e0;
}

.section-title h3 {
    font-size: 1.3rem;
    margin: 0;
    color: #555;
}

/* Estilos para el comentario resaltado */
.forum-comment.highlighted {
    background-color: rgba(108, 92, 231, 0.1);
    border-left: 5px solid #6c5ce7;
    animation: highlight-pulse 1.5s ease-in-out infinite alternate;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.forum-comment.highlighted::before {
    content: '→';
    position: absolute;
    left: -25px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c5ce7;
    font-size: 20px;
    animation: arrow-pulse 1s ease-in-out infinite;
}

@keyframes highlight-pulse {
    0% {
        background-color: rgba(108, 92, 231, 0.05);
    }
    100% {
        background-color: rgba(108, 92, 231, 0.2);
    }
}

@keyframes arrow-pulse {
    0% {
        left: -30px;
        opacity: 0.5;
    }
    100% {
        left: -25px;
        opacity: 1;
    }
}

/* Estilos para la sección de comentarios */
.forum-comments-section {
    padding: 20px;
}

.comments-title {
    font-size: 1.2rem;
    margin-bottom: 15px;
    color: #555;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 10px;
}

.no-comments-message {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
    text-align: center;
    background-color: #f9f9f9;
    border-radius: 5px;
    color: #777;
}

.no-comments-message i {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #6c5ce7;
}

.comment-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.comment-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.comment-stats {
    display: flex;
    gap: 10px;
}

.comment-content {
    margin-left: 35px;
    margin-bottom: 10px;
    line-height: 1.5;
}

.comment-actions {
    margin-left: 35px;
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

/* Ajustes para la consistencia entre páginas */
.site-comentarios .body-content {
    padding-top: 20px;
    padding-bottom: 40px;
}

.comments-container {
    margin-top: 0;
}

/* Estilo para el botón de compartir */
.share-button {
    color: #17a2b8;
}

.share-button:hover {
    background-color: #e6f7f9;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .comment-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .comment-stats {
        margin-top: 5px;
        margin-left: 35px;
    }
    
    .comment-content,
    .comment-actions {
        margin-left: 0;
    }
    
    .forum-comment.highlighted::before {
        display: none;
    }
}
</style> 