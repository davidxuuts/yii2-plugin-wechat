<?php

use yii\db\Migration;

/**
 * Class m230131_100350_create_plugin_wechat_rule_keyword
 */
class m230131_100350_create_plugin_wechat_rule_keyword extends Migration
{
    private string $tableName = '{{%plugin_wechat_rule_keyword}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->comment('ID'),
            'rule_id' => $this->integer()->null()->comment('Rule ID'),
            'content' => $this->string(255)->notNull()->comment('Content'),
            'type' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('Type'),
            'order' => $this->integer()->null()->defaultValue(1)->comment('Sort order'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat rule keywords table');
        $this->addForeignKey('FK_WechatRuleId', $this->tableName, 'rule_id',
            '{{%plugin_wechat_rule}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('IDX_WechatRuleKwd_Content',$this->tableName,'content',0);
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropForeignKey('FK_WechatRuleId', $this->tableName);
        $this->dropIndex('IDX_WechatRuleKwd_Content', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
