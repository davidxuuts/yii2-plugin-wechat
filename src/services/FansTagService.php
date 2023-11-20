<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\base\enums\StatusEnum;
use davidxu\plugin\wechat\models\PluginWechatFans;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FansTagService extends BaseWechatService
{
    /**
     * Get all tags from WeChat Server side
     * It will return all tags in array or null if no tags
     * @return array|null
     * @throws TransportExceptionInterface
     */
    public function getTagList(): ?array
    {
        $response = $this->api->get('cgi-bin/tags/get');
        return isset($response['tags']) ? $response['tags'] : null;
    }

    /**
     * @param string $name
     * @param int|null $tag_id
     * @return array|null
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function editTag(string $name, ?int $tag_id = null): ?array
    {
        if ($tag_id !== null && $tag_id >= 0) {
            $response = $this->api->postJson('cgi-bin/tags/update', [
                'tag' => [
                    'name' => $name,
                    'id' => $tag_id,
                ]
            ]);
            $result = $response->isSuccessful() ? [
                'tag_id' => $tag_id,
                'name' => $name,
            ] : null;
//            $result = $response['errcode'] === 0 ? [
//                'tag_id' => $tag_id,
//                'name' => $name,
//            ] : null;
        } else {
            $response = $this->api->postJson('cgi-bin/tags/create', [
                'tag' => [
                    'name' => $name,
                ]
            ]);
            $result = isset($response['tag']) ? [
                'tag_id' => $response['tag']['id'],
                'name' => $response['tag']['name'],
            ] : null;
        }
        return $result;
    }

    /**
     * @param string|array $openids
     * @param array $tags
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function setUserTag(string|array $openids, array $tags): bool
    {
        $result['tagging'] = $result['untagging'] = true;

        if (is_string($openids)) {
            $openids = [$openids];
        }

        // Get exist tags for all openids
        /** @var PluginWechatFans $user */
        $users = PluginWechatFans::find()->where([
            'merchant_id' => $this->getMerchantId(),
            'openid' => $openids,
            'status' => StatusEnum::ENABLED,
        ])->all();
        foreach ($users as $user) {
            $currentTags = unserialize($user->tagid_list);
            if ($currentTags === $tags) {
                return true;
            }
            // Delete user tag
            if ($currentTags) {
                foreach ($currentTags as $tag) {
                    $params = [
                        'openid_list' => $openids,
                        'tagid' => $tag
                    ];
                    $response = $this->api->postJson('cgi-bin/tags/members/batchuntagging', $params);
                    $result['untagging'] = $response['errcode'] === 0;
                }
            }
            // Tag user
            if ($tags) {
                foreach ($tags as $tag) {
                    $params = [
                        'openid_list' => $openids,
                        'tagid' => $tag
                    ];
                    $response = $this->api->postJson('cgi-bin/tags/members/batchtagging', $params);
                    $result['tagging'] = $response['errcode'] === 0;
                }
            }
        }
        return $result['untagging'] && $result['tagging'];
    }
}
