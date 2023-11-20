<?php

use yii\db\Migration;

/**
 * Class m230131_100657_create_plugin_wechat_fans_tag_map
 */
class m230131_100657_create_plugin_wechat_fans_tag_map extends Migration
{
    private string $tableName = '{{%plugin_wechat_fans_tag_map}}';
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql' || $this->db->driverName === 'mariadb') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->execute('SET foreign_key_checks = 0');
        $this->createTable($this->tableName, [
            'fans_id' => $this->integer()->comment('Fans ID'),
            'tag_id' => $this->integer()->comment('Tag ID'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat fans-tag relationship table');
        $this->addPrimaryKey('PK_FansTag_Mapping', $this->tableName, ['fans_id', 'tag_id']);
        $this->createIndex('IDX-Fans_Tag-FansId', $this->tableName, 'fans_id');
        $this->addForeignKey('FK-Fans_Tag-FansId', $this->tableName, 'fans_id',
            '{{%plugin_wechat_fans}}', 'id', 'CASCADE'
        );
        $this->createIndex('IDX-Fans_Tag-TagId', $this->tableName, 'tag_id');
        $this->addForeignKey('FK-Fans_Tag-TagId', $this->tableName, 'tag_id',
            '{{%plugin_wechat_fans_tag}}', 'id', 'CASCADE'
        );
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropForeignKey('FK-Fans_Tag-FansId', $this->tableName);
        $this->dropForeignKey('FK-Fans_Tag-TagId', $this->tableName);
        $this->dropIndex('IDX-Fans_Tag-FansId', $this->tableName);
        $this->dropIndex('IDX-Fans_Tag-TagId', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
