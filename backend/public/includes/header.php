<?php
// Base URL detectado dinamicamente — funciona em XAMPP sub-pasta e virtual host
if (!isset($baseUrl)) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapada Diamantina — Sistema de Expedições</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/main.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/components.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <!-- Morro do Camelo icon -->
                <svg width="36" height="28" viewBox="0 0 36 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Ground line -->
                    <line x1="0" y1="27" x2="36" y2="27" stroke="#C4A882" stroke-width="0.8" stroke-opacity="0.5"/>
                    <!-- Left peak (taller) -->
                    <path d="M2 27 C4 27 6 20 9 14 C10.5 11 12 9 13.5 9 C15 9 16.5 11.5 18 15 C19 17 20 19 21 21" stroke="#C4A882" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                    <!-- Right peak (shorter) -->
                    <path d="M21 21 C22 19 23.5 16 25 14 C26.5 12 28 11.5 29.5 12 C31 12.5 33 15 34 18 C34.8 20.5 35 23 35 27" stroke="#C4A882" stroke-width="1.4" fill="none" stroke-linecap="round"/>
                    <!-- Sun rays behind peaks -->
                    <line x1="18" y1="2" x2="18" y2="5.5" stroke="#B8860B" stroke-width="0.9" stroke-linecap="round" opacity="0.9"/>
                    <line x1="13.5" y1="3.5" x2="15" y2="6.5" stroke="#B8860B" stroke-width="0.9" stroke-linecap="round" opacity="0.7"/>
                    <line x1="22.5" y1="3.5" x2="21" y2="6.5" stroke="#B8860B" stroke-width="0.9" stroke-linecap="round" opacity="0.7"/>
                    <line x1="10.5" y1="6" x2="12.5" y2="8" stroke="#B8860B" stroke-width="0.8" stroke-linecap="round" opacity="0.5"/>
                    <line x1="25.5" y1="6" x2="23.5" y2="8" stroke="#B8860B" stroke-width="0.8" stroke-linecap="round" opacity="0.5"/>
                </svg>
                <span class="logo-text">Chapada Diamantina<em>Expedições Premium</em></span>
            </div>

            <nav class="sidebar-nav">
                <span class="nav-section-label">Operações</span>

                <a href="<?= $baseUrl ?>/dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="<?= $baseUrl ?>/expeditions.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'expeditions.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 17l4-8 4 4 4-6 4 10"></path>
                        <line x1="3" y1="21" x2="21" y2="21"></line>
                    </svg>
                    <span>Expedições</span>
                </a>

                <a href="<?= $baseUrl ?>/crm.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'crm.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>CRM de Leads</span>
                </a>

                <a href="<?= $baseUrl ?>/calendar.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'calendar.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Calendário</span>
                </a>

                <span class="nav-section-label">Conteúdo</span>

                <a href="<?= $baseUrl ?>/media.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <span>Banco de Mídia</span>
                </a>

                <a href="<?= $baseUrl ?>/analytics.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <line x1="12" y1="20" x2="12" y2="10"></line>
                        <line x1="18" y1="20" x2="18" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="16"></line>
                    </svg>
                    <span>Analytics</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">A</div>
                    <div class="user-info">
                        <div class="user-name">Administrador</div>
                        <div class="user-role">Chapada Diamantina</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-wrapper">
            <header class="top-bar">
                <div class="search-bar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" placeholder="Buscar expedições, leads, viajantes..." id="globalSearch">
                </div>

                <div class="top-bar-actions">
                    <button class="icon-button" onclick="toggleNotifications()" title="Notificações">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notification-badge">3</span>
                    </button>
                </div>
            </header>
