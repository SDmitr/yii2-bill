<?php

use yii\db\Migration;

class m170912_090535_user extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'last_name' => $this->string(45),
            'first_name' => $this->string(45),
            'username' => $this->string(15),
            'password' => $this->string(32),
            'auth_key' => $this->string(32),
            'access_token' => $this->string(32),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
//        echo "m170912_090535_user cannot be reverted.\n";

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
