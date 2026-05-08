// ===========================
// API Configuration — Chapada Diamantina
// ===========================
// Detect the base path dynamically — works both at root (vhost) and sub-path (XAMPP default)
(function() {
    const scripts = document.querySelectorAll('script[src]');
    let basePath = '';
    scripts.forEach(s => {
        const m = s.src.match(/^(.*?)\/js\/api\.js/);
        if (m) basePath = m[1];
    });
    window.API_BASE = basePath;
})();
const API_BASE_URL = (window.API_BASE || '') + '/api/v1';

// Build auth headers — token injected by PHP header.php
function _authHeaders() {
    const token = window.AUTH_TOKEN || '';
    const h = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (token) h['Authorization'] = 'Bearer ' + token;
    return h;
}

// API Helper Functions
const api = {
    // GET Request
    async get(endpoint) {
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                method: 'GET',
                headers: _authHeaders(),
                credentials: 'same-origin',
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = (window.API_BASE || '') + '/login.php';
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API GET Error:', error);
            throw error;
        }
    },

    // POST Request
    async post(endpoint, data) {
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                method: 'POST',
                headers: _authHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = (window.API_BASE || '') + '/login.php';
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API POST Error:', error);
            throw error;
        }
    },

    // PUT Request
    async put(endpoint, data) {
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                method: 'PUT',
                headers: _authHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = (window.API_BASE || '') + '/login.php';
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API PUT Error:', error);
            throw error;
        }
    },

    // DELETE Request
    async delete(endpoint) {
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                method: 'DELETE',
                headers: _authHeaders(),
                credentials: 'same-origin',
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = (window.API_BASE || '') + '/login.php';
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API DELETE Error:', error);
            throw error;
        }
    },

    // PATCH Request
    async patch(endpoint, data) {
        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                method: 'PATCH',
                headers: _authHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            if (response.status === 401 || response.status === 403) {
                window.location.href = (window.API_BASE || '') + '/login.php';
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API PATCH Error:', error);
            throw error;
        }
    },

    // Specific API Endpoints
    getExpeditions() {
        return this.get('/expeditions');
    },

    getExpedition(id) {
        return this.get(`/expeditions/${id}`);
    },

    createExpedition(data) {
        return this.post('/expeditions', data);
    },

    updateExpedition(id, data) {
        return this.put(`/expeditions/${id}`, data);
    },

    deleteExpedition(id) {
        return this.delete(`/expeditions/${id}`);
    },

    getLeads(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.get('/leads' + (query ? '?' + query : ''));
    },

    createLead(data) {
        return this.post('/leads', data);
    },

    updateLead(id, data) {
        return this.put(`/leads/${id}`, data);
    },

    updateLeadStatus(id, status) {
        return this.patch(`/leads/${id}/status`, { status });
    },

    getDashboardStats() {
        return this.get('/dashboard/stats');
    },

    getAnalytics() {
        return this.get('/dashboard/stats');
    }
};

// Export for use in other files
window.api = api;
