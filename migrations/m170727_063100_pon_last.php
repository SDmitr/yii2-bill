<?php

use yii\db\Migration;

class m170727_063100_pon_last extends Migration
{
    public function up()
    {
        $this->createTable('{{%pon_last}}', [
            'mac' => $this->primaryKey()->unsigned(),
            'host' => $this->string()->notNull(),
            'interface' => $this->string(),
            'olt_power' => $this->decimal(10, 2),
            'onu_power' => $this->decimal(10, 2),
            'transmitted_power' => $this->decimal(10, 2),
            'temperature_onu' => $this->decimal(10, 2),
            'distance' => $this->integer(),
            'reason' => $this->string(),
            'date' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%pon_last}}');
//        echo "m170725_153800_pon cannot be reverted.\n";

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
