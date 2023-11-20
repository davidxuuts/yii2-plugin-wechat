<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\plugin\wechat\enums\SubscriberStatusEnum;
use davidxu\plugin\wechat\models\PluginWechatFans;
use yii\bootstrap4\ActiveForm;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\web\View;
use davidxu\base\enums\ModalSizeEnum;
use yii\grid\SerialColumn;
use davidxu\config\grid\ActionColumn;
use davidxu\plugin\wechat\helpers\FormatHelper;

/**
 * @var $this View
 * @var $dataProvider ActiveDataProvider;
 */

$this->title = Yii::t('plugin_wechat', 'Fans list');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plugin-wechat-user-index card card-outline card-secondary">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h4 class="card-title"><?= Html::encode($this->title); ?> </h4>
                <p class="text-right text-info">只能通过网页授权模式获取用户头像和昵称</p>
            </div>
            <div class="col">
                <div class="card-tools">
                    <div class="row justify-content-end text-right">
                        <div class="col">
                            <?= Html::a('<i class="fas fa-cloud-download-alt"></i> '
                                . Yii::t('plugin_wechat', 'Synchronize all fans'),
                                ['get-all'],
                                [
                                    'id' => 'btn-sync-all-openids',
                                    'class' => 'btn btn-xs btn-outline-info',
                                    'data-confirm' => Yii::t('plugin_wechat', 'Confirm to synchronize all fans?'),
                                    'title' => Yii::t('plugin_wechat', 'Synchronize all fans'),
                                    'aria-label' => Yii::t('plugin_wechat', 'Synchronize all fans'),
                                ]
                            ) ?>
                        </div>
                        <?php if (Yii::$app->user->can('/main/index')): ?>
                        <div class="col-3">
                            <?php $form = ActiveForm::begin(['action' => ['get-selected']]);
                            echo Html::hiddenInput('openids', null, ['id' => 'input-wechat-openids']);
                            echo Html::submitButton('<i class="fas fa-sync-alt"></i> '
                                . Yii::t('plugin_wechat', 'Synchronize selected fans'),
                                [
                                    'id' => 'btn-select-openids',
                                    'class' => 'btn btn-xs btn-outline-info',
                                    'title' => Yii::t('plugin_wechat', 'Synchronize selected fans'),
                                    'aria-label' => Yii::t('plugin_wechat', 'Synchronize selected fans'),
                                ]
                            );
                            ActiveForm::end(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body pt-3 pl-0 pr-0">
        <div class="container">
            <?= $this->render('../common/_search', [
                'placeholder' => Yii::t('plugin_wechat', 'Search openid/unionid/nickname')
            ]) ?>
            <?php try {
                echo GridView::widget([
                    'options' => ['id' => 'plugin-wechat-users-grid'],
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered'],
                    'columns' => [
                        ['class' => CheckboxColumn::class],
                        ['class' => SerialColumn::class],
                        [
                            'attribute' => 'head_portrait',
                            'format' => 'RAW',
                            'value' => static function ($model) {
                                /** @var PluginWechatFans $model */
                                return $model->head_portrait ? Html::img($model->head_portrait, [
                                    'class' => 'img-bordered-sm img-circle img-size-32 mr-2'
                                ]) : '<span class="text-gray"><i class="fas fa-user-circle"></i></span>';
                            }
                        ],
                        [
                            'label' => Yii::t('plugin_wechat', 'Nickname(Remark)'),
                            'format' => 'RAW',
                            'value' => static function($model) {
                                /** @var PluginWechatFans $model */
                                $nickname = $model->nickname ?? Html::tag('span',
                                    Yii::t('yii', '(not set)'),
                                    ['class' => 'not-set']);
                                $remark = $model->remark ? '(' . $model->remark . ')'
                                    : Html::tag('span',
                                        Yii::t('yii', '(not set)'), ['class' => 'not-set']);
                                return $nickname . ' ' . $remark;
                            }
                        ],
                        [
                            'attribute' => 'gender',
                            'format' => 'RAW',
                            'value' => static function ($model) {
                                /** @var PluginWechatFans $model */
                                return FormatHelper::Gender($model->gender);
                            }
                        ],
                        [
                            'attribute' => 'subscribe',
                            'format' => 'RAW',
                            'value' => static function ($model) {
                                /** @var PluginWechatFans $model */
                                return FormatHelper::Subscriber($model->subscribe);
                            }
                        ],
                        [
                            'label' => Yii::t('plugin_wechat', 'Subscribe/unsubscribe time'),
                            'value' => static function ($model) {
                                /** @var PluginWechatFans $model */
                                return $model->subscribe === SubscriberStatusEnum::UNSUBSCRIBED
                                    ? date('Y-m-d', $model->unsubscribe_time)
                                    : date('Y-m-d', $model->subscribe_time);
                            }
                        ],
                        'openid',
                        [
                            'header' => Yii::t('app', 'Operate'),
                            'class' => ActionColumn::class,
                            'template' => '{remark} {tag}',
                            'buttons' => [
                                'remark' => static function ($url, $model, $key) {
                                    /** @var PluginWechatFans $model */
                                    return Html::a('<i class="fas fa-marker"></i>',
                                        ['remark', 'id' => $model->id],
                                        [
                                            'title' => Yii::t('plugin_wechat', 'Mark user'),
                                            'aria-label' => Yii::t('plugin_wechat', 'Mark user'),
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal',
                                            'data-modal-class' => ModalSizeEnum::SIZE_LARGE,
                                        ]
                                    );
                                },
                                'tag' => static function ($url, $model, $key) {
                                    /** @var PluginWechatFans $model */
                                    return Html::a('<i class="fas fa-tags"></i>',
                                        $url,
                                        [
                                            'title' => Yii::t('plugin_wechat', 'User tags'),
                                            'aria-label' => Yii::t('plugin_wechat', 'User tags'),
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal',
                                            'data-modal-class' => ModalSizeEnum::SIZE_LARGE,
                                        ]
                                    );
                                },
                            ],
                        ],
                    ],
                ]);
            } catch (Exception|Throwable $e) {
                echo YII_ENV_PROD ? null : $e->getMessage();
            } ?>
        </div>
    </div>
</div>
<?php
$errorMsg = Yii::t('plugin_wechat', 'Please select one record at least');
$js = /** @lang JavaScript */ <<<JS_CHECKBOX_COLUMN
$('#btn-sync-all-openids').on('click', function () {
    console.log('clicked')
})
$('#btn-select-openids').on('click', function () {
    let selectedOpenids = $('#plugin-wechat-users-grid').yiiGridView('getSelectedRows')
    $('#input-wechat-openids').val(selectedOpenids)
    if (selectedOpenids.length <=0) {
        errorMsg('{$errorMsg}')
        return false
    } else {
        return true
    }
})

JS_CHECKBOX_COLUMN;
$this->registerJs($js);
