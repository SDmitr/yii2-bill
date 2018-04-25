<?php

use yii\db\Migration;

class m170215_071418_status extends Migration
{
    public function up()
    {
        $this->createTable('{{%status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'list' => $this->string()->notNull(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%status}}');
//        echo "m170215_071418_status cannot be reverted.\n";

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
