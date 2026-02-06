<?php
require_once 'auth.php';
requireLogin();
$userInfo = getUserInfo();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistem Kasir'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2d3436;
            --secondary: #00b894;
            --accent: #fdcb6e;
            --danger: #ff6b6b;
            --warning: #ffa502;
            --info: #3742fa;
            --light-bg: #f8f9fa;
            --border: #e0e0e0;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --success: #00b894;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
            color: var(--text-dark);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary) 0%, #1a1a1a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h1 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.8em;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #fff 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-header .user-info {
            font-size: 0.9em;
            opacity: 0.8;
            margin-top: 10px;
        }

        .sidebar-header .role-badge {
            display: inline-block;
            padding: 4px 12px;
            background: var(--accent);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            margin-top: 8px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-section {
            margin-bottom: 25px;
        }

        .nav-section-title {
            padding: 0 25px;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.5;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border-left-color: var(--accent);
        }

        .nav-link.active {
            background: rgba(253, 203, 110, 0.1);
            color: white;
            border-left-color: var(--accent);
            font-weight: 600;
        }

        .nav-link .icon {
            margin-right: 12px;
            font-size: 1.2em;
            width: 24px;
            text-align: center;
        }

        .logout-link {
            margin: 20px 25px;
            padding: 12px 20px;
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
            text-decoration: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .logout-link:hover {
            background: rgba(255, 107, 107, 0.2);
            border-color: var(--danger);
        }

        .logout-link .icon {
            margin-right: 8px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-family: 'Crimson Pro', serif;
            font-size: 2.2em;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .page-header p {
            color: var(--text-light);
            font-size: 1em;
        }

        .breadcrumb {
            display: flex;
            gap: 10px;
            align-items: center;
            font-size: 0.9em;
            color: var(--text-light);
            margin-bottom: 15px;
        }

        .breadcrumb a {
            color: var(--info);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-family: 'Crimson Pro', serif;
            font-size: 1.4em;
            color: var(--primary);
        }

        .card-body {
            padding: 25px;
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.95em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #019874;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #ee5a52;
            transform: translateY(-2px);
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-info {
            background: var(--info);
            color: white;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85em;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: var(--light-bg);
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border);
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border);
        }

        table tr:hover {
            background: rgba(102, 126, 234, 0.03);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.95em;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1em;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        select.form-control {
            cursor: pointer;
        }

        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            border-color: var(--success);
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border-color: var(--danger);
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border-color: var(--warning);
            color: #856404;
        }

        .alert-info {
            background: #d1ecf1;
            border-color: var(--info);
            color: #0c5460;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar-header h1,
            .sidebar-header .user-info,
            .sidebar-header .role-badge,
            .nav-section-title,
            .nav-link span {
                display: none;
            }

            .nav-link {
                justify-content: center;
                padding: 14px;
            }

            .nav-link .icon {
                margin: 0;
            }

            .logout-link {
                margin: 20px 10px;
                padding: 12px;
                justify-content: center;
            }

            .logout-link span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Kasir Pro</h1>
                <div class="user-info">
                    <?php echo htmlspecialchars($userInfo['username']); ?>
                </div>
                <span class="role-badge"><?php echo htmlspecialchars($userInfo['role']); ?></span>
            </div>

            <nav class="sidebar-nav">
                <?php if (isAdmin()): ?>
                    <div class="nav-section">
                        <div class="nav-section-title">Menu Admin</div>
                        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <span class="icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="penjualan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'penjualan.php' ? 'active' : ''; ?>">
                            <span class="icon">💳</span>
                            <span>Riwayat Transaksi</span>
                        </a>
                        <a href="produk.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : ''; ?>">
                            <span class="icon">📦</span>
                            <span>Data Produk</span>
                        </a>
                        <a href="user.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : ''; ?>">
                            <span class="icon">👥</span>
                            <span>Registrasi User</span>
                        </a>
                        <a href="laporan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                            <span class="icon">📄</span>
                            <span>Laporan</span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="nav-section">
                        <div class="nav-section-title">Menu Petugas</div>
                        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <span class="icon">📊</span>
                            <span>Dashboard</span>
                        </a>
                        <a href="pos.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pos.php' ? 'active' : ''; ?>">
                            <span class="icon">💳</span>
                            <span>Penjualan (POS)</span>
                        </a>
                        <a href="stok.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'stok.php' ? 'active' : ''; ?>">
                            <span class="icon">📦</span>
                            <span>Cek Stok Barang</span>
                        </a>
                        <a href="laporan_petugas.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'laporan_petugas.php' ? 'active' : ''; ?>">
                            <span class="icon">📄</span>
                            <span>Laporan Saya</span>
                        </a>
                    </div>
                <?php endif; ?>
            </nav>

            <a href="logout.php" class="logout-link" onclick="return confirm('Yakin ingin logout?')">
                <span class="icon">🚪</span>
                <span>Logout</span>
            </a>
        </aside>

        <main class="main-content">
