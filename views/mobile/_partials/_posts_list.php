<?php
/**
 * Archivo parcial para renderizar una lista de posts
 * @var array $posts Array de objetos Posts
 */
?>

<?php foreach ($posts as $post): ?>
    <?php require '_post_card.php'; ?>
<?php endforeach; ?> 