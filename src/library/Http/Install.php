<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Http;

use App\Ebcms\Admin\Http\Common;
use DigPHP\Session\Session;
use Throwable;

class Install extends Common
{
    public function get(
        Session $session
    ) {
        try {
            $session->delete('theme');
            return $this->success('安装成功!');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
