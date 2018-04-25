<?php

use yii\db\Migration;

class m170215_071335_client extends Migration
{
    public function up()
    {
        $this->createTable('{{%client}}', [
            'id' => $this->primaryKey(),
            'num' => $this->integer()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'street' => $this->string()->notNull(),
            'building' => $this->string(),
            'room' => $this->string(),
            'phone_1' => $this->string(),
            'phone_2' => $this->string(),
            'email' => $this->string(),
        ]);
        $this->createIndex('idx-client-num', '{{%client}}', 'num');

    }

    public function down()
    {
        $this->dropTable('{{%client}}');
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
