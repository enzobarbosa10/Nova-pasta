// ===========================
// Media Bank Logic
// ===========================

let mediaItems = [];

// Load media
async function loadMedia() {
    const grid = document.getElementById('mediaGrid');
    
    if (!grid) return;
    
    try {
        grid.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
        
        const response = await api.get('/media');
        
        if (response && Array.isArray(response.data) && response.data.length > 0) {
            mediaItems = response.data;
        } else if (Array.isArray(response) && response.length > 0) {
            mediaItems = response;
        } else {
            mediaItems = [];
        }
        
        renderMedia();
    } catch (error) {
        console.error('Erro ao carregar mídia:', error);
        mediaItems = [];
        renderMedia();
    }
}

// Render media
function renderMedia() {
    const grid = document.getElementById('mediaGrid');
    
    if (!grid) return;
    
    if (mediaItems.length === 0) {
        grid.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <h3>Nenhuma mídia cadastrada</h3>
                <p>Faça upload de fotos e vídeos das expedições</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = mediaItems.map(item => `
        <div class="media-item" onclick="previewMedia('${item.id}')">
            <img src="${item.file_path || item.url || ''}" alt="${item.title || item.expedition || 'Mídia'}" loading="lazy">
            <div class="media-overlay">
                <span class="media-expedition">${item.expedition_id ? `Expedição #${item.expedition_id}` : (item.expedition || 'Sem expedição')}</span>
                ${item.title ? `<span class="media-title">${item.title}</span>` : ''}
            </div>
        </div>
    `).join('');
}

// Preview media
function previewMedia(id) {
    const item = mediaItems.find(m => String(m.id) === String(id));
    if (!item) return;
    showNotification(`Mídia: ${item.title || 'Sem título'}`, 'info');
}

// Open upload modal
function openUploadModal() {
    showNotification('Funcionalidade de upload em desenvolvimento. Use a API POST /api/v1/media para enviar arquivos.', 'info');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('media')) {
        loadMedia();
    }
});

// Add media styles
const style = document.createElement('style');
style.textContent = `
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }
    
    .media-item {
        position: relative;
        aspect-ratio: 4/3;
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .media-item:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-lg);
    }
    
    .media-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .media-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        display: flex;
        align-items: flex-end;
        padding: 16px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .media-item:hover .media-overlay {
        opacity: 1;
    }
    
    .media-expedition {
        color: white;
        font-size: 14px;
        font-weight: 600;
    }
`;
document.head.appendChild(style);

// Export functions
window.loadMedia = loadMedia;
window.openUploadModal = openUploadModal;
