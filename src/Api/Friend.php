<?php

namespace Nanziok\TencentIM\Api;

use Nanziok\TencentIM\Constants;
use Nanziok\TencentIM\Model\AddFriendItem;
use Nanziok\TencentIM\Traits\HttpClientTrait;

/**
 * 关系链管理(好友)
 */
class Friend
{
    use HttpClientTrait;

    /**
     * 添加好友
     *
     * @param string        $fromAccountId
     * @param AddFriendItem $item
     * @param bool          $forceAddFlags 管理员强制加好友标记：1表示强制加好友，0表示常规加好友方式
     * @param string        $addType       加好友方式（默认双向加好友方式）：
     *                                     Add_Type_Single 表示单向加好友
     *                                     Add_Type_Both 表示双向加好友
     *
     * @return array
     */
    public function add(
        string $fromAccountId,
        AddFriendItem $item,
        bool $forceAddFlags = false,
        string $addType = Constants::FRIEND_ADD_TYPE_BOTH
    ): array {
        $p = [
            'From_Account' => $fromAccountId,
            'ForceAddFlags' => $forceAddFlags ? 1 : 0,
            'AddType' => $addType,
            'AddFriendItem' => [
                (array)$item
            ],
        ];
        return $this->httpClient->postJson('sns/friend_add', $p);
    }

    /**
     * 批量添加好友
     *
     * @param string $fromAccountId
     * @param array  $friendItems
     * @param bool   $forceAddFlags
     * @param string $addType
     *
     * @return array
     */
    public function batchAdd(
        string $fromAccountId,
        array $friendItems,
        bool $forceAddFlags = false,
        string $addType = Constants::FRIEND_ADD_TYPE_BOTH
    ): array {
        return $this->httpClient->postJson('sns/friend_add', [
            'From_Account' => $fromAccountId,
            'ForceAddFlags' => $forceAddFlags ? 1 : 0,
            'AddType' => $addType,
            'AddFriendItem' => (array)$friendItems,
        ]);
    }

    /**
     * 拉取好友
     *
     * @param string $fromAccountId
     * @param int    $StartIndex       分页的起始位置
     * @param int    $StandardSequence 上次拉好友数据时返回的 StandardSequence，如果 StandardSequence 字段的值与后台一致，后台不会返回标配好友数据
     * @param int    $CustomSequence   上次拉好友数据时返回的 CustomSequence，如果 CustomSequence 字段的值与后台一致，后台不会返回自定义好友数据
     *
     * @return array
     */
    public function get(
        string $fromAccountId,
        int $StartIndex,
        int $StandardSequence = 0,
        int $CustomSequence = 0
    ): array {
        $p = [
            'From_Account' => $fromAccountId,
            'StartIndex' => $StartIndex,
        ];
        empty($StandardSequence) or $p['StandardSequence'] = $StandardSequence;
        empty($CustomSequence) or $p['CustomSequence'] = $CustomSequence;
        return $this->httpClient->postJson('sns/friend_get', $p);
    }
    /**
     * 拉取指定好友
     *
     * @param string $fromAccountId     指定要拉取好友数据的用户的 UserID
     * @param string    $ToccountId       好友的 UserID
     * @param array    $TagList 指定要拉取的资料字段及好友字段
     *
     * @return array
     */
    public function friend_get_list(
        string $fromAccountId,
        string $To_Account,
        array $TagList=[]
    ): array {
        $p = [
            'From_Account' => $fromAccountId,
            'To_Account'=>[$To_Account],
            'TagList' => $TagList?$TagList:[
                "Tag_Profile_IM_Nick",
                "Tag_Profile_IM_Image",
                "Tag_SNS_IM_Remark",
                "Tag_SNS_IM_AddSource",
                "Tag_SNS_IM_AddWording",
                "Tag_SNS_IM_Group"]
        ];
        return $this->httpClient->postJson('sns/friend_get_list', $p);
    }

    /**
     * 删除好友
     *
     * @param string $fromAccountId
     * @param array  $toAccountIds
     * @param string $deleteType
     *
     * @return array
     */
    public function delete(
        string $fromAccountId,
        array $toAccountIds,
        string $deleteType = Constants::FRIEND_DELETE_TYPE_BOTH
    ): array {
        return $this->httpClient->postJson('sns/friend_delete', [
            'From_Account' => $fromAccountId,
            'To_Account' => $toAccountIds,
            'DeleteType' => $deleteType,
        ]);
    }

    /**
     * 删除所有好友
     *
     * @param string $fromAccountId
     * @param string $deleteType
     *
     * @return bool
     */
    public function deleteAll(
        string $fromAccountId,
        string $deleteType = Constants::FRIEND_DELETE_TYPE_SIGNLE
    ): bool {
        $r = $this->httpClient->postJson('sns/friend_delete_all', [
            'From_Account' => $fromAccountId,
            'DeleteType' => $deleteType,
        ]);
        return $r['ActionStatus'] === Constants::ACTION_STATUS_OK;
    }

    /**
     * 更新好友
     *
     * @param string $fromAccountId
     * @param string $toAccountId
     * @param array  $items
     *
     * @return array
     */
    public function update(
        string $fromAccountId,
        string $toAccountId,
        array $items
    ): array {
        return $this->httpClient->postJson('sns/friend_update', [
            'From_Account' => $fromAccountId,
            'UpdateItem' => [['To_Account' => $toAccountId, 'SnsItem' => (array)$items]],
        ]);
    }

    /**
     * 批量更新好友
     *
     * @param string $fromAccountId
     * @param array  $items
     *
     * @return array
     */
    public function batchUpdate(
        string $fromAccountId,
        array $items
    ): array {
        return $this->httpClient->postJson('sns/friend_update', [
            'From_Account' => $fromAccountId,
            'UpdateItem' => (array)$items,
        ]);
    }

    /**
     * 批量导入好友
     *
     * @param string $fromAccountId
     * @param array  $friendItems
     *
     * @return array
     */
    public function import(
        string $fromAccountId,
        array $friendItems
    ): array {
        return $this->httpClient->postJson('sns/friend_import', [
            'From_Account' => $fromAccountId,
            'AddFriendItem' => (array)$friendItems,
        ]);
    }

    /**
     * 校验好友
     *
     * @param string $fromAccountId
     * @param array  $toAccountIds
     * @param string $checkType
     *
     * @return array
     */
    public function check(
        string $fromAccountId,
        array $toAccountIds,
        string $checkType = Constants::FRIEND_CHECK_TYPE_BOTH
    ): array {
        if (count($toAccountIds) > 1000) {
            throw new \InvalidArgumentException('AccountIds size limit exceeded.', -1);
        }
        return $this->httpClient->postJson('sns/friend_check', [
            'From_Account' => $fromAccountId,
            'To_Account' => $toAccountIds,
            'CheckType' => $checkType,
        ]);
    }

}
