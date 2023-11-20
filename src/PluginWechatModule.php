<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat;

use yii\base\Module;
use yii\i18n\PhpMessageSource;
use Yii;

/**
 * Class SrbacModule
 * @package davidxu\plugin\wechat
 */
class PluginWechatModule extends Module
{
//    /**
//     * @var string
//     */
    public $controllerNamespace = 'davidxu\plugin\wechat\controllers';

    public function init()
    {
        parent::init();

        if (!isset(Yii::$app->i18n->translations['plugin_wechat'])) {
            Yii::$app->i18n->translations['plugin_wechat'] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en-US',
                'basePath' => '@davidxu/plugin/wechat/messages',
            ];
        }
    }
}
