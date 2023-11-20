<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\plugin\wechat\models\PluginWechatRule;
use yii\data\ActiveDataProvider;
use yii\web\View;
use davidxu\config\helpers\Html;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var PluginWechatRule $model
 */
$this->title = Yii::t('plugin_wechat', 'Message replies');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card card-secondary card-outline card-tabs plugin-wechat-reply-index">
    <div class="card-header p-0 pt-1">
        <nav class="navbar navbar-expand pb-0">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <?= Html::a(Yii::t('plugin_wechat', 'Keyword auto reply'), ['reply/index'], [
                            'class' => 'nav-link active'
                    ]) ?>
                </li>
                <li class="nav-item">
                    <?= Html::a(Yii::t('plugin_wechat', 'Non-text auto reply'), ['config/special-message'], [
                        'class' => 'nav-link'
                    ]) ?>
                </li>
                <li class="nav-item">
                    <?= Html::a(Yii::t('plugin_wechat', 'Subscribe & default reply'), ['reply/default'], [
                        'class' => 'nav-link'
                    ]) ?>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <?= Html::a('<i class="fas fa-plus-circle"></i> ' . Yii::t('plugin_wechat', 'Create rule'),
                        ['edit'],
                        ['class' => 'btn btn-xs btn-outline-primary']
                    ) ?>
                </li>
            </ul>
        </nav>
    </div>
    <div class="card-body pt-3 pl-0 pr-0">
        <div class="container">
            HH
        </div>
    </div>
</div>

