<?php

namespace Nanziok\TencentIM\Traits;

use Nanziok\TencentIM\Http\HttpClientInterface;

trait HttpClientTrait
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }
}
