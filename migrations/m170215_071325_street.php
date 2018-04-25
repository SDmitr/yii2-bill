<?php

use yii\db\Migration;

class m170215_071325_street extends Migration
{
    public function up()
    {
        $this->createTable('{{%street}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%street}}');
//        echo "m170215_071325_street cannot be reverted.\n";

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
