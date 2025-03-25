<?php
/* @var $this yii\web\View */
/* @var $posts app\models\Posts[] */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Chismoso App';
?>
<div class="site-index">
    <style>
        /* Estilo para comentarios anidados */
        .subcomments {
            border-left: 3px solid #ddd;
            padding-left: 1.5rem;
            margin-left: 1rem;
        }
        /* Botón estilizado */
        .btn-cargar-mas {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }
    </style> 
    <?php if (empty($posts)): ?>
        <p class="text-center mt-5">No hay posts disponibles, intente de nuevo.</p>
    <?php else: ?>
        <!-- Contenedor donde se cargarán los posts -->
        <div id="posts-container">
            <?php foreach ($posts as $post): ?>
                <?= $this->render('_post', ['post' => $post]) ?>
            <?php endforeach; ?>
        </div>

        <!-- Botón para cargar más posts -->
        <button id="load-more-btn" class="btn btn-primary btn-cargar-mas">Cargar más</button>
    <?php endif; ?>

    <!-- Botón flotante para crear nuevos posts -->
    <?= Html::a(
        '<i class="fa fa-paper-plane"></i>',
        ['/site/create-post'],
        [
            'class' => 'btn btn-primary btn-lg rounded-circle position-fixed btn-flotante',
            'style' => 'bottom: 20px; right: 20px; z-index: 1000;'
        ]
    ); ?>
</div>

<!-- Script para cargar más posts con botón -->
<script>
    let offset = <?= count($posts) ?>;

document.getElementById('load-more-btn').addEventListener('click', function() {
    let btn = this;
    btn.innerText = "Cargando...";
    btn.disabled = true;

    fetch('<?= Url::to(['site/load-more']) ?>?offset=' + offset)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                btn.style.display = 'none';
                alert(data.message);
                return;
            }

            document.getElementById('posts-container').insertAdjacentHTML('beforeend', data.html);
            offset += 20;
            btn.innerText = "Cargar más";
            btn.disabled = false;
        })
        .catch(error => {
            console.error('Error al cargar más posts:', error);
            alert('Hubo un error al cargar más posts. Intente de nuevo.');
            btn.innerText = "Cargar más";
            btn.disabled = false;
        });
});

</script>

<?php
// Si se recibe un parámetro "modal", reabrir ese modal automáticamente
$modalId = Yii::$app->request->get('modal');
if ($modalId) {
    $this->registerJs("
        $(document).ready(function(){
            $('#commentModal$modalId').modal('show');
            $('#commentModal$modalId').on('hidden.bs.modal', function(){
                window.location.href = window.location.pathname;
            });
        });
        var modal = new bootstrap.Modal(document.getElementById('commentModal{$modalId}'));
        modal.show();
    ");
}
?>
