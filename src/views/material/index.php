<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

use davidxu\base\assets\FancyUIAsset;
use davidxu\base\enums\ModalSizeEnum;
use davidxu\plugin\wechat\enums\WechatLinkTypeEnum;
use davidxu\plugin\wechat\enums\WechatMaterialTypeEnum;
use davidxu\plugin\wechat\enums\WechatMediaTypeEnum;
use davidxu\plugin\wechat\models\PluginWechatMaterial;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\grid\SerialColumn;
use davidxu\config\grid\ActionColumn;
use davidxu\config\helpers\Html;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var PluginWechatMaterial $model
 * @var string $currentType
 */
FancyUIAsset::register($this);
$this->title = Yii::t('plugin_wechat', 'Material list');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card card-secondary card-outline card-tabs">
    <div class="card-header p-0 pt-1">
        <nav class="navbar navbar-expand pb-0">
            <ul class="nav nav-tabs">
                <?php foreach (WechatMediaTypeEnum::getPermanentMediaTypeMap() as $type => $value) {
                    $linkClass = 'nav-link' . ($currentType === $type ? ' active' : '');
                    $content = Html::a(Html::encode($value), ['index', 'type' => $type], ['class' => $linkClass]);
                    echo Html::tag('li', $content, ['class' => 'nav-item']);
                } ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item pr-2">
                    <?= Html::a('<i class="fas fa-plus-circle"></i> '
                        . Yii::t('plugin_wechat', 'Create material'),
                        ['ajax-edit',
                            'type' => $currentType !== WechatMediaTypeEnum::MEDIA_TYPE_NEWS
                                ? $currentType
                                : WechatMediaTypeEnum::MEDIA_TYPE_THUMB
                        ], [
                            'class' => 'btn btn-xs btn-outline-info',
                            'title' => Yii::t('plugin_wechat', 'Create material'),
                            'aria-label' => Yii::t('plugin_wechat', 'Create material'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal',
                            'data-modal-class' => ModalSizeEnum::SIZE_LARGE,
                        ]
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= Html::a('<i class="fas fa-cloud-download-alt"></i> '
                        . Yii::t('plugin_wechat', 'Get permanent materials'),
                        ['get-permanent', 'type' => $currentType],
                        [
                            'id' => 'btn-get-materials',
                            'class' => 'btn btn-xs btn-outline-info',
                            'data-confirm' => Yii::t('plugin_wechat', 'Confirm to get permanent materials from wechat server?'),
                            'title' => Yii::t('plugin_wechat', 'Get permanent materials'),
                            'aria-label' => Yii::t('plugin_wechat', 'Get permanent materials'),
                        ]
                    ) ?>
                </li>
            </ul>


        </nav>
    </div>
    <div class="card-body pt-3 pl-0 pr-0">
        <div class="container">
            <?= $this->render('_search', [
                'placeholder' => Yii::t('plugin_wechat', 'Search file name/media id/description')
            ]) ?>
            <?php try {
                echo GridView::widget([
                    'options' => ['id' => 'plugin-wechat-materials-grid'],
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-bordered'],
                    'columns' => [
                        ['class' => SerialColumn::class],
                        [
                            'attribute' => 'file_name',
                            'format' => 'RAW',
                            'value' => static function($model) {
                                /** @var PluginWechatMaterial $model */
                                $text = match($model->media_type) {
                                    WechatMediaTypeEnum::MEDIA_TYPE_VIDEO => '<i class="fas fa-file-video"></i> ',
                                    WechatMediaTypeEnum::MEDIA_TYPE_VOICE => '<i class="fas fa-file-audio"></i> ',
                                    WechatMediaTypeEnum::MEDIA_TYPE_IMAGE => '<i class="fas fa-file-image"></i> ',
                                    WechatMediaTypeEnum::MEDIA_TYPE_NEWS =>  '<i class="fas fa-file-alt"></i> ',
                                    default => '<i class="fas fa-file-archive"></i> ',
                                };
                                return $model->media_type === WechatMediaTypeEnum::MEDIA_TYPE_VOICE
                                    ? Html::a($text. Html::encode($model->file_name), $model->local_url, [
                                        'data-src' => $model->local_url,
                                        'data-type' => "audio",
                                        'data-fancybox' => 'fancyboxGallery',
                                        'data-caption' =>Html::encode($model->file_name),
                                    ])
                                        : Html::a($text . Html::encode($model->file_name),
                                    $model->local_url, [
                                    'data-fancybox' => 'fancyboxGallery',
                                    'data-caption' =>Html::encode($model->file_name),
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'material_type',
                            'value' => static function($model) {
                                /** @var PluginWechatMaterial $model */
                                return WechatMaterialTypeEnum::getValue($model->material_type);
                            }
                        ],
                        [
                            'attribute' => 'link_type',
                            'value' => static function($model) {
                                /** @var PluginWechatMaterial $model */
                                return WechatLinkTypeEnum::getValue($model->link_type);
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'RAW',
                            'value' => static function($model) {
                                /** @var PluginWechatMaterial $model */
                                return Html::displayStatus($model->status);
                            }
                        ],
                        [
                            'header' => Yii::t('app', 'Operate'),
                            'class' => ActionColumn::class,
                            'template' => '{destroy}'
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
$infoMsg = Yii::t('plugin_wechat', 'Get permanent materials from wechat server in progress...');
$js = /** @lang JavaScript */ <<<JS_GET_PERMANENT_MATERIALS
$('#btn-get-materials').on('click', function (){
    infoMsg('{$infoMsg}')
})
JS_GET_PERMANENT_MATERIALS;
$this->registerJs($js);
