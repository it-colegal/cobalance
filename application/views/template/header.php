<?php
defined('BASEPATH') or exit('No direct script access allowed');

$user = isset($current_user) ? $current_user : array();
$role_scope = isset($user['role_scope']) ? $user['role_scope'] : 'tenant';
$role_name  = isset($user['role_name']) ? $user['role_name'] : 'staff';
$nama       = isset($user['nama_lengkap']) ? $user['nama_lengkap'] : '';
$tenant     = isset($user['nama_perusahaan']) ? $user['nama_perusahaan'] : '';
$menus      = isset($menus) && is_array($menus) ? $menus : array();

if (!function_exists('render_menu_tree')) {
    function render_menu_tree($items)
    {
        if (empty($items)) return;

        foreach ($items as $m) {
            $label = htmlspecialchars((string)$m['nama_menu'], ENT_QUOTES, 'UTF-8');
            $route = !empty($m['route_path']) ? site_url($m['route_path']) : '#';
            $isActive = '';

            if (!empty($m['children'])) {
                echo '<li class="dropdown '.$isActive.'">';
                echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$label.' <span class="caret"></span></a>';
                echo '<ul class="dropdown-menu">';
                foreach ($m['children'] as $c) {
                    $clabel = htmlspecialchars((string)$c['nama_menu'], ENT_QUOTES, 'UTF-8');
                    $croute = !empty($c['route_path']) ? site_url($c['route_path']) : '#';
                    echo '<li><a href="'.$croute.'">'.$clabel.'</a></li>';
                }
                echo '</ul>';
                echo '</li>';
            } else {
                echo '<li '.$isActive.'><a href="'.$route.'">'.$label.'</a></li>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?php echo isset($title) ? $title : 'Co-Balance'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sticky header */
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: #66CCCC;
            border-color: #66CCCC;
        }

        .sticky-header .navbar-brand,
        .sticky-header .navbar-nav>li>a {
            color: #ffffff;
        }

        .sticky-header .navbar-nav>li>a:hover,
        .sticky-header .navbar-nav>li.active>a {
            background-color: rgba(0, 0, 0, 0.1);
            color: #ffffff;
        }

        /* Sticky footer */
        .sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: #66CCCC;
            padding: 15px 0;
        }

        .sticky-footer p {
            margin: 0;
            color: #ffffff;
            text-align: center;
        }

        /* Ruang untuk konten utama agar tidak tertutup header & footer */
        .content-container {
            margin-top: 70px;
            margin-bottom: 70px;
        }
    </style>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- DataTable JS -->
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-default sticky-header">
    <div class="container-fluid">
        <!-- Brand and toggle -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo site_url(); ?>">Co-Balance</a>
        </div>
        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav">
                <?php render_menu_tree($menus); ?>
            </ul>
            <!-- Right-side: nama pengguna dan logout -->
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="javascript:void(0)" style="cursor: default;">
                        <?php echo htmlspecialchars((string)$nama, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </li>
                <?php if ($role_scope === 'tenant'): ?>
                    <li>
                        <a href="javascript:void(0)" style="cursor: default;">
                            <?php echo htmlspecialchars((string)$tenant, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?php echo site_url('auth/logout'); ?>" title="Logout">
                        <i class="glyphicon glyphicon-log-out"></i>
                    </a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container content-container">
