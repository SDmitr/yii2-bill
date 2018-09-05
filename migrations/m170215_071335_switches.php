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
            'oid' => $this->string(),
            'ip' => $this->string()->notNull()->unique(),
            'interface_count' => $this->integer()->notNull(),
            'interface_status' => $this->text(),
            'fdb' => $this->text(),
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
