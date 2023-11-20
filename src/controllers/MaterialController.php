<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\controllers;

use davidxu\base\helpers\ActionHelper;
use davidxu\plugin\wechat\components\BaseWechatController;
use davidxu\plugin\wechat\enums\WechatLinkTypeEnum;
use davidxu\plugin\wechat\enums\WechatMaterialTypeEnum;
use davidxu\plugin\wechat\enums\WechatMediaTypeEnum;
use davidxu\plugin\wechat\forms\MaterialForm;
use davidxu\plugin\wechat\models\PluginWechatMaterial;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Yii;
use yii\base\Exception;
use yii\base\ExitException;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;

/**
 * MaterialController implements the CRUD actions for WeChat Material model.
 */
class MaterialController extends BaseWechatController
{
    public string|ActiveRecordInterface|null $modelClass = PluginWechatMaterial::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        return [];
    }

    /**
	 * Lists all Material models.
     * @return string
     */
	public function actionIndex(): string
    {
        $type = Yii::$app->request->get('type', WechatMediaTypeEnum::MEDIA_TYPE_IMAGE);
        if (!in_array($type, WechatMediaTypeEnum::getPermanentMediaTypeKeys())) {
            $type = WechatMediaTypeEnum::MEDIA_TYPE_IMAGE;
        }
        $query = $this->modelClass::find()
            ->where(['merchant_id' => $this->merchant_id])
            ->andFilterWhere(['media_type' => $type]);
        $key = trim(Yii::$app->request->get('key', ''));
        if ($key) {
            $query->andFilterWhere(['or',
                ['like', 'file_name', $key],
                ['like', 'media_id', $key],
                ['like', 'description', $key],
            ]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'currentType' => $type,
        ]);
	}

    /**
     * @return mixed
     * @throws ClientExceptionInterface
     * @throws ExitException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function actionAjaxEdit(): mixed
    {
        $type = Yii::$app->request->get('type', 'image');
        if (!in_array($type, WechatMediaTypeEnum::getKeys())) {
            $type = WechatMediaTypeEnum::MEDIA_TYPE_IMAGE;
        }
        $acceptedFiles = match ($type) {
            WechatMediaTypeEnum::MEDIA_TYPE_IMAGE => 'bmp,png,jpeg,jpg,gif',
            WechatMediaTypeEnum::MEDIA_TYPE_VIDEO => 'mp4',
            WechatMediaTypeEnum::MEDIA_TYPE_VOICE => 'mp3,wma,wav,amr',
            WechatMediaTypeEnum::MEDIA_TYPE_THUMB => 'jpg',
        };
        $model = new MaterialForm(['scenario' => MaterialForm::SCENARIO_NOT_STORE_IN_DB]);
        ActionHelper::activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            $material = new PluginWechatMaterial();
            $info = ($cache = Yii::$app->cache) ? $cache->get($model->material_id) : null;
            if ($info) {
                $material->attributes = $info;
            }
            $material->material_type = (int)$model->material_type;
            $material->local_url = $model->material_id;
            $material->merchant_id = $this->merchant_id;
            $material->file_name = $info['name'];
            $material->link_type = WechatLinkTypeEnum::LINK_TYPE_LOCAL;
            $material->media_type = $type;
            $response = Yii::$app->wechatService->materialService->uploadMaterial($material);
            if ($response->isSuccessful()) {
                $material->media_id = $response['media_id'];
                $material->media_url = $response['url'];
                $material->update_time = time();
            }
            $message = $response->isFailed() ? $response['errmsg'] : null;
            if ($material->save() && $response->isSuccessful()) {
                if ($info) {
                    TagDependency::invalidate($cache, $info['path'] . $info['hash']);
                }
                return ActionHelper::message(Yii::t('base', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer));
            }
            return ActionHelper::message(ActionHelper::getError($material) ?? $message,
                    $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'type' => $type,
            'acceptedFiles' => $acceptedFiles,
            'existFiles' => [],
            'materialTypeRadioList' => WechatMaterialTypeEnum::getMap(),
        ]);
    }

    /**
     * Get all materials from WeChat server
     * @return mixed
     * @throws BadResponseException|ClientExceptionInterface|DecodingExceptionInterface|Exception
     * @throws GuzzleException|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function actionGetPermanent(): mixed
    {
        $type = Yii::$app->request->get('type', 'image');
        if (!in_array($type, WechatMediaTypeEnum::getKeys())) {
            $type = WechatMediaTypeEnum::MEDIA_TYPE_IMAGE;
        }
        $result = Yii::$app->wechatService->materialService->getPermanentMaterialListByType($type);
        return true === $result
            ? ActionHelper::message(Yii::t('plugin_wechat', 'Saved successfully'),
                $this->redirect(Yii::$app->request->referrer))
            : ActionHelper::message(
                (YII_ENV_PROD
                    ? Yii::t('plugin_wechat', 'Save failed')
                    : 'Wechat Error: ' . $result['errcode'] . ' ' . $result['errmsg']),
                $this->redirect(Yii::$app->request->referrer), 'error');
    }
}
