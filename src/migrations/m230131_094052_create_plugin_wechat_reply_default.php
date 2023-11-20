<?php

use yii\db\Migration;

/**
 * Class m230131_094052_create_plugin_wechat_reply_default
 */
class m230131_094052_create_plugin_wechat_reply_default extends Migration
{
    private string $tableName = '{{%plugin_wechat_reply_default}}';
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
            'follow_content' => $this->string(200)->null()->defaultValue('')->comment('Subscribe reply keyword'),
            'default_content' => $this->string(200)->null()->defaultValue('')->comment('Default reply keyword'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat reply table');
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
