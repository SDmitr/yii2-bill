<?php

use yii\db\Migration;

class m170215_090137_money extends Migration
{
    public function up()
    {
        $this->createTable('{{%money}}', [
            'id' => $this->primaryKey(),
            'num' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-money-client', '{{%money}}', 'num', '{{%client}}', 'num', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%money}}');        
//        echo "m170215_090137_money cannot be reverted.\n";
//
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
