<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Tstore\Model\Server;
use DigPHP\Request\Request;
use DigPHP\Template\Template;
use Ebcms\Framework\Framework;

class Item extends Common
{
    public function get(
        Request $request,
        Server $server,
        Template $template
    ) {
        $data = [];
        $res = $server->query('/detail', [
            'name' => $request->get('name'),
        ]);
        if ($res['code']) {
            return $this->error($res['message'], $res['redirect_url'] ?? '', $res['code']);
        }
        $data['theme'] = $res['data'];
        $data['type'] = 'install';
        if (file_exists(Framework::getRoot() . '/theme/' . $request->get('name') . '/theme.json')) {
            $data['type'] = 'upgrade';
        } else {
            $data['type'] = 'install';
        }
        return $template->renderFromFile('item@ebcms/tstore', $data);
    }
}
