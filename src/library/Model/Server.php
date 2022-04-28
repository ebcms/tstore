<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Model;

use Composer\InstalledVersions;
use DiggPHP\Router\Router;
use DiggPHP\Framework\Config;
use DiggPHP\Framework\Framework;
use Exception;
use Throwable;

class Server
{
    private $api;

    public function __construct(Config $config)
    {
        $this->api = $config->get('api.host@ebcms/tstore', 'https://www.ebcms.com/index.php/plugin/tstore/api');
    }

    public function query(string $path, array $param = []): array
    {
        try {
            $url = $this->api . $path . '?' . http_build_query($this->getCommonParam());
            $res = (array)json_decode($this->post($url, $param), true);
            if (!isset($res['code'])) {
                return [
                    'code' => 1,
                    'message' => '错误：服务器无效响应！',
                ];
            }
            if ($res['code']) {
                $res['message'] = '服务器消息：' . ($res['message'] ?? '');
            }
            return $res;
        } catch (Throwable $th) {
            return [
                'code' => 1,
                'message' => '错误：' . $th->getMessage(),
            ];
        }
    }

    private function getCommonParam(): array
    {
        $root = InstalledVersions::getRootPackage();
        $res = [];
        $res['name'] = $root['name'];
        $res['version'] = $root['pretty_version'];
        $res['site'] = Framework::execute(function (
            Router $router
        ): string {
            return $router->build('/');
        });
        $res['installed'] = $this->getInstalled();
        return $res;
    }

    public function getInstalled(): array
    {
        $themes = [];
        foreach (glob(Framework::getRoot() . '/theme/*/theme.json') as $file) {
            $name = substr($file, strlen(Framework::getRoot() . '/theme/'), -strlen('/theme.json'));
            $json = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
            $themes[$name] = $json['version'] ?? '0.0.0';
        }
        return $themes;
    }

    private function get(string $url, $timeout = 5, $ssl_verify = false)
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'timeout' => $timeout,
            ],
        ];
        if ($ssl_verify === false) {
            $options['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ];
        }
        $response = file_get_contents($url, false, stream_context_create($options));
        if (false === $response) {
            throw new Exception('post(' . $url . ') failure!');
        }
        return $response;
    }

    private function post(string $url, array $data = [], $timeout = 5, $ssl_verify = false)
    {
        $content = http_build_query($data);
        $options = [
            'http' => [
                'method' => 'POST',
                'timeout' => $timeout,
                'header' => "Accept: application/json\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: " . mb_strlen($content),
                'content' => $content,
            ],
        ];
        if ($ssl_verify === false) {
            $options['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ];
        }
        $response = file_get_contents($url, false, stream_context_create($options));
        if (false === $response) {
            throw new Exception('post(' . $url . ') failure!');
        }
        return $response;
    }
}
