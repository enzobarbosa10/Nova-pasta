<?php
session_start();
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Visão Geral</span>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Acompanhe a operação da sua agência em tempo real</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center">
            <button class="btn-secondary" onclick="refreshDashboard()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                Atualizar
            </button>
            <button class="btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Nova Expedição
            </button>
        </div>
    </div>

    <!-- KPI Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon revenue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Receita Total</span>
                <div class="stat-value" id="stat-revenue">—</div>
                <span class="stat-change" id="stat-revenue-change">carregando...</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon leads">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Leads Ativos</span>
                <div class="stat-value" id="stat-leads">—</div>
                <span class="stat-change" id="stat-leads-change">carregando...</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon expeditions">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 17l4-8 4 4 4-6 4 10"></path>
                    <line x1="3" y1="21" x2="21" y2="21"></line>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Expedições</span>
                <div class="stat-value" id="stat-expeditions">—</div>
                <span class="stat-change" id="stat-expeditions-change">carregando...</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon conversion">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <span class="stat-label">Taxa de Conversão</span>
                <div class="stat-value" id="stat-conversion">—</div>
                <span class="stat-change" id="stat-conversion-change">carregando...</span>
            </div>
        </div>
    </div>

    <!-- Two-column layout -->
    <div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start">

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <div>
                    <span class="card-label">Movimentações</span>
                    <h2>Atividade Recente</h2>
                </div>
                <a href="<?= $baseUrl ?>/crm.php" style="font-size:12px;color:var(--gold);text-decoration:none;letter-spacing:0.05em;">Ver tudo →</a>
            </div>
            <div class="card-content" style="padding:8px 0">
                <div class="activity-list" id="activityFeed">
                    <div class="activity-item">
                        <div class="activity-icon lead">...</div>
                        <div class="activity-content">
                            <span style="color:var(--terra);font-size:13px">Carregando atividade recente...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar widgets -->
        <div>

            <!-- KPI Semanal -->
            <div class="card" style="margin-bottom:20px">
                <div class="card-header">
                    <div>
                        <span class="card-label">Esta Semana</span>
                        <h2>KPIs Semanais</h2>
                    </div>
                </div>
                <div class="card-content" style="padding:16px 20px">
                    <div class="kpi-mini-list">
                        <div class="kpi-mini-row">
                            <span class="kpi-mini-label">Leads captados</span>
                            <div class="kpi-mini-right">
                                <span class="kpi-mini-value" id="kpi-leads-week">—</span>
                                <div class="kpi-mini-bar"><div class="kpi-mini-fill" id="kpi-leads-bar" style="width:0%;background:var(--moss)"></div></div>
                            </div>
                        </div>
                        <div class="kpi-mini-row">
                            <span class="kpi-mini-label">Conversões</span>
                            <div class="kpi-mini-right">
                                <span class="kpi-mini-value" id="kpi-conversions-week">—</span>
                                <div class="kpi-mini-bar"><div class="kpi-mini-fill" id="kpi-conversions-bar" style="width:0%;background:var(--gold)"></div></div>
                            </div>
                        </div>
                        <div class="kpi-mini-row">
                            <span class="kpi-mini-label">Taxa de conversão</span>
                            <div class="kpi-mini-right">
                                <span class="kpi-mini-value" id="kpi-conv-rate">—</span>
                                <div class="kpi-mini-bar"><div class="kpi-mini-fill" id="kpi-conv-rate-bar" style="width:0%;background:var(--moss)"></div></div>
                            </div>
                        </div>
                        <div class="kpi-mini-row">
                            <span class="kpi-mini-label">Vendas esta semana</span>
                            <div class="kpi-mini-right">
                                <span class="kpi-mini-value" id="kpi-revenue-week">—</span>
                                <div class="kpi-mini-bar"><div class="kpi-mini-fill" id="kpi-revenue-bar" style="width:0%;background:var(--burnt)"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximas Expedições -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="card-label">Em Breve</span>
                        <h2>Próximas Expedições</h2>
                    </div>
                    <a href="<?= $baseUrl ?>/expeditions.php" style="font-size:12px;color:var(--gold);text-decoration:none;">Ver todas →</a>
                </div>
                <div class="upcoming-list" id="upcomingExpeditionsList">
                    <div class="upcoming-item">
                        <div class="upcoming-dot" style="background:var(--terra)"></div>
                        <div class="upcoming-info">
                            <span class="upcoming-name" style="color:var(--terra)">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?= $baseUrl ?>/js/dashboard.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
