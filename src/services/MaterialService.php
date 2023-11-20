<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\base\models\Attachment;
use davidxu\plugin\wechat\enums\WechatMaterialTypeEnum;
use davidxu\plugin\wechat\enums\WechatLinkTypeEnum;
use davidxu\plugin\wechat\enums\WechatMediaTypeEnum;
use davidxu\plugin\wechat\models\PluginWechatMaterial;
use davidxu\plugin\wechat\models\PluginWechatMaterialNews;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;
use EasyWeChat\Kernel\HttpClient\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use Yii;
use yii\db\ActiveRecordInterface;
use yii\db\Exception;

class MaterialService extends BaseWechatService
{
    const CACHE_TAG = 'material-service-dependency-count';

    /**
     * Get WeChat permanent material count
     *
     * @return array
     * @throws BadResponseException|ClientExceptionInterface|DecodingExceptionInterface
     * @throws RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function getPermanentMaterialCount(): array
    {
        if ($cache = $this->getCache()) {
            $data = $cache->getOrSet([Yii::$app->id, __CLASS__, 'plugin_wechat_material_count'], function () {
                $response = $this->api->get('cgi-bin/material/get_materialcount')->toArray();
                $count[WechatMediaTypeEnum::MEDIA_TYPE_NEWS] = $response['news_count'];
                $count[WechatMediaTypeEnum::MEDIA_TYPE_VIDEO] = $response['video_count'];
                $count[WechatMediaTypeEnum::MEDIA_TYPE_VOICE] = $response['voice_count'];
                $count[WechatMediaTypeEnum::MEDIA_TYPE_IMAGE] = $response['image_count'];
                return $count;
            }, 3600 * 24, new TagDependency(['tags' => self::CACHE_TAG]));
        } else {
            $response = $this->api->get('cgi-bin/material/get_materialcount')->toArray();
            $data[WechatMediaTypeEnum::MEDIA_TYPE_NEWS] = $response['news_count'];
            $data[WechatMediaTypeEnum::MEDIA_TYPE_VIDEO] = $response['video_count'];
            $data[WechatMediaTypeEnum::MEDIA_TYPE_VOICE] = $response['voice_count'];
            $data[WechatMediaTypeEnum::MEDIA_TYPE_IMAGE] = $response['image_count'];
        }
        return $data;
    }

    /**
     * Get all permanent materials from WeChat Server side by offset
     * If set model, will store all records in model DB
     * @param string $type Media type
     * @param int $offset exists count
     * @param int $count How many count fetch from WeChat server
     * @param string|ActiveRecord|ActiveRecordInterface $modelClass Material ModelClass
     * @param string|ActiveRecord|ActiveRecordInterface $modelClassNews Material news ModelClass
     * @return Response|ResponseInterface|bool|array
     * @throws BadResponseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws GuzzleException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \yii\base\Exception
     */
    public function getPermanentMaterialListByType(
        string $type,
        int $offset = 0,
        int $count = 20,
        string|ActiveRecordInterface|ActiveRecord $modelClass = PluginWechatMaterial::class,
        string|ActiveRecordInterface|ActiveRecord $modelClassNews = PluginWechatMaterialNews::class
    ): Response|ResponseInterface|bool|array
    {
        $result = true;
        $merchant_id = $this->getMerchantId();
        $count = max(min($count, 20), 1);
        if ($offset <= 0) {
            $offset = PluginWechatMaterial::find()->where([
                'merchant_id' => $merchant_id,
                'material_type' => WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT,
                'media_type' => $type,
            ])->count();
        }
        // from WeChat server
        if (($totalCount = $this->getPermanentMaterialCount()[$type]) > 0) {
            for ($i = 0; $i < ceil($totalCount / $count); $i++) {
                $response = $this->api->postJson('cgi-bin/material/batchget_material', [
                    'type' => $type,
                    'offset' => $offset,
                    'count' => $count,
                ]);
                if ($response->isFailed()) {
                    return $response;
                }
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (isset($response['item']) && count($items = $response['item'])) {
                        $media_id = [];
                        foreach ($items as $item) {
                            $media_id[] = $item['media_id'];
                        }
                        PluginWechatMaterial::deleteAll([
                            'merchant_id' => $merchant_id,
                            'media_type' => $type,
                            'material_type' => WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT,
                            'media_id' => $media_id
                        ]);
                        $result = $this->savePermanentMaterial($items, $type, $modelClass, $modelClassNews);
                    }
                    $transaction->commit();
                } catch (Exception $exception) {
                    $transaction->rollBack();
                    return $exception->getMessage();
                }
                $offset = ($i + 1) * $count;
            }
            if ($cache = Yii::$app->cache) {
                TagDependency::invalidate($cache, self::CACHE_TAG);
            }
        }
        return $result;
    }

    /**
     * @param array|ActiveRecordInterface|ActiveRecord|PluginWechatMaterial $material
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function uploadMaterial(array|ActiveRecordInterface|ActiveRecord|PluginWechatMaterial $material): ResponseInterface|Response
    {
        $params = [
            'media' => File::fromPath(Yii::getAlias('@webroot' . '/..' . $material->local_url), $material->file_name),
        ];
        if ($material->material_type === WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT
            && $material->media_type === WechatMediaTypeEnum::MEDIA_TYPE_VIDEO) {
            $params['description'] = [
                'title' => $material->file_name,
                'introduction' => $material->file_name,
            ];
        }
        $options = Form::create($params)->toArray();
        $url = $material->material_type === WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT
            ? 'cgi-bin/material/add_material?type=' . $material->media_type
            : 'cgi-bin/media/upload?type=' . $material->media_type;
        return $this->api->post($url, $options);
    }
//
//    /**
//     * @param array|ActiveRecordInterface|ActiveRecord|Attachment $material
//     * @return Response|ResponseInterface
//     * @throws TransportExceptionInterface
//     */
//    public function uploadPermanentMaterial(array|ActiveRecordInterface|ActiveRecord|Attachment $material): ResponseInterface|Response
//    {
//        $params = [
//            'media' => File::fromPath(Yii::getAlias('@webroot' . $material['path'])),
//        ];
//        if ($material['media_type'] === WechatMediaTypeEnum::MEDIA_TYPE_VIDEO) {
//            $params['description'] = [
//                'title' => $material['name'],
//                'introduction' => $material['name'],
//            ];
//        }
//        $options = Form::create($params)->toArray();
//        return $this->api->post('cgi-bin/material/add_material?type=' . $material['media_type'], $options);
//    }
//
//    /**
//     * @param array|ActiveRecordInterface|ActiveRecord|Attachment $material
//     * @return Response|ResponseInterface
//     * @throws TransportExceptionInterface
//     */
//    public function uploadTemporaryMaterial(array|ActiveRecordInterface|ActiveRecord|Attachment $material): ResponseInterface|Response
//    {
//        $params = [
//            'media' => File::fromPath(Yii::getAlias('@webroot' . $material['path'])),
//        ];
//        $options = Form::create($params)->toArray();
//        return $this->api->post('cgi-bin/media/upload?type=' . $material['media_type'], $options);
//    }

    /**
     * Insert materials
     * @param array $materials
     * @param string $type
     * @param string|ActiveRecordInterface|ActiveRecord $modelClass
     * @param string|ActiveRecordInterface|ActiveRecord $modelClassNews
     * @return bool
     * @throws BadResponseException|ClientExceptionInterface|Exception|GuzzleException
     * @throws RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface|\yii\base\Exception
     */
    private function savePermanentMaterial(
        array $materials,
        string $type,
        string|ActiveRecordInterface|ActiveRecord $modelClass,
        string|ActiveRecordInterface|ActiveRecord $modelClassNews
    ): bool
    {
        $merchant_id = $this->getMerchantId();
        $fields = ['merchant_id', 'file_name', 'media_type', 'media_id', 'media_url',
            'local_url', 'update_time', 'description', 'material_type', 'link_type'];
        $values = [];
        foreach ($materials as $material) {
            $media_url = isset($material['url']) && $material['url'] ? $material['url'] : null;
            $media_id = isset($material['media_id']) && $material['media_id'] ? $material['media_id'] : null;
            $file_name = isset($material['name']) && $material['name'] ? $material['name'] : null;
            $file_name_arr = $file_name ? explode('.', $file_name) : [];
            $file_extension = !empty($file_name_arr)
                ? (($count = count($file_name_arr)) > 1 ? $file_name_arr[$count - 1] : '')
                : '';
            $local_url = $type !== WechatMediaTypeEnum::MEDIA_TYPE_NEWS
                ? $this->getPermanentMaterialByMediaId($media_id, $type, $file_extension)
                : null;
            $values[] = [
                $merchant_id,
                $file_name,
                $type,
                $media_id,
                $media_url,
                $local_url,
                isset($material['update_time']) && $material['update_time'] ? $material['update_time'] : null,
                isset($material['description']) && $material['description'] ? $material['description'] : null,
                WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT,
                WechatLinkTypeEnum::LINK_TYPE_WECHAT
            ];
        }
        $sql = Yii::$app->db->createCommand()->batchInsert($modelClass::tableName(), $fields, $values);
        $sql->execute();

        // Update news contents
        foreach ($materials as $material) {
            if ($type === WechatMediaTypeEnum::MEDIA_TYPE_NEWS
                && isset($material['content'])
                && isset($material['content']['news_item'])) {
                $materialId = $modelClass::find()->where([
                    'merchant_id' => $merchant_id,
                    'media_id' => isset($material['media_id']) && $material['media_id'] ? $material['media_id'] : null,
                    'material_type' => WechatMaterialTypeEnum::MATERIAL_TYPE_PERMANENT,
                    'media_type' => WechatMediaTypeEnum::MEDIA_TYPE_NEWS,
                ])->one();
                $order = 1;
                $valuesNews = [];
                $fieldsNews = ['material_id', 'title', 'thumb_media_id', 'thumb_url', 'author', 'digest', 'show_cover_pic', 'order', 'content', 'content_source_url'];
                foreach ($material['content']['news_item'] as $item) {
                    $valuesNews[] = [
                        $materialId,
                        isset($item['title']) && $item['title'] ? $item['title'] : null,
                        isset($item['thumb_media_id']) && $item['thumb_media_id'] ? $item['thumb_media_id'] : null,
                        isset($item['url']) && $item['url'] ? $item['url'] : null,
                        isset($item['author']) && $item['author'] ? $item['author'] : null,
                        isset($item['digest']) && $item['digest'] ? $item['digest'] : null,
                        isset($item['show_cover_pic']) && $item['show_cover_pic'] ? $item['show_cover_pic'] : 0,
                        $order,
                        isset($item['content']) && $item['content'] ? $item['content'] : null,
                        isset($item['content_source_url']) && $item['content_source_url'] ? $item['content_source_url'] : null,
                    ];
                    $order++;
                }
                $sql = Yii::$app->db->createCommand()->batchInsert($modelClassNews::tableName(), $fieldsNews, $valuesNews);
                $sql->execute();
            }
        }
        return true;
    }

    /**
     * @param string $mediaId Media ID
     * @param string $type Media type
     * @param ?string $extension File extension
     * @return ?string
     * @throws ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface
     * @throws BadResponseException|\yii\base\Exception|TransportExceptionInterface|GuzzleException
     */
    private function getPermanentMaterialByMediaId(string $mediaId,
                                          string $type,
                                          ?string $extension = null,
                                          ): ?string
    {
        $fileName = Yii::$app->security->generateRandomString() . '.' . $extension;
        $response = $this->api->postJson('cgi-bin/material/get_material', ['media_id' => $mediaId]);
        if ($response->isFailed()) {
            return null;
        }
        if ($type === WechatMediaTypeEnum::MEDIA_TYPE_VIDEO) {
            $downUrl = isset($response['down_url']) && ($url = $response['down_url']) ? $url : null;
            return $downUrl ? $this->savePermanentMaterialToLocal($type, $fileName, $response) : null;
        }
        return $this->savePermanentMaterialToLocal($type, $fileName, $response);
    }

    /**
     * @param string $type
     * @param string $fileName
     * @param Response|ResponseInterface $response
     * @return ?string
     * @throws BadResponseException|ClientExceptionInterface|RedirectionExceptionInterface
     * @throws ServerExceptionInterface|TransportExceptionInterface|GuzzleException
     */
    private function savePermanentMaterialToLocal(string $type,
                                         string $fileName,
                                         ResponseInterface|Response $response): ?string
    {
        if (isset(Yii::$app->utility) && ($url = Yii::$app->utility->config('local_store_base_url')) !== null) {
            $localStorePath = $url;
        } else {
            $localStorePath = '/uploads';
        }
        if (str_starts_with($localStorePath, DIRECTORY_SEPARATOR)) {
            $storePath = Yii::getAlias('@webroot' . $localStorePath) . DIRECTORY_SEPARATOR . $type;
            $urlPath = Yii::getAlias('@web' . $localStorePath) . DIRECTORY_SEPARATOR . $type;
        } else {
            $storePath = Yii::getAlias('@webroot' . DIRECTORY_SEPARATOR . $localStorePath) . DIRECTORY_SEPARATOR . $type;
            $urlPath = Yii::getAlias('@web' . DIRECTORY_SEPARATOR . $localStorePath) . DIRECTORY_SEPARATOR . $type;
        }

        if (!is_dir($storePath)) {
            @mkdir($storePath, 0777, true);
        }
        if (is_dir($storePath)) {
            if ($type === WechatMediaTypeEnum::MEDIA_TYPE_VIDEO) {
                $guzzleClient = new Client();
                $res = $guzzleClient->get($response['down_url'], ['sink' => $storePath . DIRECTORY_SEPARATOR . $fileName]);
                return $res->getStatusCode() < 400 ? $urlPath . DIRECTORY_SEPARATOR . $fileName : null;
            }
            $response->saveAs($storePath . DIRECTORY_SEPARATOR . $fileName);
            return $urlPath . DIRECTORY_SEPARATOR . $fileName;
        }
        return null;
    }
}
