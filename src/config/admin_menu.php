<?php

use DiggPHP\Router\Router;
use DiggPHP\Framework\Framework;

return Framework::execute(function (
    Router $router
): array {
    $res = [];
    $res[] = [
        'title' => '主题商店',
        'url' => $router->build('/ebcms/tstore/index'),
    ];
    return $res;
});
