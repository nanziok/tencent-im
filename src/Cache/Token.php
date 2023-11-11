<?php

namespace Nanziok\TencentIM\Cache;

use Nanziok\TencentIM\Crypt\TLSSigAPIv2;
use Nanziok\TencentIM\Traits\HttpClientTrait;
use Nanziok\TencentIM\Traits\SecretTrait;

class Token extends AbstractCache
{
    use HttpClientTrait, SecretTrait;

    protected function getCacheKey(): string
    {
        $unique = md5("{$this->sdkAppId}__{$this->secret}__{$this->identifier}");
        return md5('Nanziok\TencentIM.token.' . $unique);
    }

    protected function getCacheExpire(): int
    {
        return 3600 * 24 * 7;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getFromServer(): string
    {
        $m = new TLSSigAPIv2($this->sdkAppId, $this->secret);
        return $m->genSig($this->identifier, $this->getCacheExpire() + 600);
    }
}
