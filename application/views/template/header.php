<?php
defined('BASEPATH') or exit('No direct script access allowed');

$u = isset($current_user) ? $current_user : array();
$role_scope = isset($u['role_scope']) ? $u['role_scope'] : 'tenant';
$role_name  = isset($u['role_name']) ? $u['role_name'] : 'staff';
$nama       = isset($u['nama_lengkap']) ? $u['nama_lengkap'] : '';
$tenant     = isset($u['nama_perusahaan']) ? $u['nama_perusahaan'] : '';
$menus      = isset($menus) && is_array($menus) ? $menus : array();

if (!function_exists('render_menu_tree')) {
    function render_menu_tree($items)
    {
        if (empty($items)) return;

        echo '<ul style="list-style:none;margin:0;padding:0;display:flex;gap:12px;align-items:flex-start;">';
        foreach ($items as $m) {
            $label = htmlspecialchars((string)$m['nama_menu'], ENT_QUOTES, 'UTF-8');
            $route = !empty($m['route_path']) ? site_url($m['route_path']) : '#';

            echo '<li style="position:relative;">';
            echo '<a href="'.$route.'">'.$label.'</a>';

            if (!empty($m['children'])) {
                echo '<ul style="list-style:none;margin:6px 0 0 0;padding:8px;border:1px solid #ddd;">';
                foreach ($m['children'] as $c) {
                    $clabel = htmlspecialchars((string)$c['nama_menu'], ENT_QUOTES, 'UTF-8');
                    $croute = !empty($c['route_path']) ? site_url($c['route_path']) : '#';
                    echo '<li style="margin:4px 0;"><a href="'.$croute.'">'.$clabel.'</a></li>';
                }
                echo '</ul>';
            }

            echo '</li>';
        }
        echo '</ul>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?php echo isset($title) ? $title : 'Aplikasi'; ?></title>
</head>
<body>
<header style="padding:10px;border-bottom:1px solid #ddd;">
    <strong><?php echo isset($title) ? $title : 'Aplikasi'; ?></strong>
    <div style="float:right;">
        Login sebagai: <?php echo htmlspecialchars((string)$nama, ENT_QUOTES, 'UTF-8'); ?>
        <?php if ($role_scope === 'tenant'): ?>
            | Tenant: <?php echo htmlspecialchars((string)$tenant, ENT_QUOTES, 'UTF-8'); ?>
        <?php else: ?>
            | Scope: SYSTEM
        <?php endif; ?>
        | <a href="<?php echo site_url('auth/logout'); ?>">Logout</a>
    </div>
</header>

<nav style="padding:10px;border-bottom:1px solid #eee;">
    <?php if (!empty($menus)): ?>
        <?php render_menu_tree($menus); ?>
    <?php else: ?>
        <em>Menu belum tersedia untuk role: <?php echo htmlspecialchars((string)$role_name, ENT_QUOTES, 'UTF-8'); ?></em>
    <?php endif; ?>
</nav>
