<?php

use yii\db\Migration;

/**
 * Class m230131_100322_create_plugin_wechat_rule
 */
class m230131_100322_create_plugin_wechat_rule extends Migration
{
    private string $tableName = '{{%plugin_wechat_rule}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'merchant_id' => $this->integer()->comment('Merchant'),
            'name' => $this->string(50)->notNull()->comment('Rule name'),
            'mode' => $this->string(20)->notNull()->comment('Mode'),
            'data' => $this->binary()->comment('Data'),
            'order' => $this->integer()->defaultValue(0)->comment('Sort order'),
            'hit' => $this->integer()->null()->defaultValue(1)->comment('Hit'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat rule table');
        $this->createIndex('UNQ_Name_Merchant', $this->tableName, ['merchant_id', 'name'], true);
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('UNQ_Name_Merchant', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
