<?php
// Base URL detectado dinamicamente — funciona em XAMPP sub-pasta e virtual host
if (!isset($baseUrl)) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}

// Auth session data (set by auth_check.php)
$_currentUser = $_SESSION['user']    ?? ['name' => '', 'role' => '', 'role_label' => ''];
$_authToken   = $_SESSION['auth_token'] ?? '';
$_isMaster    = ($_currentUser['role'] ?? '') === 'MASTER_ADMIN';
$_userInitial = strtoupper(mb_substr($_currentUser['name'] ?? 'U', 0, 1));
$_roleLabel   = $_currentUser['role_label'] ?? $_currentUser['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapada Diamantina — Sistema de Expedições</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/main.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/components.css">
    <script>
        // SECURITY: The Sanctum token is stored in an HttpOnly cookie and is
        // never exposed to JavaScript. Only the current user profile (non-sensitive)
        // is injected for UI use (role-based menu visibility, etc.).
        window.CURRENT_USER = <?= json_encode($_currentUser) ?>;
    </script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <!-- Logo oficial — montanhas com sol nascente -->
                <svg width="40" height="30" viewBox="0 0 300 220" xmlns="http://www.w3.org/2000/svg">
                    <!-- Raios do sol — centro (150,88) -->
                    <line x1="150" y1="29" x2="150" y2="9"  stroke="#C49018" stroke-width="10" stroke-linecap="round"/>
                    <line x1="128" y1="34" x2="120" y2="14" stroke="#C49018" stroke-width="9"  stroke-linecap="round"/>
                    <line x1="172" y1="34" x2="180" y2="14" stroke="#C49018" stroke-width="9"  stroke-linecap="round"/>
                    <line x1="109" y1="47" x2="93"  y2="31" stroke="#C49018" stroke-width="8"  stroke-linecap="round"/>
                    <line x1="191" y1="47" x2="207" y2="31" stroke="#C49018" stroke-width="8"  stroke-linecap="round"/>
                    <line x1="96"  y1="67" x2="76"  y2="58" stroke="#C49018" stroke-width="7"  stroke-linecap="round"/>
                    <line x1="204" y1="67" x2="224" y2="58" stroke="#C49018" stroke-width="7"  stroke-linecap="round"/>
                    <line x1="92"  y1="86" x2="72"  y2="85" stroke="#C49018" stroke-width="6.5" stroke-linecap="round"/>
                    <line x1="208" y1="86" x2="228" y2="85" stroke="#C49018" stroke-width="6.5" stroke-linecap="round"/>
                    <!-- Arco do sol (semicírculo, raio 52) -->
                    <path d="M 98 88 A 52 52 0 0 1 202 88" fill="none" stroke="#C49018" stroke-width="13" stroke-linecap="round"/>
                    <!-- Montanhas preenchidas -->
                    <path d="M 0 178 L 85 58 L 148 96 L 190 70 L 216 85 L 242 74 L 282 130 L 300 178 L 300 220 L 0 220 Z" fill="#2B3B18"/>
                    <!-- Destaque dourado no pico esquerdo -->
                    <path d="M 85 58 L 115 92 L 148 96 L 128 104 L 96 80 Z" fill="#9B7840" opacity="0.72"/>
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

                <?php if ($_isMaster): ?>
                <span class="nav-section-label">Administração</span>
                <a href="<?= $baseUrl ?>/users.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    <span>Gestão de Usuários</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar"><?= htmlspecialchars($_userInitial) ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($_currentUser['name'] ?? 'Usuário') ?></div>
                        <div class="user-role"><?= htmlspecialchars($_roleLabel) ?></div>
                    </div>
                </div>
                <a href="<?= $baseUrl ?>/logout.php" class="nav-item nav-logout" title="Sair do sistema" style="margin-top:8px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Sair</span>
                </a>
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
                    <!-- Raios do sol — centro (150,88) -->
                    <line x1="150" y1="29" x2="150" y2="9"  stroke="#C49018" stroke-width="10" stroke-linecap="round"/>
                    <line x1="128" y1="34" x2="120" y2="14" stroke="#C49018" stroke-width="9"  stroke-linecap="round"/>
                    <line x1="172" y1="34" x2="180" y2="14" stroke="#C49018" stroke-width="9"  stroke-linecap="round"/>
                    <line x1="109" y1="47" x2="93"  y2="31" stroke="#C49018" stroke-width="8"  stroke-linecap="round"/>
                    <line x1="191" y1="47" x2="207" y2="31" stroke="#C49018" stroke-width="8"  stroke-linecap="round"/>
                    <line x1="96"  y1="67" x2="76"  y2="58" stroke="#C49018" stroke-width="7"  stroke-linecap="round"/>
                    <line x1="204" y1="67" x2="224" y2="58" stroke="#C49018" stroke-width="7"  stroke-linecap="round"/>
                    <line x1="92"  y1="86" x2="72"  y2="85" stroke="#C49018" stroke-width="6.5" stroke-linecap="round"/>
                    <line x1="208" y1="86" x2="228" y2="85" stroke="#C49018" stroke-width="6.5" stroke-linecap="round"/>
                    <!-- Arco do sol (semicírculo, raio 52) -->
                    <path d="M 98 88 A 52 52 0 0 1 202 88" fill="none" stroke="#C49018" stroke-width="13" stroke-linecap="round"/>
                    <!-- Montanhas preenchidas -->
                    <path d="M 0 178 L 85 58 L 148 96 L 190 70 L 216 85 L 242 74 L 282 130 L 300 178 L 300 220 L 0 220 Z" fill="#2B3B18"/>
                    <!-- Destaque dourado no pico esquerdo -->
                    <path d="M 85 58 L 115 92 L 148 96 L 128 104 L 96 80 Z" fill="#9B7840" opacity="0.72"/>
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
