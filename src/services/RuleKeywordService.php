<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\plugin\wechat\services;

use davidxu\base\enums\StatusEnum;
use davidxu\plugin\wechat\enums\WechatReplyRuleModeEnum;
use davidxu\plugin\wechat\enums\WechatRuleKeywordEnum;
use davidxu\plugin\wechat\models\PluginWechatRule;
use davidxu\plugin\wechat\models\PluginWechatRuleKeyword;

class RuleKeywordService extends BaseWechatService
{
    /**
     * Keyword reply
     * @param string $content
     * @return array|bool
     */
    public function match(string $content): bool|array
    {
        /**
         * @var PluginWechatRuleKeyword $keyword
         */
        $keyword = PluginWechatRuleKeyword::find()->where([
            'or',
            ['and', '{{type}}=:typeMatch', '{{content}}=:content'],
            ['and', '{{type}}=:typeInclude', 'INSTR(:content, {{content}}) > 0'],
            ['and', '{{type}}=:typeRegular', ':content REGEXP {{content}}']
        ])->addParams([
            ':content' => $content,
            ':typeMatch' => WechatRuleKeywordEnum::TYPE_MATCH,
            ':typeInclude' => WechatRuleKeywordEnum::TYPE_INCLUDE,
            ':typeRegular' => WechatRuleKeywordEnum::TYPE_REGULAR,
        ])->andWhere(['status' => StatusEnum::ENABLED])
            ->orderBy(['order' => SORT_DESC, 'id' => SORT_DESC])
            ->one();
        if (!$keyword) {
            return false;
        }

        // Search take over
        $takeoverKeyword = PluginWechatRuleKeyword::find()
            ->where(['type' => WechatRuleKeywordEnum::TYPE_TAKE_OVER, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['>=', 'order', $keyword->order])
            ->orderBy(['order' => SORT_DESC, 'id' => SORT_DESC])
            ->one();
        $takeoverKeyword && $keyword = $takeoverKeyword;

        /* @var $model PluginWechatRule */
        $model = PluginWechatRule::findOne($keyword->rule_id);

        switch ($keyword->rule->mode) {
            case  WechatReplyRuleModeEnum::RULE_MODE_TEXT:
                return [
                    'MsgType' => 'text',
                    'Content' => $model->data,
                ];
            case  WechatReplyRuleModeEnum::RULE_MODE_NEWS:
                $news = $model->materialNews;
                if ($news) {
                    $newsList = [
                        'MsgType' => 'news',
                        'ArticleCount' => count($news),
                    ];
                    $articleItems = [];
                    foreach ($news as $item) {
                        $articleItems[]['item'] = [
                            'Title' => $item->title,
                            'Description' => $item->digest,
                            'PicUrl' => $item->thumb_url,
                            'Url' => $item->material->media_url,
                        ];
                    }
                    $newsList['Articles'] = $articleItems;
                    return $newsList;
                }
                return false;
            case  WechatReplyRuleModeEnum::RULE_MODE_IMAGE:
                return [
                    'MsgType' => 'image',
                    'Image' => [
                        'MediaId' => $model->data
                    ],
                ];
            case WechatReplyRuleModeEnum::RULE_MODE_VIDEO:
                return [
                    'MsgType' => 'video',
                    'Video' => [
                        'MediaId' => $model->materialVideo->media_id,
                        'Title' => $model->materialVideo->file_name,
                        'Description' => $model->materialVideo->description,
                    ],
                ];
            case WechatReplyRuleModeEnum::RULE_MODE_VOICE:
                return [
                    'MsgType' => 'voice',
                    'Voice' => [
                        'MediaId' => $model->materialVoice->media_id,
                    ],
                ];
            case WechatReplyRuleModeEnum::RULE_MODE_MUSIC:
                return [
                    'MsgType' => 'music',
                    'Music' => [
                        'ThumbMediaId' => $model->materialMusic->media_id,
                        'Title' => $model->materialMusic->file_name,
                        'Description' => $model->materialMusic->description,
                        'MusicURL' => $model->materialMusic->media_url,
                    ],
                ];
            default :
                return false;
        }
    }

    /**
     * Get Rule Keyword type array
     *
     * @param array $ruleKeyword
     * @return array|array[]
     */
    public function getType(array $ruleKeyword = []): array
    {
        $ruleKeywords = [
            WechatRuleKeywordEnum::TYPE_MATCH => [],
            WechatRuleKeywordEnum::TYPE_REGULAR => [],
            WechatRuleKeywordEnum::TYPE_INCLUDE => [],
            WechatRuleKeywordEnum::TYPE_TAKE_OVER => [],
        ];

        foreach ($ruleKeyword as $value) {
            $ruleKeywords[$value['type']][] = $value['content'];
        }

        return $ruleKeywords;
    }
}
