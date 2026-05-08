// ===========================
// Analytics Page Logic — Dados Reais
// ===========================

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('analyticsContainer') || document.querySelector('.analytics-dashboard')) {
        loadAnalytics();
    }
});

// ===========================
// Load all analytics data
// ===========================
async function loadAnalytics() {
    const container = document.querySelector('.analytics-dashboard');
    if (!container) return;

    container.innerHTML = `
        <div class="analytics-loading" style="text-align:center;padding:60px;color:var(--terra)">
            <div class="spinner" style="margin:0 auto 16px"></div>
            <p>Carregando dados...</p>
        </div>`;

    try {
        const [stats, analytics] = await Promise.all([
            api.getDashboardStats(),
            api.getAnalytics()
        ]);
        renderAnalyticsDashboard(container, stats, analytics);
    } catch (error) {
        console.error('Erro ao carregar analytics:', error);
        container.innerHTML = `
            <div style="text-align:center;padding:60px;color:var(--terra)">
                <p>Erro ao carregar dados. Verifique a conexão com o servidor.</p>
                <button class="btn-secondary" onclick="loadAnalytics()" style="margin-top:16px">Tentar novamente</button>
            </div>`;
    }
}

// ===========================
// Render full analytics dashboard
// ===========================
function renderAnalyticsDashboard(container, stats, analytics) {
    const monthlyRevenue = analytics.monthly_revenue || [];
    const funnel         = analytics.funnel || [];
    const sources        = analytics.sources || [];
    const destinations   = analytics.destinations || [];

    const maxRevenue = Math.max(...monthlyRevenue.map(m => m.revenue), 1);
    const maxFunnel  = Math.max(...funnel.map(f => f.count), 1);
    const maxSource  = Math.max(...sources.map(s => s.count), 1);
    const maxDest    = Math.max(...destinations.map(d => d.count), 1);

    const stageLabelPT = {
        NEW: 'Novos', CONTACTED: 'Contatados', QUALIFIED: 'Qualificados',
        PROPOSAL: 'Proposta', RESERVED: 'Reservados', PAID: 'Fechados'
    };
    const stageColor = {
        NEW: 'var(--terra)', CONTACTED: 'var(--gold)', QUALIFIED: 'var(--moss)',
        PROPOSAL: 'var(--burnt)', RESERVED: '#5c7a3e', PAID: '#2e6b4f'
    };
    const sourceLabel = {
        instagram: 'Instagram', whatsapp: 'WhatsApp', indicacao: 'Indicação',
        site: 'Site', facebook: 'Facebook', google: 'Google', outros: 'Outros'
    };

    container.innerHTML = `
        <!-- KPI summary row -->
        <div class="analytics-kpi-row">
            <div class="analytics-kpi-card">
                <span class="analytics-kpi-label">Receita Total</span>
                <div class="analytics-kpi-value">${formatCurrency(stats.total_revenue || 0)}</div>
                <span class="analytics-kpi-sub">${stats.total_leads || 0} leads no total</span>
            </div>
            <div class="analytics-kpi-card">
                <span class="analytics-kpi-label">Taxa de Conversão</span>
                <div class="analytics-kpi-value">${stats.conversion_rate || 0}%</div>
                <span class="analytics-kpi-sub">Leads → Fechados</span>
            </div>
            <div class="analytics-kpi-card">
                <span class="analytics-kpi-label">Expedições Ativas</span>
                <div class="analytics-kpi-value">${stats.active_expeditions || 0}</div>
                <span class="analytics-kpi-sub">de ${stats.total_expeditions || 0} cadastradas</span>
            </div>
            <div class="analytics-kpi-card">
                <span class="analytics-kpi-label">Novos Leads (mês)</span>
                <div class="analytics-kpi-value">${stats.new_leads_month || 0}</div>
                <span class="analytics-kpi-sub">este mês</span>
            </div>
        </div>

        <div class="chart-grid">
            <!-- Monthly Revenue -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="card-label">Últimos 6 meses</span>
                        <h2>Receita Mensal</h2>
                    </div>
                </div>
                <div class="card-content" style="padding:24px 20px">
                    ${monthlyRevenue.length === 0
                        ? '<p style="color:var(--terra);text-align:center;padding:40px 0">Nenhuma receita registrada</p>'
                        : `<div class="bar-chart">
                            ${monthlyRevenue.map(m => `
                                <div class="bar-col">
                                    <div class="bar-tooltip">${formatCurrency(m.revenue)}</div>
                                    <div class="bar-fill" style="height:${maxRevenue > 0 ? Math.round((m.revenue / maxRevenue) * 180) : 0}px;background:var(--moss)"></div>
                                    <div class="bar-label">${m.month}</div>
                                </div>`).join('')}
                        </div>`
                    }
                </div>
            </div>

            <!-- Conversion Funnel -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="card-label">Pipeline</span>
                        <h2>Funil de Conversão</h2>
                    </div>
                </div>
                <div class="card-content" style="padding:20px">
                    ${funnel.length === 0
                        ? '<p style="color:var(--terra);text-align:center;padding:40px 0">Nenhum lead cadastrado</p>'
                        : funnel.map(f => `
                            <div class="funnel-row">
                                <span class="funnel-label">${stageLabelPT[f.stage] || f.stage}</span>
                                <div class="funnel-bar-wrap">
                                    <div class="funnel-bar" style="width:${maxFunnel > 0 ? Math.round((f.count / maxFunnel) * 100) : 0}%;background:${stageColor[f.stage] || 'var(--terra)'}"></div>
                                </div>
                                <span class="funnel-count">${f.count}</span>
                                <span class="funnel-value">${formatCurrency(f.value)}</span>
                            </div>`).join('')
                    }
                </div>
            </div>

            <!-- Lead Sources -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="card-label">Origem dos Leads</span>
                        <h2>Canais de Captação</h2>
                    </div>
                </div>
                <div class="card-content" style="padding:20px">
                    ${sources.length === 0
                        ? '<p style="color:var(--terra);text-align:center;padding:40px 0">Nenhum dado de origem disponível</p>'
                        : sources.map(s => `
                            <div class="funnel-row">
                                <span class="funnel-label">${sourceLabel[s.source] || s.source}</span>
                                <div class="funnel-bar-wrap">
                                    <div class="funnel-bar" style="width:${maxSource > 0 ? Math.round((s.count / maxSource) * 100) : 0}%;background:var(--gold)"></div>
                                </div>
                                <span class="funnel-count">${s.count}</span>
                            </div>`).join('')
                    }
                </div>
            </div>

            <!-- Top Destinations -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="card-label">Destinos</span>
                        <h2>Destinos Mais Procurados</h2>
                    </div>
                </div>
                <div class="card-content" style="padding:20px">
                    ${destinations.length === 0
                        ? '<p style="color:var(--terra);text-align:center;padding:40px 0">Nenhum destino registrado</p>'
                        : destinations.map((d, i) => `
                            <div class="funnel-row">
                                <span class="funnel-rank">${i + 1}</span>
                                <span class="funnel-label">${d.destination}</span>
                                <div class="funnel-bar-wrap">
                                    <div class="funnel-bar" style="width:${maxDest > 0 ? Math.round((d.count / maxDest) * 100) : 0}%;background:var(--burnt)"></div>
                                </div>
                                <span class="funnel-count">${d.count}</span>
                            </div>`).join('')
                    }
                </div>
            </div>
        </div>
    `;
}

// ===========================
// Analytics Styles
// ===========================
const analyticsStyle = document.createElement('style');
analyticsStyle.textContent = `
    .analytics-kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .analytics-kpi-card {
        background: var(--cream);
        border: 1px solid rgba(196,168,130,.25);
        border-radius: 12px;
        padding: 20px;
    }
    .analytics-kpi-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--terra);
        margin-bottom: 6px;
    }
    .analytics-kpi-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--graphite);
        font-family: 'Playfair Display', serif;
        margin-bottom: 4px;
    }
    .analytics-kpi-sub {
        font-size: 12px;
        color: #6b5e4e;
    }
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 24px;
    }
    .bar-chart {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        height: 220px;
        padding: 0 8px;
        position: relative;
    }
    .bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        position: relative;
    }
    .bar-col:hover .bar-tooltip { opacity: 1; }
    .bar-tooltip {
        position: absolute;
        top: -30px;
        font-size: 11px;
        font-weight: 600;
        color: var(--graphite);
        white-space: nowrap;
        opacity: 0;
        transition: opacity .2s;
        pointer-events: none;
    }
    .bar-fill {
        width: 100%;
        border-radius: 4px 4px 0 0;
        min-height: 4px;
        transition: height .4s ease;
    }
    .bar-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--terra);
        text-transform: uppercase;
    }
    .funnel-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .funnel-rank {
        width: 20px;
        font-size: 12px;
        font-weight: 700;
        color: var(--terra);
        flex-shrink: 0;
        text-align: right;
    }
    .funnel-label {
        width: 100px;
        font-size: 13px;
        font-weight: 600;
        color: var(--graphite);
        flex-shrink: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .funnel-bar-wrap {
        flex: 1;
        height: 8px;
        background: rgba(196,168,130,.15);
        border-radius: 4px;
        overflow: hidden;
    }
    .funnel-bar {
        height: 100%;
        border-radius: 4px;
        transition: width .5s ease;
    }
    .funnel-count {
        width: 30px;
        font-size: 13px;
        font-weight: 700;
        color: var(--graphite);
        text-align: right;
    }
    .funnel-value {
        width: 90px;
        font-size: 12px;
        color: var(--moss);
        font-weight: 600;
        text-align: right;
    }
    .analytics-loading .spinner {
        width: 32px;
        height: 32px;
        border: 3px solid rgba(59,74,47,.15);
        border-top-color: var(--moss);
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
`;
document.head.appendChild(analyticsStyle);

// Export
window.loadAnalytics = loadAnalytics;


// Initialize charts (basic implementation)
function initializeCharts() {
    showNotification('Analytics charts coming soon!', 'info');
    
    // In a real implementation, you would use a library like Chart.js
    const revenueChart = document.getElementById('revenueChart');
    const conversionChart = document.getElementById('conversionChart');
    
    if (revenueChart) {
        const ctx = revenueChart.getContext('2d');
        ctx.fillStyle = '#10b981';
        ctx.fillRect(0, 150, 400, 50);
        ctx.fillText('Revenue chart placeholder', 120, 100);
    }
    
    if (conversionChart) {
        const ctx = conversionChart.getContext('2d');
        ctx.fillStyle = '#3b82f6';
        ctx.fillRect(0, 150, 400, 50);
        ctx.fillText('Conversion chart placeholder', 100, 100);
    }
}

// Add chart styles
const style = document.createElement('style');
style.textContent = `
    .analytics-dashboard {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 24px;
    }
    
    canvas {
        max-width: 100%;
        height: auto !important;
    }
`;
document.head.appendChild(style);

// Export functions
window.initializeCharts = initializeCharts;
