<?php

use yii\db\Migration;

class m170215_071412_tarif_tv extends Migration
{
    public function up()
    {
        $this->createTable('{{%tarif_tv}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'money'=> $this->string(),

        ]);

    }

    public function down()
    {
        $this->dropTable('{{%tarif_tv}}');
//        echo "m170215_071412_tarif_tv cannot be reverted.\n";

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
