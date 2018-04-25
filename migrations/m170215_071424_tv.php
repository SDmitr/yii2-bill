<?php

use yii\db\Migration;

class m170215_071424_tv extends Migration
{
    public function up()
    {
        $this->createTable('{{%tv}}', [
            'id' => $this->primaryKey(),
            'inet_id' => $this->integer()->notNull(),
            'tarif_id' => $this->integer(),
            'status_id' => $this->integer(),
            'date_on' => $this->dateTime(),
            'date_off' => $this->dateTime(),
            'date_create' => $this->dateTime(),
        ]);
        $this->addForeignKey('fk-tv-inet', '{{%tv}}', 'inet_id', '{{%inet}}', 'id', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk-tv-tarif_tv', '{{%tv}}', 'tarif_id', '{{%tarif_tv}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk-tv-status', '{{%tv}}', 'status_id', '{{%status}}', 'id', 'SET NULL', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%tv}}');
//        echo "m170215_071424_tv cannot be reverted.\n";

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
