<?php

use yii\db\Migration;

class m170215_090137_logs extends Migration
{
    public function up()
    {
        $this->createTable('{{%logs}}', [
            'id' => $this->primaryKey(),
            'action' => $this->string(10),
            'ip' => $this->string()->notNull(),
            'user' => $this->string(),
            'description' => $this->text(),
            'after' => $this->text(),
            'until' => $this->text(),
            'create_at' => $this->timestamp()->notNull(),
            'level' => $this->smallInteger(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%logs}}');
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
