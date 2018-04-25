<?php

use yii\db\Migration;

class m170215_071403_tarif_inet extends Migration
{
    public function up()
    {
        $this->createTable('{{%tarif_inet}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'speed' => $this->integer()->notNull(),
            'money' => $this->string(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%tarif_inet}}');
//        echo "m170215_071403_tarif_inet cannot be reverted.\n";

//        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
