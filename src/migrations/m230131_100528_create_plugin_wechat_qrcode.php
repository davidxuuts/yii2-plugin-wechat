<?php

use yii\db\Migration;

/**
 * Class m230131_100528_create_plugin_wechat_qrcode
 */
class m230131_100528_create_plugin_wechat_qrcode extends Migration
{
    private string $tableName = '{{%plugin_wechat_qrcode}}';
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
            'name' => $this->string(50)->notNull()->comment('Scene name'),
            'keyword' => $this->string(50)->notNull()->comment('Keywords'),
            'model' => $this->tinyInteger(1)->notNull()->comment('Model'),
            'scene_id' => $this->integer()->null()->defaultValue(0)->comment('Scene id'),
            'scene_str' => $this->string(64)->null()->defaultValue('')->comment('Scene name'),
            'ticket' => $this->string(50)->null()->defaultValue('')->comment('Scene name'),
            'expire_seconds' => $this->integer()->null()->defaultValue(2592000)->comment('Expire(sec)'),
            'scan_number' => $this->integer()->null()->defaultValue(0)->comment('Scan number'),
            'type' => $this->string(10)->null()->comment('Qrcode type'),
            'extra' => $this->integer()->null()->comment('Extra info'),
            'url' => $this->string(50)->null()->comment('Url'),
            'end_time' => $this->integer()->null()->defaultValue(0)->comment('End time'),
            'status' => $this->tinyInteger(4)->defaultValue(1)
                ->comment('Status[-1:Deleted;0:Disabled;1:Enabled]'),
            'created_at' => $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Created at'),
            'updated_at' => $this->integer()->notNull()
                ->defaultExpression('UNIX_TIMESTAMP()')
                ->comment('Updated at')
        ], $tableOptions);
        $this->addCommentOnTable($this->tableName, 'Plugin wechat qrcode table');

        $this->createIndex('IDX_WechatQRCode_SceneID',$this->tableName,'scene_id',0);
        $this->createIndex('IDX_WechatQRCode_Ticket',$this->tableName,'ticket',0);

        $this->execute('SET foreign_key_checks = 1');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropIndex('IDX_WechatQRCode_SceneID', $this->tableName);
        $this->dropIndex('IDX_WechatQRCode_Ticket', $this->tableName);
        $this->dropTable($this->tableName);
        $this->execute('SET foreign_key_checks = 1');
    }
}
