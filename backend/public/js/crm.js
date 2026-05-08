// ===========================
// CRM Page Logic — Chapada Diamantina
// ===========================

// DB enum: NEW, CONTACTED, QUALIFIED, PROPOSAL, RESERVED, PAID, POST_TRIP, REFERRAL
const PIPELINE_STAGES = [
    { key: 'NEW',       label: 'Novos Leads' },
    { key: 'CONTACTED', label: 'Contatados' },
    { key: 'QUALIFIED', label: 'Qualificados' },
    { key: 'PROPOSAL',  label: 'Proposta Enviada' },
    { key: 'RESERVED',  label: 'Reservados' },
    { key: 'PAID',      label: 'Fechados / Pagos' }
];

const STAGE_COLORS = {
    NEW:       'var(--terra)',
    CONTACTED: 'var(--gold)',
    QUALIFIED: 'var(--moss)',
    PROPOSAL:  'var(--burnt)',
    RESERVED:  '#5c7a3e',
    PAID:      '#2e6b4f'
};

let leads = [];

// ===========================
// Load leads from API
// ===========================
async function loadLeads() {
    try {
        const response = await api.getLeads();

        // Laravel returns a direct array
        if (Array.isArray(response) && response.length > 0) {
            leads = response;
        } else if (response && Array.isArray(response.data) && response.data.length > 0) {
            leads = response.data;
        } else {
            leads = [];
        }

        renderKanbanBoard();
    } catch (error) {
        console.error('Erro ao carregar leads:', error);
        showNotification('Erro ao carregar leads. Verifique a conexão.', 'error');
        leads = [];
        renderKanbanBoard();
    }
}

// ===========================
// Render Kanban board
// ===========================
function renderKanbanBoard() {
    const board = document.getElementById('kanbanBoard');
    if (!board) return;

    board.innerHTML = PIPELINE_STAGES.map(stage => {
        const stageLeads = leads.filter(lead => lead.status === stage.key);
        const stageValue = stageLeads.reduce((sum, l) => sum + (parseFloat(l.estimated_ticket) || 0), 0);

        return `
            <div class="kanban-column" data-stage="${stage.key}">
                <div class="kanban-header">
                    <span class="kanban-stage-dot" style="background:${STAGE_COLORS[stage.key]}"></span>
                    <h3>${stage.label}</h3>
                    <span class="kanban-count">${stageLeads.length}</span>
                </div>
                <div class="kanban-value">${formatCurrency(stageValue)}</div>
                <div class="kanban-cards" data-stage="${stage.key}">
                    ${stageLeads.map(lead => createLeadCard(lead)).join('')}
                    ${stageLeads.length === 0 ? '<div class="kanban-empty">Nenhum lead aqui</div>' : ''}
                </div>
            </div>`;
    }).join('');

    initializeDragAndDrop();
}

// ===========================
// Lead card HTML
// ===========================
function createLeadCard(lead) {
    const sourceIcon = {
        instagram: '📸', whatsapp: '💬', indicacao: '🤝',
        site: '🌐', facebook: '👥', google: '🔍', outros: '📋'
    };
    const followUp = lead.next_follow_up
        ? new Date(lead.next_follow_up).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })
        : '';

    return `
        <div class="kanban-card" draggable="true" data-id="${lead.id}" onclick="viewLead('${lead.id}')">
            <div class="kanban-card-header">
                <strong>${lead.name}</strong>
                <span class="kanban-card-value">${formatCurrency(parseFloat(lead.estimated_ticket) || 0)}</span>
            </div>
            <div class="kanban-card-body">
                <div class="kanban-card-info">
                    <span style="font-size:13px">${sourceIcon[lead.source] || '📋'}</span>
                    <span>${lead.destination || lead.interest || 'Sem destino'}</span>
                </div>
                ${followUp ? `
                <div class="kanban-card-info">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Follow-up: ${followUp}</span>
                </div>` : ''}
            </div>
        </div>`;
}

// ===========================
// Drag & drop
// ===========================
function initializeDragAndDrop() {
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });
    document.querySelectorAll('.kanban-cards').forEach(col => {
        col.addEventListener('dragover', handleDragOver);
        col.addEventListener('drop', handleDrop);
    });
}

let draggedCard = null;

function handleDragStart(e) {
    draggedCard = this;
    this.style.opacity = '0.4';
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragEnd() {
    this.style.opacity = '1';
    draggedCard = null;
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    return false;
}

async function handleDrop(e) {
    e.stopPropagation();
    e.preventDefault();
    if (!draggedCard) return false;

    const leadId = draggedCard.dataset.id;
    const newStatus = this.dataset.stage;

    await updateLeadStatus(leadId, newStatus);
    return false;
}

// ===========================
// Update lead status
// ===========================
async function updateLeadStatus(leadId, newStatus) {
    try {
        await api.updateLeadStatus(leadId, newStatus);

        const lead = leads.find(l => String(l.id) === String(leadId));
        if (lead) lead.status = newStatus;

        renderKanbanBoard();
        showNotification('Lead atualizado!', 'success');
    } catch (error) {
        console.error('Erro ao atualizar lead:', error);
        showNotification('Não foi possível mover o lead.', 'error');
        // Re-render to restore original position
        renderKanbanBoard();
    }
}

// ===========================
// View lead (placeholder)
// ===========================
function viewLead(id) {
    // Prevent drag-click conflict
    if (draggedCard) return;
    showNotification('Painel do lead em breve!', 'info');
}

// ===========================
// New lead modal
// ===========================
function openNewLeadModal() {
    showNotification('Formulário de novo lead em breve!', 'info');
}

// ===========================
// Init
// ===========================
document.addEventListener('DOMContentLoaded', () => {
    loadLeads();
});

// ===========================
// Kanban styles (brand)
// ===========================
const crmStyle = document.createElement('style');
crmStyle.textContent = `
    .kanban-board {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding-bottom: 24px;
        align-items: flex-start;
    }
    .kanban-column {
        flex: 0 0 260px;
        background: var(--cream);
        border: 1px solid rgba(196,168,130,.25);
        border-radius: 12px;
        padding: 18px 16px;
    }
    .kanban-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .kanban-stage-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .kanban-header h3 {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--graphite);
        flex: 1;
    }
    .kanban-count {
        background: var(--graphite);
        color: var(--cream);
        font-size: 11px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 10px;
    }
    .kanban-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--moss);
        font-family: 'Playfair Display', serif;
        margin-bottom: 14px;
    }
    .kanban-cards {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 120px;
    }
    .kanban-empty {
        text-align: center;
        font-size: 12px;
        color: var(--terra);
        opacity: 0.6;
        padding: 20px 0;
    }
    .kanban-card {
        background: #fff;
        border: 1px solid rgba(196,168,130,.2);
        border-radius: 10px;
        padding: 14px;
        cursor: grab;
        transition: box-shadow .2s, transform .2s;
    }
    .kanban-card:hover {
        box-shadow: 0 4px 16px rgba(59,74,47,.12);
        transform: translateY(-2px);
    }
    .kanban-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
        gap: 8px;
    }
    .kanban-card-header strong {
        font-size: 13px;
        font-weight: 600;
        color: var(--graphite);
        line-height: 1.3;
    }
    .kanban-card-value {
        font-size: 12px;
        font-weight: 700;
        color: var(--moss);
        white-space: nowrap;
    }
    .kanban-card-body {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .kanban-card-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #6b5e4e;
    }
    .kanban-card-info svg { flex-shrink: 0; }
`;
document.head.appendChild(crmStyle);

// Export
window.loadLeads        = loadLeads;
window.openNewLeadModal = openNewLeadModal;
window.viewLead         = viewLead;