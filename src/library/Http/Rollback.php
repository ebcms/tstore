<?php

declare(strict_types=1);

namespace App\Ebcms\Tstore\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Tstore\Traits\DirTrait;
use Composer\InstalledVersions;
use DiggPHP\Session\Session;
use Throwable;

class Rollback extends Common
{
    use DirTrait;

    public function get(
        Session $session
    ) {
        try {
            $theme = $session->get('theme');
            $root_path = InstalledVersions::getRootPackage()['install_path'];
            $this->delDir($root_path . '/theme/' . $theme['name']);
            foreach ($theme['backup_dirs'] as $dir) {
                if (is_file($root_path . $dir)) {
                    unlink($root_path . $dir);
                } elseif (is_dir($root_path . $dir)) {
                    $this->delDir($root_path . $dir);
                }
            }
            $this->copyDir($theme['backup_path'], $root_path);
        } catch (Throwable $th) {
            return $this->error('还原失败：' . $th->getMessage());
        }
    }
}
