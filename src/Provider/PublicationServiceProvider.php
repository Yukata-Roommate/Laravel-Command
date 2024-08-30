<?php

namespace YukataRm\Laravel\Command\Provider;

use YukataRm\Laravel\Provider\PublicationServiceProvider as BaseServiceProvider;

/**
 * Publication Service Provider
 * 
 * @package YukataRm\Laravel\Command\Provider
 */
class PublicationServiceProvider extends BaseServiceProvider
{
    /**
     * base path
     * 
     * @var string
     */
    protected string $basePath = __DIR__;

    /**
     * publish common group
     * 
     * @var string
     */
    protected string $commonGroup = "yukata-roommate";

    /**
     * get publications
     * 
     * @return array<string, array<string, string>>
     */
    protected function publications(): array
    {
        return [
            "ym-command" => [
                "config" => config_path("yukata-roommate"),
            ],
        ];
    }
}
