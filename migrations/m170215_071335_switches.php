<?php

use yii\db\Migration;

class m170215_071335_switches extends Migration
{
    public function up()
    {
        $this->createTable('{{%switches}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'vendor' => $this->string()->notNull(),
            'ip' => $this->string()->notNull()->unique(),
            'interfaces' => $this->text(),
            'fdb' => $this->text(),
            'status' => $this->integer()->notNull()
        ]);
        $this->createIndex('idx-switch-ip', '{{%switches}}', 'ip');

    }

    public function down()
    {
        $this->dropTable('{{%switches}}');
//        echo "m170215_071335_client cannot be reverted.\n";

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
