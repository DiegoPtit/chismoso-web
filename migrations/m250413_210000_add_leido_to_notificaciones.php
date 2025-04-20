<?php

use yii\db\Migration;

/**
 * Class m250413_210000_add_leido_to_notificaciones
 */
class m250413_210000_add_leido_to_notificaciones extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('notificaciones', 'leido', $this->boolean()->defaultValue(false)->after('comentario_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('notificaciones', 'leido');
    }
} 