<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Http;

use App\Ebcms\Admin\Traits\ResponseTrait;
use App\Ebcms\Admin\Traits\RestfulTrait;
use DigPHP\Psr16\LocalAdapter;
use DigPHP\Request\Request;

class Api
{
    use RestfulTrait;
    use ResponseTrait;

    public function post(
        Request $request,
        LocalAdapter $cache
    ) {
        if ($request->get('token') != $cache->get('tstoreapitoken')) {
            return $this->error('token校验失败！');
        }
        $cache->set('tstoresource', $request->post(), 10);
        return $this->success('success');
    }
}
