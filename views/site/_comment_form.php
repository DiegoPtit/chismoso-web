<?php
/**
 * Vista parcial para mostrar un formulario de comentario
 * 
 * @var $model app\models\Posts o comentario
 * @var $formId string ID del formulario
 * @var $inputId string ID del campo de texto
 * @var $type string Tipo de formulario ('post' o 'comment')
 */
?>

<div class="comment-form-container" id="<?= $formId ?>" style="display: none;">
    <form class="comment-form" action="<?= Yii::$app->urlManager->createUrl(['site/comentar']) ?>" method="post">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
        <input type="hidden" name="padre_id" value="<?= $model->id ?>">
        <div class="form-group">
            <label for="age-<?= $type ?>-<?= $model->id ?>">Edad (opcional)</label>
            <input type="number" id="age-<?= $type ?>-<?= $model->id ?>" name="age" min="13" max="100" class="form-control">
        </div>
        <div class="form-group">
            <label for="genre-<?= $type ?>-<?= $model->id ?>">Género (opcional)</label>
            <select id="genre-<?= $type ?>-<?= $model->id ?>" name="genre" class="form-control">
                <option value="0">Prefiero no decir</option>
                <option value="1">Hombre</option>
                <option value="2">Mujer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="<?= $inputId ?>">Comentario</label>
            <textarea id="<?= $inputId ?>" name="contenido" class="form-control" maxlength="480" rows="3" required></textarea>
            <small class="character-count">0/480 caracteres</small>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Publicar</button>
            <button type="button" class="btn btn-secondary cancel-comment">Cancelar</button>
        </div>
    </form>
</div> 