<?php

use yii\db\Migration;

class m250413_203458_add_leido_to_notificaciones extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250413_203458_add_leido_to_notificaciones cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250413_203458_add_leido_to_notificaciones cannot be reverted.\n";

        return false;
    }
    */
}
