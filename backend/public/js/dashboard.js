// ===========================
// Dashboard Page Logic — Chapada Diamantina
// ===========================

// Fallback stats used while loading or on error
const FALLBACK_STATS = {
    total_leads: '—',
    new_leads_month: 0,
    total_expeditions: '—',
    active_expeditions: 0,
    pending_tasks: 0,
    total_revenue: 0,
    recent_leads: [],
    upcoming_expeditions: []
};

// ===========================
// Load and render dashboard
// ===========================
async function loadDashboardData() {
    try {
        const stats = await api.getDashboardStats();
        updateDashboardStats(stats);
        renderActivityFeed(stats.recent_leads || []);
        renderUpcomingExpeditions(stats.upcoming_expeditions || []);
        renderWeeklyKpis(stats);
    } catch (error) {
        console.error('Erro ao carregar dados do dashboard:', error);
        updateDashboardStats(FALLBACK_STATS);
    }
}

// ===========================
// Update KPI stat cards
// ===========================
function updateDashboardStats(stats) {
    // Revenue
    const revEl = document.getElementById('stat-revenue');
    if (revEl) {
        revEl.textContent = stats.total_revenue > 0
            ? formatCurrency(stats.total_revenue)
            : 'R$ —';
    }
    const revChange = document.getElementById('stat-revenue-change');
    if (revChange) {
        revChange.textContent = stats.new_leads_month !== undefined
            ? `${stats.new_leads_month} novos leads este mês`
            : '';
    }

    // Leads
    const leadsEl = document.getElementById('stat-leads');
    if (leadsEl) leadsEl.textContent = stats.total_leads;
    const leadsChange = document.getElementById('stat-leads-change');
    if (leadsChange) {
        leadsChange.textContent = stats.new_leads_month !== undefined
            ? `+${stats.new_leads_month} novos este mês`
            : '';
    }

    // Expeditions
    const expEl = document.getElementById('stat-expeditions');
    if (expEl) expEl.textContent = stats.total_expeditions;
    const expChange = document.getElementById('stat-expeditions-change');
    if (expChange) {
        expChange.textContent = stats.active_expeditions !== undefined
            ? `${stats.active_expeditions} ativas agora`
            : '';
    }

    // Conversion rate
    const convEl = document.getElementById('stat-conversion');
    if (convEl) {
        const rate = stats.conversion_rate !== undefined ? stats.conversion_rate : 0;
        convEl.textContent = stats.total_leads > 0 ? `${rate}%` : '—';
    }
    const convChange = document.getElementById('stat-conversion-change');
    if (convChange) {
        const paidLeads = stats.paid_leads !== undefined ? stats.paid_leads : 0;
        convChange.textContent = stats.total_leads > 0
            ? `${paidLeads || ''} leads convertidos`
            : 'Sem dados ainda';
    }

    animateStatNumbers();
}

// ===========================
// Activity feed (recent leads)
// ===========================
function renderActivityFeed(recentLeads) {
    const feed = document.getElementById('activityFeed');
    if (!feed || !recentLeads || recentLeads.length === 0) return;

    const sourceLabel = {
        instagram: 'Instagram', whatsapp: 'WhatsApp', indicacao: 'Indicação',
        site: 'Site', facebook: 'Facebook', google: 'Google', outros: 'Outro canal'
    };

    const statusColors = {
        NEW: 'lead', CONTACTED: 'lead', QUALIFIED: 'expedition',
        PROPOSAL: 'expedition', RESERVED: 'expedition', PAID: 'payment',
        POST_TRIP: 'payment', REFERRAL: 'lead'
    };

    const statusPT = {
        NEW: 'Novo Lead', CONTACTED: 'Lead Contatado', QUALIFIED: 'Lead Qualificado',
        PROPOSAL: 'Proposta Enviada', RESERVED: 'Reserva Confirmada',
        PAID: 'Pagamento Recebido', POST_TRIP: 'Pós-Viagem', REFERRAL: 'Indicação'
    };

    feed.innerHTML = recentLeads.map(lead => {
        const iconClass = statusColors[lead.status] || 'lead';
        const iconLetter = lead.name ? lead.name.charAt(0).toUpperCase() : 'L';
        const src = sourceLabel[lead.source] || lead.source || '';
        const srcStr = src ? ` via ${src}` : '';
        const dest = lead.destination ? ` — ${lead.destination}` : '';
        const label = statusPT[lead.status] || 'Novo Lead';

        return `
            <div class="activity-item">
                <div class="activity-icon ${iconClass}">${iconLetter}</div>
                <div class="activity-content">
                    <strong>${label}:</strong> ${lead.name}${srcStr}${dest}
                    <span class="activity-time">${formatActivityTime(lead.created_at)}</span>
                </div>
            </div>`;
    }).join('');
}

// ===========================
// Upcoming expeditions widget
// ===========================
function renderUpcomingExpeditions(expeditions) {
    const list = document.getElementById('upcomingExpeditionsList');
    if (!list) return;

    if (!expeditions || expeditions.length === 0) {
        list.innerHTML = '<div class="upcoming-item"><span style="font-size:13px;color:var(--terra);opacity:.7">Nenhuma expedição ativa</span></div>';
        return;
    }

    const statusBadge = {
        OPEN:        { label: 'Aberta',     cls: 'badge-gold' },
        GUARANTEED:  { label: 'Confirmada', cls: 'badge-moss' },
        IN_PROGRESS: { label: 'Em Rota',    cls: 'badge-burnt' },
        WAITLIST:    { label: 'Aguardando', cls: 'badge-terra' },
        COMPLETED:   { label: 'Concluída',  cls: 'badge-success' },
        CANCELLED:   { label: 'Cancelada',  cls: 'badge-danger' }
    };

    const dotColor = {
        OPEN: 'var(--gold)', GUARANTEED: 'var(--moss)', IN_PROGRESS: 'var(--burnt)',
        WAITLIST: 'var(--terra)', COMPLETED: '#4CAF50', CANCELLED: '#c62828'
    };

    list.innerHTML = expeditions.map(exp => {
        const badge = statusBadge[exp.status] || { label: exp.status, cls: 'badge-terra' };
        const color = dotColor[exp.status] || 'var(--terra)';
        const spotsLeft = exp.remaining_spots !== undefined
            ? `${exp.remaining_spots} vagas`
            : '';
        const dateLabel = [exp.dates, spotsLeft].filter(Boolean).join(' · ');

        return `
            <div class="upcoming-item">
                <div class="upcoming-dot" style="background:${color}"></div>
                <div class="upcoming-info">
                    <span class="upcoming-name">${exp.name}</span>
                    <span class="upcoming-date">${dateLabel || exp.destination || ''}</span>
                </div>
                <span class="badge ${badge.cls}">${badge.label}</span>
            </div>`;
    }).join('');
}

// ===========================
// Activity time formatter
// ===========================
function formatActivityTime(timestamp) {
    if (!timestamp) return '';
    const now = new Date();
    const then = new Date(timestamp);
    const diffMin = Math.floor((now - then) / 60000);
    const diffHours = Math.floor(diffMin / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMin < 2)    return 'Agora mesmo';
    if (diffMin < 60)   return `Há ${diffMin} min`;
    if (diffHours < 24) return `Há ${diffHours} hora${diffHours !== 1 ? 's' : ''}`;
    if (diffDays === 1) return 'Ontem';
    return `Há ${diffDays} dias`;
}

// ===========================
// Stat number animation
// ===========================
function animateStatNumbers() {
    document.querySelectorAll('.stat-value').forEach((el, i) => {
        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(8px)';
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 60 + i * 80);
    });
}

// ===========================
// Manual refresh button
// ===========================
function refreshDashboard() {
    const btn = document.querySelector('[onclick="refreshDashboard()"]');
    if (btn) { btn.style.opacity = '0.6'; btn.style.pointerEvents = 'none'; }
    loadDashboardData().finally(() => {
        if (btn) { btn.style.opacity = '1'; btn.style.pointerEvents = ''; }
        if (typeof showNotification === 'function') {
            showNotification('Dashboard atualizado', 'success');
        }
    });
}

// ===========================
// Weekly KPI sidebar
// ===========================
function renderWeeklyKpis(stats) {
    const leadsWeek = stats.leads_this_week ?? 0;
    const convWeek  = stats.conversions_this_week ?? 0;
    const convRate  = stats.conversion_rate ?? 0;
    const revWeek   = stats.revenue_this_week ?? 0;

    // Calculate bar widths relative to each other
    const maxLeads = Math.max(leadsWeek, 1);

    const setKpi = (valId, barId, text, pct) => {
        const el = document.getElementById(valId);
        const bar = document.getElementById(barId);
        if (el) el.textContent = text;
        if (bar) bar.style.width = `${Math.min(pct, 100)}%`;
    };

    setKpi('kpi-leads-week', 'kpi-leads-bar', leadsWeek, leadsWeek > 0 ? 100 : 0);
    setKpi('kpi-conversions-week', 'kpi-conversions-bar', convWeek, maxLeads > 0 ? (convWeek / maxLeads) * 100 : 0);
    setKpi('kpi-conv-rate', 'kpi-conv-rate-bar', convRate > 0 ? `${convRate}%` : '—', convRate);
    setKpi('kpi-revenue-week', 'kpi-revenue-bar', revWeek > 0 ? formatCurrency(revWeek) : 'R$ —', revWeek > 0 ? 60 : 0);
}

// ===========================
// Init
// ===========================
document.addEventListener('DOMContentLoaded', () => {
    loadDashboardData();
    setInterval(loadDashboardData, 5 * 60 * 1000);
});

window.loadDashboardData = loadDashboardData;
window.refreshDashboard  = refreshDashboard;
