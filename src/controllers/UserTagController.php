<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\controllers;

use davidxu\base\helpers\ActionHelper;
use davidxu\plugin\wechat\components\BaseWechatController;
use davidxu\plugin\wechat\models\PluginWechatFansTag;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Yii;
use yii\base\ExitException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecordInterface;

/**
 * UserTagController implements the CRUD actions for WeChat FansTag model.
 */
class UserTagController extends BaseWechatController
{
    public string|ActiveRecordInterface|null $modelClass = PluginWechatFansTag::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        return [];
    }

    /**
	 * Lists all User models.
     * @return string
     */
	public function actionIndex(): string
    {
        $query = $this->modelClass::find()->where(['merchant_id' => $this->merchant_id]);
        $key = trim(Yii::$app->request->get('key', ''));
        if ($key) {
            $query->andFilterWhere(['like', 'name', $key]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                    'updated_at' => SORT_DESC,
                ],
            ],
        ]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
        ]);
	}

    /**
     * @return mixed|string
     * @throws ExitException
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function actionAjaxEdit(): mixed
    {
        $id = Yii::$app->request->get('id');
        /** @var PluginWechatFansTag $model */
        $model = $this->findModel($id);
        if (!($model->isNewRecord) && in_array($model->tag_id, [0, 1, 2])) {
            return ActionHelper::message(Yii::t('plugin_wechat', 'Can not modify default tag'),
                $this->redirect(Yii::$app->request->referrer), 'error');
        }
        ActionHelper::activeFormValidate($model);
        $oldName = $model->name;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->name === $oldName) {
                ActionHelper::message(Yii::t('base', 'Saved successfully'),
                    $this->redirect(Yii::$app->request->referrer));
            }
            $result = Yii::$app->wechatService->fansTagService->editTag(
                $model->name, $model->isNewRecord ? null : $model->tag_id);
            if ($result) {
                $model->name = $result['name'];
                $model->tag_id = $result['tag_id'];
                return $model->save()
                    ? ActionHelper::message(Yii::t('base', 'Saved successfully'),
                        $this->redirect(Yii::$app->request->referrer))
                    : ActionHelper::message(ActionHelper::getError($model),
                        $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return ActionHelper::message(Yii::t('base', 'Save failed'),
                $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * Get all tags from WeChat server
     * @return mixed
     * @throws TransportExceptionInterface
     */
    public function actionGetAll(): mixed
    {
//        ActionHelper::toast(Yii::t('plugin_wechat', 'Synchronize all fans in progress',
//            ActionHelper::MESSAGE_INFO));
        $existTags = PluginWechatFansTag::find()
            ->select('tag_id')
            ->where(['merchant_id' => $this->merchant_id])
            ->column();
        $allTags = Yii::$app->wechatService->fansTagService->getTagList();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // update exist tags and insert new ones
            foreach ($allTags as $tag) {
                if (in_array($tag['id'], $existTags)) {
                    // Update tag
                    $model = PluginWechatFansTag::findOne(['tag_id' => $tag['id']]);
                } else {
                    // Insert tag
                    $model = new PluginWechatFansTag();
                    $model->merchant_id = $this->merchant_id;
                    $model->tag_id = $tag['id'];
                }
                $model->name = $tag['name'];
                $model->count = $tag['count'];
                $model->save();
            }
            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollBack();
            return ActionHelper::message($exception->getMessage(),
                $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return ActionHelper::message(Yii::t('plugin_wechat', 'Synchronize all tags successfully'),
            $this->redirect(Yii::$app->request->referrer));
    }
}
