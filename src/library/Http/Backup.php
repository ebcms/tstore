<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Tstore\Traits\DirTrait;
use Composer\InstalledVersions;
use DiggPHP\Session\Session;
use Exception;
use Throwable;

class Backup extends Common
{
    use DirTrait;

    public function get(
        Session $session
    ) {
        try {
            $root_path = InstalledVersions::getRootPackage()['install_path'];
            $theme = $session->get('theme');
            $theme['backup_path'] = $root_path . '/backup/' . date('YmdHis');
            $theme['backup_dirs'] = [
                'theme/', // todo
            ];
            $this->backup($theme['backup_dirs'], $root_path, $theme['backup_path']);
            $session->set('theme', $theme);
            return $this->success('备份成功！', $theme);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    private function backup(array $items, string $path, string $target)
    {
        foreach ($items as $item) {
            if (is_file($path . '/' . $item)) {
                if (!is_dir(dirname($target . '/' . $item))) {
                    if (false === mkdir(dirname($target . '/' . $item), 0755, true)) {
                        throw new Exception('创建目录（' . dirname($target . '/' . $item) . '）失败，请检查权限~');
                    }
                }
                if (false === copy($path . '/' . $item, $target . '/' . $item)) {
                    throw new Exception('Copy失败（from:' . $path . '/' . $item . ' to:' . $target . '/' . $item . '），请检查权限~');
                }
            } else {
                $this->copyDir($path . '/' . $item, $target . '/' . $item);
            }
        }
    }
}
