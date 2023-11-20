<?php

use yii\db\Migration;

/**
 * Class m230201_090605_create_plugin_wechat_material_news
 */
class m230201_090605_create_plugin_wechat_material_news extends Migration
{
    private string $tableName = '{{%plugin_wechat_material_news}}';
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
            'title' => $this->string(50)->null()->comment('Title'),
            'thumb_media_id' => $this->string(64)->comment('Wechat thumbnail media ID'),
            'thumb_url' => $this->string(255)->comment('Wechat thumbnail URL'),
            'author' => $this->string(64)->comment('Author'),
            'digest' => $this->string(255)->comment('Digest'),
            'show_cover_pic' => $this->tinyInteger(4)->notNull()->defaultValue(0)
                ->comment('0:false,1:true'),
            'order' => $this->integer()->defaultValue(0)->comment('Display order'),
            'content' => $this->text()->comment('Content'),
            'content_source_url' => $this->string(255)->comment('Content source url'),
            'can_comment' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Can comment'),
            'only_fans_can_comment' => $this->tinyInteger(4)->defaultValue(0)
                ->comment('Only fans can comment'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat material-news table');
        $this->addForeignKey('FK_PluginWechatMaterialId', $this->tableName,'material_id',
            '{{%plugin_wechat_material}}', 'id', 'CASCADE', 'CASCADE'
        );
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropForeignKey('FK_PluginWechatMaterialId', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
