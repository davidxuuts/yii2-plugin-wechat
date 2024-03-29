<?php

use yii\db\Migration;

/**
 * Class m230201_085426_create_plugin_wechat_material
 */
class m230201_085426_create_plugin_wechat_material extends Migration
{
    private string $tableName = '{{%plugin_wechat_material}}';
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
            'file_name' => $this->string(200)->null()->comment('Original name'),
            'local_url' => $this->string(1024)->null()->comment('Local URL'),
            'media_type' => $this->string(15)->defaultValue('images')
                ->comment('Media type'),
            'media_id' => $this->string(64)->comment('Wechat media ID'),
            'media_url' => $this->string(1024)->comment('Wechat media URL'),
            'width' => $this->integer()->defaultValue(0)->comment('Width'),
            'height' => $this->integer()->defaultValue(0)->comment('Height'),
            'update_time' => $this->integer()->comment('Update time'),
            'description' => $this->string(255)
                ->comment('Description description'),
            'material_type' => $this->tinyInteger(4)->notNull()->comment('Wechat file type[0:temporary;1:permanent]'),
            'link_type' => $this->tinyInteger(4)->null()->defaultValue(1)
                ->comment('Link type[1:wechat;2:local]'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat material table');

//        $this->createIndex('UNQ_Material_MediaId', $this->tableName, ['merchant_id', 'media_id', 'media_type', 'material_type'],true);
        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
//        $this->dropIndex('UNQ_Material_MediaId', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
