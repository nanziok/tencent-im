<?php

namespace Nanziok\TencentIM\Model;

class SendChatMsgItem
{
    /** @var int 消息随机数，由随机函数产生，用于后台定位问题（必填） */
    public $MsgRandom;

    /** @var string TIM 消息对象类型(必填) */
    public $MsgBody;

    /** @var string  消息来源帐号, 非必填 */
    public $From_Account;

    /** @var int 消息离线保存时长（单位：秒），最长为7天（604800秒）
     * 若设置该字段为0，则消息只发在线用户，不保存离线
     * 若设置该字段超过7天（604800秒），仍只保存7天
     * 若不设置该字段，则默认保存7天 */
    public $MsgLifeTime;

    //消息随机数（32位无符号整数），后台用于同一秒内的消息去重。请确保该字段填的是随机
    public $MsgSeq;

    //消息回调禁止开关，只对本条消息有效，ForbidBeforeSendMsgCallback 表示禁止发消息前回调，ForbidAfterSendMsgCallback 表示禁止发消息后回调
    public $ForbidCallbackControl;

    /** @var int
     * 1：把消息同步到 From_Account 在线终端和漫游上；
     * 2：消息不同步至 From_Account；
     * 若不填写默认情况下会将消息存 From_Account 漫游
     */
    public $SyncOtherMachine;

    /** @var array 离线推送信息配置 */
    public $OfflinePushInfo;

    /** @var integer 默认为 0，表示消息存历史聊天记录 1表示消息不存历史聊天记录，即发送消息时，若接收方在线，则能收到此消息，若接收方不在线，则收不到该消息。适用于实现一些实时状态类的功能。 */
    public $OnlineOnlyFlag;

    /** @var string 消息自定义数据（云端保存，会发送到对端，程序卸载重装后还能拉取到） */
    public $CloudCustomData;

    /** @var array{string} 离线推送信息配置消息发送控制选项，["NoUnread","NoLastMsg","WithMuteNotifications","NoMsgCheck"] */
    public $SendMsgControl;

    /** @var integer 该条消息是否需要已读回执，0为不需要，1为需要，默认为0 */
    public $IsNeedReadReceipt = 0;

    /**
     * SendChatMsgItem constructor.
     */
    public function __construct()
    {
        $this->MsgRandom = rand(100000, 999999);
    }


    /**
     * @return int
     */
    public function getMsgRandom(): int
    {
        return $this->MsgRandom;
    }

    /**
     * @param int $MsgRandom
     */
    public function setMsgRandom(int $MsgRandom): void
    {
        $this->MsgRandom = $MsgRandom;
    }


    /**
     * @return array
     */
    public function getMsgBody(): array
    {
        return $this->MsgBody;
    }

    /**
     * @param array $MsgBody
     */
    public function setMsgBody(array $MsgBody): void
    {
        $this->MsgBody = $MsgBody;
    }


    public function addMessage($type,$data) {
        $this->MsgBody[] = [
            "MsgType" => $type,
            "MsgContent" => $data
        ];
    }

    /**
     * @return string
     */
    public function getFromAccount(): string
    {
        return $this->From_Account;
    }

    /**
     * @param string $From_Account
     */
    public function setFromAccount(string $From_Account): void
    {
        $this->From_Account = $From_Account;
    }


    /**
     * @return int
     */
    public function getMsgLifeTime(): int
    {
        return $this->MsgLifeTime;
    }

    /**
     * @param int $MsgLifeTime
     */
    public function setMsgLifeTime(int $MsgLifeTime): void
    {
        $this->MsgLifeTime = $MsgLifeTime;
    }

    /**
     * @return int
     */
    public function getSyncOtherMachine(): int
    {
        return $this->SyncOtherMachine;
    }

    /**
     * @param int $SyncOtherMachine
     */
    public function setSyncOtherMachine(int $SyncOtherMachine): void
    {
        $this->SyncOtherMachine = $SyncOtherMachine;
    }

    /**
     * @return array
     */
    public function getOfflinePushInfo(): array
    {
        return $this->OfflinePushInfo;
    }

    /**
     * @param array $OfflinePushInfo
     */
    public function setOfflinePushInfo(array $OfflinePushInfo): void
    {
        $this->OfflinePushInfo = $OfflinePushInfo;
    }

    public function setSendMsgControl($val) {
        $this->SendMsgControl = $val;
    }

    public function getSendMsgControl() {
        return $this->SendMsgControl;
    }

}
