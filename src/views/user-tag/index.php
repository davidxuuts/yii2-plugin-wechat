<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\base\enums\ModalSizeEnum;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\grid\SerialColumn;
use davidxu\config\grid\ActionColumn;

/**
 * @var $this View
 * @var $dataProvider ActiveDataProvider;
 */

$this->title = Yii::t('plugin_wechat', 'User tags list');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plugin-wechat-user-index card card-outline card-secondary">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title); ?> </h4>
        <div class="card-tools">
            <?= Html::a('<i class="fas fa-plus-circle"></i> ' . Yii::t('plugin_wechat', 'Create tag'),
                ['ajax-edit'],
                [
                    'class' => 'btn btn-xs btn-outline-primary',
                    'title' => Yii::t('plugin_wechat', 'Create tag'),
                    'arial-label' => Yii::t('plugin_wechat', 'Create tag'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'data-modal-class' => ModalSizeEnum::SIZE_LARGE,
                ]
            ) ?>
            <?= Html::a('<i class="fas fa-cloud-download-alt"></i> '
                . Yii::t('plugin_wechat', 'Get tags'),
                ['get-all'],
                [
                    'id' => 'btn-get-tags',
                    'class' => 'btn btn-xs btn-outline-info',
                    'data-confirm' => Yii::t('plugin_wechat', 'Confirm to get tags from wechat server?'),
                    'title' => Yii::t('plugin_wechat', 'Get tags'),
                    'aria-label' => Yii::t('plugin_wechat', 'Get tags'),
                ]
            ) ?>
        </div>
    </div>
    <div class="card-body pt-3 pl-0 pr-0">
        <div class="container">
            <?= $this->render('../common/_search', [
                'placeholder' => Yii::t('plugin_wechat', 'Search tag name')
            ]) ?>
            <?php try {
                echo GridView::widget([
                    'options' => ['id' => 'plugin-wechat-users-grid'],
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered'],
                    'columns' => [
                        ['class' => SerialColumn::class],
                        'name',
                        'count',
                        [
                            'header' => Yii::t('app', 'Operate'),
                            'class' => ActionColumn::class,
                            'template' => '{ajax-edit} {destroy}'
                        ],
                    ],
                ]);
            } catch (Exception|Throwable $e) {
                echo YII_ENV_PROD ? null : $e->getMessage();
            } ?>
        </div>
    </div>
</div>
