<?php

use yii\db\Migration;

class m170215_071431_inet extends Migration
{
    public function up()
    {
        $this->createTable('{{%inet}}', [
            'id' => $this->primaryKey(),
            'num' => $this->integer()->notNull(),
            'ip' => $this->string()->notNull()->unique(),
            'aton' => $this->bigInteger()->notNull()->unique(),
            'mac' => $this->string()->notNull()->unique(),
            'comment' => $this->string(),
            'switch' => $this->integer(),
            'interface' => $this->integer(),
            'tarif_id' => $this->integer(),
            'status_id' => $this->integer(),
            'onu_mac' => $this->string(),
            'date_on' => $this->dateTime(),
            'date_off' => $this->dateTime(),
            'date_create' => $this->dateTime(),
        ]);
        
        $this->addForeignKey('fk-inet-client', '{{%inet}}', 'num', '{{%client}}', 'num', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-inet-tarif', '{{%inet}}', 'tarif_id', '{{%tarif_inet}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-inet-status', '{{%inet}}', 'status_id', '{{%status}}', 'id', 'SET NULL', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%inet}}');
//        echo "m170215_071431_inet cannot be reverted.\n";

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
