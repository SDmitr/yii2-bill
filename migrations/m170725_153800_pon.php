<?php

use yii\db\Migration;

class m170725_153800_pon extends Migration
{
    public function up()
    {
        $this->createTable('{{%pon}}', [
            'id' => $this->primaryKey(),
            'mac' => $this->string()->notNull(),
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
        
//| update_pon |          | CREATE DEFINER=`billing`@`localhost` TRIGGER `update_pon` AFTER INSERT ON `pon` FOR EACH ROW BEGIN
// REPLACE INTO `pon_last`
// SET 
// `mac` = NEW.mac,
// `host` = NEW.host,
// `interface` = NEW.interface,
// `olt_power` = NEW.olt_power,
// `onu_power` = NEW.onu_power,
// `transmitted_power` = NEW.transmitted_power,
// `temperature_onu` = NEW.temperature_onu,
// `distance` = NEW.distance,
// `reason` = NEW.reason,
// `date` = NEW.date;
//END | utf8mb4              | utf8mb4_unicode_ci   | utf8_general_ci    |
         
        
    }

    public function down()
    {
        $this->dropTable('{{%pon}}');
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
