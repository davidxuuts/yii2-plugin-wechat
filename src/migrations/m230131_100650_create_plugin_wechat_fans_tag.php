<?php

use yii\db\Migration;

/**
 * Class m230131_100650_create_plugin_wechat_fans_tag
 */
class m230131_100650_create_plugin_wechat_fans_tag extends Migration
{
    private string $tableName = '{{%plugin_wechat_fans_tag}}';
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
            'tag_id' => $this->integer()->notNull()->comment('Tag ID from Wechat'),
            'name' => $this->string(30)->notNull()->comment('Tag name'),
            'count' => $this->integer()->defaultValue(0)->comment('Users count of this tag'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat fans tag table');
        $this->createIndex('UNQ_TagID', $this->tableName, ['merchant_id', 'tag_id', 'name'], true);
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('UNQ_TagID', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
