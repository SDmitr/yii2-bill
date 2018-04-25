<?php

use yii\db\Migration;

class m170215_090123_network extends Migration
{
    public function up()
    {
        $this->createTable('{{%network}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'first_ip' => $this->string()->notNull()->unique(),
            'last_ip' => $this->string()->notNull()->unique(),
            'gateway' => $this->string()->notNull(),
            'subnet' => $this->string()->notNull(),
            'mask' => $this->string()->notNull(),
            'dns1' => $this->string()->notNull(),
            'dns2' => $this->string()->notNull(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%network}}');
//        echo "m170215_090123_network cannot be reverted.\n";

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
