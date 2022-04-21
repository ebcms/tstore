<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Tstore\Model\Server;
use DigPHP\Request\Request;

class Query extends Common
{
    public function get(
        Request $request,
        Server $server
    ) {
        $res = $server->query('/' . $request->get('api'), (array) $request->get('params'));
        if ($res['code']) {
            return $this->error($res['message'], $res['redirect_url'] ?? '', $res['code']);
        } else {
            return $this->success('获取成功', $res['data']);
        }
    }
}
