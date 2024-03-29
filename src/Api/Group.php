<?php

namespace Nanziok\TencentIM\Api;

use JetBrains\PhpStorm\ArrayShape;
use Nanziok\TencentIM\Constants;
use Nanziok\TencentIM\Model\CreateGroupItem;
use Nanziok\TencentIM\Model\GroupMemberInfoResponseFilter;
use Nanziok\TencentIM\Model\GroupResponseFilter;
use Nanziok\TencentIM\Model\ModifyGroupItem;
use Nanziok\TencentIM\Model\ModifyGroupMemberInfoItem;
use Nanziok\TencentIM\Traits\HttpClientTrait;

/**
 * 群组管理
 */
class Group {
    use HttpClientTrait;

    /**
     * 获取 App 中的所有群组
     *
     * @param array $filter
     *
     * @return array
     */
    public function all_group($filter = []): array {
        if ($filter) {
            $r = $this->httpClient->postJson('group_open_http_svc/get_appid_group_list', $filter);
        } else {
            $r = $this->httpClient->postJson('group_open_http_svc/get_appid_group_list');
        }
        if ($r['ErrorCode'] !== 0) {
            throw new \InvalidArgumentException($r['ErrorInfo'], $r['ErrorCode']);
        }
        return $r;
    }

    /**
     * 创建群组
     *
     * @param CreateGroupItem $item
     *
     * @return string
     */
    public function create(CreateGroupItem $item): string {
        $r = $this->httpClient->postJson('group_open_http_svc/create_group',
            (array)$item
        );
        return $r['GroupId'] ?? '';
    }

    /**
     * 获取群组资料
     *
     * @param string $groupId
     * @param GroupResponseFilter|null $filter
     *
     * @return array
     */
    public function get(string $groupId, GroupResponseFilter $filter = null): array {
        $args = [
            'GroupIdList' => [$groupId]
        ];
        null !== $filter and $args['ResponseFilter'] = (array)$filter;
        $r = $this->httpClient->postJson('group_open_http_svc/get_group_info', $args);
        $r = $r['GroupInfo'][0];
        if ($r['ErrorCode'] !== 0) {
            throw new \InvalidArgumentException($r['ErrorInfo'], $r['ErrorCode']);
        }
        return $r;
    }

    /**
     * 批量获取群组资料
     *
     * @param array $groupIds
     * @param GroupResponseFilter|null $filter
     *
     * @return array
     */
    public function batchGet(array $groupIds, GroupResponseFilter $filter = null): array {
        $args = [
            'GroupIdList' => $groupIds
        ];
        null !== $filter and $args['ResponseFilter'] = (array)$filter;
        $r = $this->httpClient->postJson('group_open_http_svc/get_group_info', $args);
        return $r['GroupInfo'];
    }

    /**
     * 修改群基本信息
     *
     * @param string $groupId
     * @param ModifyGroupItem $item
     *
     * @return bool
     */
    public function modify(string $groupId, ModifyGroupItem $item): bool {
        $item->setGroupId($groupId);
        $r = $this->httpClient->postJson('group_open_http_svc/modify_group_base_info', (array)$item);
        return $r['ActionStatus'] === Constants::ACTION_STATUS_OK;
    }

    /**
     * 解散群组
     *
     * @param string $groupId
     *
     * @return bool
     */
    public function destroy(string $groupId): bool {
        $r = $this->httpClient->postJson('group_open_http_svc/destroy_group', ['GroupId' => $groupId]);
        return $r['ActionStatus'] === Constants::ACTION_STATUS_OK;
    }

    /**
     * 更换群主 (转移群组给其他人，转移的新群主必须是群成员)
     * AVChatRoom（直播群）不支持转让群主，对该类型的群组进行操作时会返回10007错误
     *
     * @param string $groupId
     * @param string $newOwnerAccountId
     *
     * @return bool
     */
    public function changeGroupOwner(string $groupId, string $newOwnerAccountId): bool {
        $r = $this->httpClient->postJson('group_open_http_svc/change_group_owner', [
            'GroupId' => $groupId, 'NewOwner_Account' => $newOwnerAccountId
        ]);
        return $r['ActionStatus'] === Constants::ACTION_STATUS_OK;
    }

    /**
     * 查询用户在群组中的身份
     * 拉取到的成员角色，包括：Owner(群主)，Admin(群管理员)，Member(普通群成员），NotMember(非群成员)
     *
     * @param string $groupId
     * @param array $accountIds 表示需要查询的用户帐号，最多支持500个帐号
     *
     * @return array
     */
    public function getRoleInGroup(string $groupId, array $accountIds): array {
        if (count($accountIds) > 500) {
            throw new \InvalidArgumentException('AccountIds size limit exceeded.', -1);
        }
        $r = $this->httpClient->postJson('group_open_http_svc/get_role_in_group', [
            'GroupId' => $groupId, 'User_Account' => $accountIds
        ]);
        return $r['UserIdList'];
    }

    /**
     * 获取用户所加入的群组
     *
     * @param string $accountId
     *
     * @return array
     */
    public function getJoinedGroupList(string $accountId): array {
        $p = [
            'Member_Account' => $accountId
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/get_joined_group_list', $p);
        return $r;
    }

    /**
     * 获取用户所加入的群组(分页)
     *
     * @param string $accountId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getJoinedGroupListByPage(string $accountId, int $offset, int $limit = 20): array {
        $p = [
            'Member_Account' => $accountId,
            'Offset'         => $offset,
            'Limit'          => $limit,
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/get_joined_group_list', $p);
        return $r;
    }

    /**
     * 修改群成员资料
     *
     * @param ModifyGroupMemberInfoItem $item
     *
     * @return bool
     */
    public function modifyGroupMemberInfo(ModifyGroupMemberInfoItem $item): bool {
        $r = $this->httpClient->postJson('group_open_http_svc/modify_group_member_info', (array)$item);
        return $r['ActionStatus'] === Constants::ACTION_STATUS_OK;
    }

    /**
     * 增加群成员
     *
     * @param string $groupId
     * @param array $accountIds
     * @param bool $silence
     *
     * @return array
     */
    public function addGroupMember(string $groupId, array $accountIds, bool $silence = true): array {
        $r = $this->httpClient->postJson('group_open_http_svc/add_group_member', [
            'GroupId'    => $groupId,
            'MemberList' => array_map(function ($v) {
                return ['Member_Account' => $v];
            }, $accountIds),
            'Silence'    => $silence ? 1 : 0
        ]);
        return $r['MemberList'];
    }

    /**
     * 删除群成员
     * 传入数组成员，如果在群里都不存在会返回memberlist is empty的错误
     *
     * @param string $groupId
     * @param array $accountIds
     * @param string $reason
     * @param bool $silence
     *
     * @return bool
     */
    public function deleteGroupMember(
        string $groupId,
        array  $accountIds,
        string $reason = '',
        bool   $silence = false
    ): bool {
        if (count($accountIds) > 500) {
            throw new \InvalidArgumentException('AccountIds size limit exceeded.', -1);
        }
        $p = [
            'GroupId'             => $groupId,
            'MemberToDel_Account' => $accountIds,
            'Silence'             => $silence ? 1 : 0
        ];
        empty($reason) or $p['Reason'] = $reason;
        $r = $this->httpClient->postJson('group_open_http_svc/delete_group_member', $p);
        return $r['ActionStatus'] === Constants::ACTION_STATUS_OK;
    }

    /**
     * 获取群成员详细资料
     *
     * @param string $groupId
     * @param GroupMemberInfoResponseFilter|null $filter
     *
     * @return array
     */
    public function getGroupMemberInfo(string $groupId, GroupMemberInfoResponseFilter $filter = null): array {
        $p = ['GroupId' => $groupId,];
        if (null !== $filter) {
            $p += (array)$filter;
        }
        $r = $this->httpClient->postJson('group_open_http_svc/get_group_member_info', $p);
        return $r;
    }

    /**
     * 获取群成员详细资料(分页)
     *
     * @param string $groupId
     * @param int $offset
     * @param int $limit
     * @param GroupMemberInfoResponseFilter|null $filter
     *
     * @return array
     */
    public function getGroupMemberInfoByPage(
        string                        $groupId,
        int                           $offset,
                                      $limit = 100,
        GroupMemberInfoResponseFilter $filter = null
    ): array {
        $p = ['GroupId' => $groupId, 'Offset' => $offset, 'Limit' => $limit];
        if (null !== $filter) {
            $p += (array)$filter;
        }
        $r = $this->httpClient->postJson('group_open_http_svc/get_group_member_info', $p);
        return $r;
    }


    /**
     * 获取群成员详细资料 需要旗舰版资源包配合使用
     *
     * @param string $groupId
     * @param GroupMemberInfoResponseFilter|null $filter
     *
     * @return array
     */
    public function getAvGroupMemberList(string $groupId, $timestamp = 0, $mark = null): array {
        $p = [
            'GroupId'   => $groupId,
            'Timestamp' => $timestamp
        ];
        if (null !== $mark) {
            $p["Mark"] = $mark;
        }
        $r = $this->httpClient->postJson('group_open_avchatroom_http_svc/get_members', $p);
        return $r;
    }

    /**
     * 直播群封禁成员列表  需要旗舰版资源包配合使用
     * @return array
     */
    public function getAvBanMemberList($groupId, $offset = 0, $limit = 200) {
        $p = [
            'GroupId' => $groupId,
            'Offset'  => $offset
        ];
        if (null !== $limit) {
            $p["Limit"] += $limit;
        }
        $r = $this->httpClient->postJson('group_open_http_svc/get_group_ban_member', $p);
        return $r;
    }

    /**
     * 封禁语音房成员 需要旗舰版资源包配合使用
     * @return array
     */
    public function setAvBanMember($groupId, array $member_list, int $duration, $description = null) {
        $p = [
            'GroupId'         => $groupId,
            'Members_Account' => $member_list,
            "Duration"        => $duration,
        ];
        if (null !== $description) {
            $p["Description"] = $description;
        }
        $r = $this->httpClient->postJson('group_open_http_svc/ban_group_member', $p);
        return $r;
    }

    /**
     * 解封语音房成员 需要旗舰版资源包配合使用
     * @return array
     */
    public function setAvUnbanMember($groupId, array $member_list) {
        $p = [
            'GroupId'         => $groupId,
            'Members_Account' => $member_list,
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/ban_group_member', $p);
        return $r;
    }

    /**
     * 获取在线人数
     * @param $groupId
     * @return array
     */
    public function getOnlineMemberNum(string $groupId) {
        $p = [
            'GroupId' => $groupId,
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/get_online_member_num', $p);
        return $r;
    }

    /**
     * 获取群自定义资料
     * @param string $groupId
     * @return array
     */
    public function getGroupAttr(string $groupId) {
        $p = [
            'GroupId' => $groupId,
        ];
        $r = $this->httpClient->postJson('group_open_attr_http_svc/get_group_attr', $p);
        return $r;
    }

    /**
     * 修改
     * @param string $groupId
     * @param array $arr #[ArrayShape([key=>value])]
     * @return array
     */
    public function modifyGroupAttr(string $groupId, array $arr) {
        $attr = [];
        foreach ($arr as $k=>$v){
            $attr[] = [
                "key" => $k,
                "value" => $v
            ];
        }
        $p = [
            'GroupId' => $groupId,
            "GroupAttr" => $attr
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/modify_group_attr', $p);
        return $r;
    }

    /**
     * 清空群自定义资料
     * @param string $groupId
     * @return array
     */
    public function clearGroupAttr(string $groupId) {
        $p = [
            'GroupId' => $groupId,
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/clear_group_attr', $p);
        return $r;
    }

    /**
     * 重置群自定义字段
     * @param string $groupId
     * @param array $arr #[ArrayShape([key=>value])]
     * @return array
     */
    public function resetGroupAttr(string $groupId, array $arr) {
        $attr = [];
        foreach ($arr as $k=>$v){
            $attr[] = [
                "key" => $k,
                "value" => $v
            ];
        }
        $p = [
            'GroupId' => $groupId,
            "GroupAttr" => $attr
        ];
        $r = $this->httpClient->postJson('group_open_http_svc/set_group_attr', $p);
        return $r;
    }

}
