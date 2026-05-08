// ===========================
// Expeditions Page Logic
// ===========================

let expeditions = [];
let isLoading = false;

// Load expeditions from API
async function loadExpeditions() {
    const grid = document.getElementById('expeditionsGrid');
    if (!grid) return;

    try {
        isLoading = true;
        grid.innerHTML = '<div class="empty-state"><div class="spinner"></div></div>';

        const response = await api.getExpeditions();

        // Laravel returns a direct array (not wrapped in .data)
        if (Array.isArray(response) && response.length > 0) {
            expeditions = response;
        } else if (response && Array.isArray(response.data) && response.data.length > 0) {
            expeditions = response.data;
        } else {
            expeditions = [];
        }

        renderExpeditions();
    } catch (error) {
        console.error('Erro ao carregar expedições:', error);
        showNotification('Erro ao carregar expedições. Verifique a conexão.', 'error');
        expeditions = [];
        renderExpeditions();
    } finally {
        isLoading = false;
    }
}

// Render expeditions to grid
function renderExpeditions() {
    const grid = document.getElementById('expeditionsGrid');
    
    if (!grid) return;
    
    if (expeditions.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                </svg>
                <h3>Nenhuma expedição cadastrada</h3>
                <p>Crie a primeira expedição para começar</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = expeditions.map(exp => createExpeditionCard(exp)).join('');
}

// Create expedition card HTML
function createExpeditionCard(exp) {
    const totalSpots = exp.capacity || 0;
    const remaining  = exp.remaining_spots !== undefined ? exp.remaining_spots : totalSpots;
    const booked     = totalSpots - remaining;
    const progress   = totalSpots > 0 ? Math.round((booked / totalSpots) * 100) : 0;

    const trailLabel = {
        EASY: 'Fácil', MODERATE: 'Moderado',
        HARD: 'Difícil', CHALLENGING: 'Desafiador'
    };
    const statusLabel = {
        PLANNING: 'Planejamento', OPEN: 'Aberta', GUARANTEED: 'Confirmada',
        IN_PROGRESS: 'Em Rota', COMPLETED: 'Concluída', CANCELLED: 'Cancelada'
    };
    const statusClass = {
        OPEN: 'badge-gold', GUARANTEED: 'badge-moss', IN_PROGRESS: 'badge-burnt',
        PLANNING: 'badge-terra', COMPLETED: 'badge-success', CANCELLED: 'badge-danger'
    };

    return `
        <div class="expedition-card" onclick="viewExpedition('${exp.id}')">
            <div class="expedition-image">
                <img src="${exp.cover_image || 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800'}" alt="${exp.name}" loading="lazy">
                <div class="expedition-overlay"></div>
                <span class="badge ${statusClass[exp.status] || 'badge-terra'}" style="position:absolute;top:12px;right:12px">
                    ${statusLabel[exp.status] || exp.status}
                </span>
                <div class="expedition-dates">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>${exp.dates || '—'}</span>
                </div>
            </div>

            <div class="expedition-content">
                <div class="expedition-header">
                    <div>
                        <h3 class="expedition-title">${exp.name}</h3>
                        <div class="expedition-location">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>${exp.destination}</span>
                        </div>
                    </div>
                </div>

                <div class="expedition-details">
                    <div class="detail-item">
                        <span class="detail-label">Dificuldade</span>
                        <div class="detail-value">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                            </svg>
                            <span>${trailLabel[exp.trail_level] || exp.trail_level || '—'}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Vagas</span>
                        <div class="detail-value">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>${remaining} restante${remaining !== 1 ? 's' : ''}</span>
                        </div>
                    </div>
                </div>

                <div class="expedition-progress">
                    <div class="progress-header">
                        <span class="progress-label">Ocupação</span>
                        <span class="progress-value">${booked}/${totalSpots} viajantes</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${progress}%"></div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// View expedition details
function viewExpedition(id) {
    showNotification('Detalhes da expedição em breve!', 'info');
}

// Modal functions
function openNewExpeditionModal() {
    const modal = document.getElementById('newExpeditionModal');
    modal.classList.add('active');
}

function closeNewExpeditionModal() {
    const modal = document.getElementById('newExpeditionModal');
    modal.classList.remove('active');
    document.getElementById('newExpeditionForm').reset();
}

// Handle form submission
async function handleSubmitExpedition(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const expeditionData = {
        name:              formData.get('name'),
        destination:       formData.get('destination'),
        dates:             formData.get('dates'),
        trail_level:       formData.get('trail_level'),
        capacity:          parseInt(formData.get('capacity')),
        remaining_spots:   parseInt(formData.get('remaining_spots')),
        costs:             parseFloat(formData.get('costs')),
        accommodation:     formData.get('accommodation'),
        transport:         formData.get('transport'),
        margin_predicted:  parseFloat(formData.get('margin_predicted')),
        status: 'PLANNING'
    };
    
    try {
        const response = await api.createExpedition(expeditionData);
        showNotification('Expedição criada com sucesso!', 'success');
        closeNewExpeditionModal();
        loadExpeditions();
    } catch (error) {
        console.error('Erro ao criar expedição:', error);
        showNotification('Não foi possível criar a expedição. Tente novamente.', 'error');
    }
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    const modal = document.getElementById('newExpeditionModal');
    if (e.target === modal) {
        closeNewExpeditionModal();
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadExpeditions();
});

// Export functions to window
window.loadExpeditions = loadExpeditions;
window.viewExpedition = viewExpedition;
window.openNewExpeditionModal = openNewExpeditionModal;
window.closeNewExpeditionModal = closeNewExpeditionModal;
window.handleSubmitExpedition = handleSubmitExpedition;
