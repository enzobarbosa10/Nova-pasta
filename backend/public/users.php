<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
require_once __DIR__ . '/includes/auth_check.php';

// Only MASTER_ADMIN can access this page
if (($currentUser['role'] ?? '') !== 'MASTER_ADMIN') {
    header('Location: ' . $baseUrl . '/dashboard.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <span class="page-eyebrow">Administração</span>
            <h1 class="page-title">Gestão de Usuários</h1>
            <p class="page-subtitle">Crie e gerencie os acessos ao sistema</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center">
            <button class="btn-primary" onclick="openCreateModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo Usuário
            </button>
        </div>
    </div>

    <!-- Role legend -->
    <div class="users-legend">
        <div class="legend-item">
            <span class="role-badge role-MASTER_ADMIN">MASTER ADMIN</span>
            <span class="legend-desc">Acesso total · Gerencia usuários</span>
        </div>
        <div class="legend-item">
            <span class="role-badge role-ADMIN">ADMIN</span>
            <span class="legend-desc">Dashboards, CRM, operações</span>
        </div>
        <div class="legend-item">
            <span class="role-badge role-OPERATOR">OPERADOR</span>
            <span class="legend-desc">Módulos operacionais</span>
        </div>
        <div class="legend-item">
            <span class="role-badge role-GUIDE">GUIA</span>
            <span class="legend-desc">Expedições e checklists</span>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card" style="padding:0;overflow:hidden;">
        <div class="card-header-row">
            <div class="card-header-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Usuários do Sistema
                <span class="badge-count" id="userCount">—</span>
            </div>
            <div class="search-inline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="tableSearch" placeholder="Buscar por nome ou email…" oninput="filterTable()">
            </div>
        </div>

        <div id="tableWrap">
            <div class="table-loading" id="tableLoading">
                <div class="spinner-md"></div>
                <span>Carregando usuários…</span>
            </div>
            <table class="data-table" id="usersTable" style="display:none">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th>Último Acesso</th>
                        <th>Criado em</th>
                        <th style="text-align:right">Ações</th>
                    </tr>
                </thead>
                <tbody id="usersBody"></tbody>
            </table>
            <div class="table-empty" id="tableEmpty" style="display:none">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity=".3">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
                <p>Nenhum usuário encontrado.</p>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     MODAL: Create / Edit User
================================================================ -->
<div class="modal-overlay" id="userModal" onclick="closeModal(event)">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Novo Usuário</h2>
            <button class="modal-close" onclick="closeUserModal()" aria-label="Fechar">&times;</button>
        </div>

        <div class="modal-alert modal-alert-error" id="modalError" style="display:none"></div>

        <form id="userForm" novalidate>
            <input type="hidden" id="userId">

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label-sm" for="userName">Nome completo <span class="req">*</span></label>
                    <input type="text" id="userName" class="form-input-sm" placeholder="João da Silva" required maxlength="255">
                    <div class="field-err" id="userNameErr"></div>
                </div>
                <div class="form-group">
                    <label class="form-label-sm" for="userEmail">Email <span class="req">*</span></label>
                    <input type="email" id="userEmail" class="form-input-sm" placeholder="joao@email.com" required maxlength="255">
                    <div class="field-err" id="userEmailErr"></div>
                </div>
            </div>

            <div class="form-row-2" id="passwordRow">
                <div class="form-group">
                    <label class="form-label-sm" for="userPassword">Senha temporária <span class="req">*</span></label>
                    <div class="input-pass-wrap">
                        <input type="password" id="userPassword" class="form-input-sm" placeholder="Mín. 8 caracteres" minlength="8" maxlength="255">
                        <button type="button" class="pass-toggle" onclick="togglePassField('userPassword')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="field-err" id="userPasswordErr"></div>
                </div>
                <div class="form-group">
                    <label class="form-label-sm" for="userRole">Cargo / Nível <span class="req">*</span></label>
                    <select id="userRole" class="form-input-sm" required>
                        <option value="">Selecione…</option>
                        <option value="ADMIN">Administrador</option>
                        <option value="OPERATOR">Operador</option>
                        <option value="GUIDE">Guia</option>
                    </select>
                    <div class="field-err" id="userRoleErr"></div>
                </div>
            </div>

            <!-- Edit-only: active toggle -->
            <div id="activeRow" style="display:none;margin-bottom:16px;">
                <label class="toggle-label">
                    <input type="checkbox" id="userActive" checked>
                    <span class="toggle-track"></span>
                    <span class="toggle-text">Conta ativa</span>
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeUserModal()">Cancelar</button>
                <button type="submit" class="btn-primary" id="modalSaveBtn">
                    <span class="btn-text">Criar Usuário</span>
                    <div class="spinner-sm"></div>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL: Reset Password
================================================================ -->
<div class="modal-overlay" id="resetModal" onclick="closeModal(event)">
    <div class="modal-box modal-sm" role="dialog">
        <div class="modal-header">
            <h2 class="modal-title">Redefinir Senha</h2>
            <button class="modal-close" onclick="closeResetModal()" aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-alert modal-alert-error" id="resetError" style="display:none"></div>
        <form id="resetForm" novalidate>
            <input type="hidden" id="resetUserId">
            <p class="modal-desc">Definindo nova senha para: <strong id="resetUserName"></strong></p>
            <div class="form-group">
                <label class="form-label-sm">Nova Senha <span class="req">*</span></label>
                <div class="input-pass-wrap">
                    <input type="password" id="newPassword" class="form-input-sm" placeholder="Mín. 8 caracteres" required minlength="8" maxlength="255">
                    <button type="button" class="pass-toggle" onclick="togglePassField('newPassword')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="field-err" id="newPasswordErr"></div>
            </div>
            <div class="form-group">
                <label class="form-label-sm">Confirmar Senha <span class="req">*</span></label>
                <div class="input-pass-wrap">
                    <input type="password" id="newPasswordConf" class="form-input-sm" placeholder="Repita a senha" required maxlength="255">
                    <button type="button" class="pass-toggle" onclick="togglePassField('newPasswordConf')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="field-err" id="newPasswordConfErr"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeResetModal()">Cancelar</button>
                <button type="submit" class="btn-danger" id="resetSaveBtn">
                    <span class="btn-text">Redefinir Senha</span>
                    <div class="spinner-sm"></div>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL: Confirm Delete
================================================================ -->
<div class="modal-overlay" id="deleteModal" onclick="closeModal(event)">
    <div class="modal-box modal-sm" role="dialog">
        <div class="modal-header">
            <h2 class="modal-title" style="color:#e05252">Excluir Usuário</h2>
            <button class="modal-close" onclick="closeDeleteModal()" aria-label="Fechar">&times;</button>
        </div>
        <p class="modal-desc" style="margin:20px 0 24px">
            Tem certeza que deseja excluir <strong id="deleteUserName"></strong>?<br>
            <span style="color:var(--gray-500);font-size:12px">Esta ação não pode ser desfeita. Todas as sessões serão encerradas.</span>
        </p>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <button type="button" class="btn-danger" id="confirmDeleteBtn">Excluir</button>
        </div>
    </div>
</div>

<style>
/* ── User management page specific styles ── */
.users-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}
.legend-desc {
    font-size: 12px;
    color: var(--gray-500);
}

/* Role badges */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    white-space: nowrap;
}
.role-MASTER_ADMIN { background: rgba(184,134,11,0.15); color: #c49a2a; border: 1px solid rgba(184,134,11,0.3); }
.role-ADMIN        { background: rgba(59,74,47,0.2);    color: #7aad60; border: 1px solid rgba(59,74,47,0.4); }
.role-OPERATOR     { background: rgba(60,100,160,0.15); color: #6b9fd8; border: 1px solid rgba(60,100,160,0.3); }
.role-GUIDE        { background: rgba(140,90,40,0.15);  color: #c4955e; border: 1px solid rgba(140,90,40,0.3); }
.role-TRAVELER     { background: rgba(100,100,100,0.12);color: #9a9a9a; border: 1px solid rgba(100,100,100,0.25); }

/* Status badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 500;
}
.status-active   { background: rgba(15,110,86,0.12); color: #3db891; border: 1px solid rgba(15,110,86,0.25); }
.status-inactive { background: rgba(163,45,45,0.12); color: #e06060; border: 1px solid rgba(163,45,45,0.25); }
.status-dot { width: 6px; height: 6px; border-radius: 50%; }
.status-active .status-dot   { background: #3db891; }
.status-inactive .status-dot { background: #e06060; }

/* Card header row */
.card-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid var(--gray-200);
    gap: 16px;
    flex-wrap: wrap;
}
.card-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
    color: var(--graphite);
}
.badge-count {
    background: var(--moss-bg);
    color: var(--moss);
    border-radius: 20px;
    padding: 1px 8px;
    font-size: 11px;
    font-weight: 600;
}
.search-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 7px 12px;
}
.search-inline svg { color: var(--gray-400); flex-shrink: 0; }
.search-inline input {
    border: none;
    background: transparent;
    font-size: 13px;
    font-family: var(--font-sans);
    color: var(--graphite);
    outline: none;
    width: 200px;
}
.search-inline input::placeholder { color: var(--gray-400); }

/* Table */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 13px 24px; text-align: left; vertical-align: middle; }
.data-table th {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--gray-500);
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}
.data-table tr:not(:last-child) td { border-bottom: 1px solid rgba(44,31,20,0.04); }
.data-table tr:hover td { background: var(--gray-50); }
.data-table td { font-size: 13.5px; color: var(--graphite); }

.user-cell { display: flex; align-items: center; gap: 11px; }
.user-avatar-sm {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--moss-bg);
    color: var(--moss);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600;
    flex-shrink: 0;
    text-transform: uppercase;
}
.user-cell-info { display: flex; flex-direction: column; gap: 1px; }
.user-cell-name  { font-weight: 500; font-size: 13.5px; }
.user-cell-email { font-size: 12px; color: var(--gray-500); }

.table-actions { display: flex; gap: 4px; justify-content: flex-end; }
.action-btn {
    padding: 5px 8px;
    border-radius: 6px;
    border: 1px solid var(--gray-200);
    background: transparent;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    color: var(--gray-600);
    display: flex; align-items: center; gap: 4px;
    transition: all .15s;
    white-space: nowrap;
}
.action-btn:hover { background: var(--gray-200); border-color: var(--gray-300); color: var(--graphite); }
.action-btn.danger:hover { background: rgba(163,45,45,0.08); border-color: rgba(163,45,45,0.3); color: var(--danger); }
.action-btn.success:hover { background: rgba(15,110,86,0.08); border-color: rgba(15,110,86,0.3); color: var(--success); }

.table-loading, .table-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 64px 24px;
    color: var(--gray-400);
    font-size: 13px;
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 24px;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 14px;
    width: 100%;
    max-width: 580px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.2);
    animation: modalIn .18s ease-out;
}
.modal-sm { max-width: 420px; }
@keyframes modalIn { from { opacity:0; transform: scale(.96) translateY(8px); } to { opacity:1; transform: none; } }

.modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 24px 24px 0;
}
.modal-title { font-size: 16px; font-weight: 600; color: var(--graphite); }
.modal-close {
    background: none; border: none; font-size: 22px; cursor: pointer;
    color: var(--gray-400); line-height: 1; padding: 4px 8px;
    border-radius: 6px; transition: background .15s;
}
.modal-close:hover { background: var(--gray-200); color: var(--graphite); }
.modal-desc { padding: 0 24px; font-size: 13.5px; color: var(--gray-600); line-height: 1.6; }

.modal-alert {
    margin: 16px 24px 0;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
}
.modal-alert-error { background: rgba(163,45,45,0.08); border: 1px solid rgba(163,45,45,0.2); color: var(--danger); }
.modal-alert-success { background: rgba(15,110,86,0.08); border: 1px solid rgba(15,110,86,0.2); color: var(--success); }

#userForm, #resetForm { padding: 20px 24px 0; }

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
@media (max-width: 520px) { .form-row-2 { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-label-sm { font-size: 12px; font-weight: 500; color: var(--gray-600); letter-spacing: 0.03em; }
.req { color: var(--danger); }
.form-input-sm {
    padding: 9px 12px;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    font-size: 13.5px;
    font-family: var(--font-sans);
    color: var(--graphite);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    width: 100%;
    background: var(--gray-50);
}
.form-input-sm:focus { border-color: var(--moss); box-shadow: 0 0 0 3px var(--moss-bg); background: #fff; }
.form-input-sm.is-invalid { border-color: var(--danger); }

.input-pass-wrap { position: relative; }
.input-pass-wrap .form-input-sm { padding-right: 36px; }
.pass-toggle {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: var(--gray-400);
    padding: 4px; display: flex; align-items: center;
}
.pass-toggle:hover { color: var(--graphite); }

.field-err { font-size: 11.5px; color: var(--danger); min-height: 16px; }

/* Toggle switch */
.toggle-label { display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; }
.toggle-label input[type="checkbox"] { display: none; }
.toggle-track {
    width: 36px; height: 20px;
    background: var(--gray-300);
    border-radius: 20px;
    position: relative;
    transition: background .2s;
    flex-shrink: 0;
}
.toggle-track::after {
    content: '';
    position: absolute;
    top: 2px; left: 2px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
    transition: transform .2s;
}
.toggle-label input:checked + .toggle-track { background: var(--success); }
.toggle-label input:checked + .toggle-track::after { transform: translateX(16px); }
.toggle-text { font-size: 13px; color: var(--gray-600); }

.modal-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 20px 24px 24px;
    border-top: 1px solid var(--gray-200);
    margin-top: 20px;
}

/* Loading spinners */
.spinner-md {
    width: 28px; height: 28px;
    border: 2.5px solid var(--gray-200);
    border-top-color: var(--moss);
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
.spinner-sm {
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .7s linear infinite;
    display: none;
}
button.loading .btn-text { display: none !important; }
button.loading .spinner-sm { display: inline-block !important; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Danger button */
.btn-danger {
    padding: 9px 18px;
    background: var(--danger);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex; align-items: center; gap: 6px;
    transition: opacity .15s;
}
.btn-danger:hover:not(:disabled) { opacity: .85; }
.btn-danger:disabled { opacity: .5; cursor: not-allowed; }
</style>

<script>
(function () {
    'use strict';

    const BASE  = (() => {
        const path = window.location.pathname;
        return path.replace(/\/[^/]*$/, '');
    })();
    const API   = BASE + '/api/v1';
    const TOKEN = window.AUTH_TOKEN || '';

    let allUsers     = [];
    let editingUserId = null;

    // ── Auth helper ──────────────────────────────────────────
    async function apiFetch(path, opts = {}) {
        const res = await fetch(API + path, {
            ...opts,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                ...(opts.headers || {}),
            },
        });
        const data = await res.json().catch(() => ({}));
        return { ok: res.ok, status: res.status, data };
    }

    // ── Load users ───────────────────────────────────────────
    async function loadUsers() {
        document.getElementById('tableLoading').style.display = 'flex';
        document.getElementById('usersTable').style.display  = 'none';
        document.getElementById('tableEmpty').style.display  = 'none';

        const { ok, data } = await apiFetch('/users');
        document.getElementById('tableLoading').style.display = 'none';

        if (!ok) { showToast('Erro ao carregar usuários.', 'error'); return; }

        allUsers = data;
        document.getElementById('userCount').textContent = data.length;
        renderTable(data);
    }

    function renderTable(users) {
        const tbody = document.getElementById('usersBody');
        const table = document.getElementById('usersTable');
        const empty = document.getElementById('tableEmpty');

        if (!users.length) { table.style.display = 'none'; empty.style.display = 'flex'; return; }

        table.style.display = '';
        empty.style.display = 'none';

        tbody.innerHTML = users.map(u => {
            const initial = (u.name || '?').charAt(0).toUpperCase();
            const lastLogin = u.last_login_at
                ? new Date(u.last_login_at).toLocaleString('pt-BR', {dateStyle:'short',timeStyle:'short'})
                : '—';
            const createdAt = u.created_at
                ? new Date(u.created_at).toLocaleDateString('pt-BR')
                : '—';
            const isMaster = u.role === 'MASTER_ADMIN';

            return `<tr data-id="${u.id}">
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm">${initial}</div>
                        <div class="user-cell-info">
                            <span class="user-cell-name">${escHtml(u.name)}</span>
                            <span class="user-cell-email">${escHtml(u.email)}</span>
                        </div>
                    </div>
                </td>
                <td><span class="role-badge role-${u.role}">${escHtml(u.role_label || u.role)}</span></td>
                <td>
                    <span class="status-badge ${u.active ? 'status-active' : 'status-inactive'}">
                        <span class="status-dot"></span>
                        ${u.active ? 'Ativo' : 'Inativo'}
                    </span>
                </td>
                <td style="color:var(--gray-500);font-size:12.5px">${lastLogin}</td>
                <td style="color:var(--gray-500);font-size:12.5px">${createdAt}</td>
                <td>
                    <div class="table-actions">
                        ${!isMaster ? `
                        <button class="action-btn" onclick="openEditModal('${u.id}')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Editar
                        </button>
                        <button class="action-btn" onclick="openResetModal('${u.id}', '${escHtml(u.name)}')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Senha
                        </button>
                        <button class="action-btn ${u.active ? 'danger' : 'success'}" onclick="toggleActive('${u.id}', ${u.active})">
                            ${u.active
                                ? `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg> Desativar`
                                : `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Ativar`
                            }
                        </button>
                        <button class="action-btn danger" onclick="openDeleteModal('${u.id}', '${escHtml(u.name)}')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            Excluir
                        </button>
                        ` : '<span style="font-size:12px;color:var(--gray-400)">Conta protegida</span>'}
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    // ── Filter table ─────────────────────────────────────────
    window.filterTable = function () {
        const q = document.getElementById('tableSearch').value.toLowerCase();
        const filtered = q
            ? allUsers.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q))
            : allUsers;
        renderTable(filtered);
    };

    // ── Create modal ──────────────────────────────────────────
    window.openCreateModal = function () {
        editingUserId = null;
        document.getElementById('modalTitle').textContent    = 'Novo Usuário';
        document.getElementById('modalSaveBtn').querySelector('.btn-text').textContent = 'Criar Usuário';
        document.getElementById('userForm').reset();
        document.getElementById('activeRow').style.display  = 'none';
        document.getElementById('passwordRow').style.display = '';
        document.getElementById('userId').value             = '';
        clearModalErrors();
        openModal('userModal');
    };

    // ── Edit modal ────────────────────────────────────────────
    window.openEditModal = function (id) {
        const u = allUsers.find(x => x.id === id);
        if (!u) return;
        editingUserId = id;
        document.getElementById('modalTitle').textContent   = 'Editar Usuário';
        document.getElementById('modalSaveBtn').querySelector('.btn-text').textContent = 'Salvar Alterações';
        document.getElementById('userId').value             = id;
        document.getElementById('userName').value           = u.name;
        document.getElementById('userEmail').value          = u.email;
        document.getElementById('userRole').value           = u.role;
        document.getElementById('userActive').checked       = u.active;
        document.getElementById('userPassword').value       = '';
        document.getElementById('passwordRow').style.display = 'none';
        document.getElementById('activeRow').style.display  = '';
        clearModalErrors();
        openModal('userModal');
    };

    window.closeUserModal = function () { closeModalById('userModal'); };

    // ── Form submit ───────────────────────────────────────────
    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        clearModalErrors();

        const name     = document.getElementById('userName').value.trim();
        const email    = document.getElementById('userEmail').value.trim();
        const password = document.getElementById('userPassword').value;
        const role     = document.getElementById('userRole').value;
        const active   = document.getElementById('userActive').checked;
        const isEdit   = !!editingUserId;

        let valid = true;
        if (!name)  { setFieldErr('userName', 'Nome obrigatório.');  valid = false; }
        if (!email) { setFieldErr('userEmail', 'Email obrigatório.'); valid = false; }
        if (!isEdit && !password) { setFieldErr('userPassword', 'Senha obrigatória.'); valid = false; }
        if (!isEdit && password && password.length < 8) { setFieldErr('userPassword', 'Mínimo 8 caracteres.'); valid = false; }
        if (!role)  { setFieldErr('userRole', 'Selecione um cargo.'); valid = false; }
        if (!valid) return;

        const btn = document.getElementById('modalSaveBtn');
        btn.disabled = true; btn.classList.add('loading');

        const body  = isEdit
            ? { name, email, role, active }
            : { name, email, password, role };

        const { ok, data } = await apiFetch(
            isEdit ? '/users/' + editingUserId : '/users',
            { method: isEdit ? 'PUT' : 'POST', body: JSON.stringify(body) }
        );

        btn.disabled = false; btn.classList.remove('loading');

        if (!ok) {
            const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Erro ao salvar.');
            document.getElementById('modalError').textContent = msg;
            document.getElementById('modalError').style.display = '';
            return;
        }

        closeModalById('userModal');
        showToast(isEdit ? 'Usuário atualizado.' : 'Usuário criado com sucesso.', 'success');
        loadUsers();
    });

    // ── Reset password modal ──────────────────────────────────
    window.openResetModal = function (id, name) {
        document.getElementById('resetUserId').value    = id;
        document.getElementById('resetUserName').textContent = name;
        document.getElementById('resetForm').reset();
        document.getElementById('resetError').style.display = 'none';
        openModal('resetModal');
    };
    window.closeResetModal = function () { closeModalById('resetModal'); };

    document.getElementById('resetForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id   = document.getElementById('resetUserId').value;
        const pass = document.getElementById('newPassword').value;
        const conf = document.getElementById('newPasswordConf').value;
        document.getElementById('newPasswordErr').textContent     = '';
        document.getElementById('newPasswordConfErr').textContent = '';

        if (!pass || pass.length < 8) { document.getElementById('newPasswordErr').textContent = 'Mínimo 8 caracteres.'; return; }
        if (pass !== conf) { document.getElementById('newPasswordConfErr').textContent = 'Senhas não coincidem.'; return; }

        const btn = document.getElementById('resetSaveBtn');
        btn.disabled = true; btn.classList.add('loading');

        const { ok, data } = await apiFetch('/users/' + id + '/reset-password', {
            method: 'POST',
            body: JSON.stringify({ password: pass, password_confirmation: conf }),
        });

        btn.disabled = false; btn.classList.remove('loading');

        if (!ok) {
            document.getElementById('resetError').textContent = data.message || 'Erro ao redefinir senha.';
            document.getElementById('resetError').style.display = '';
            return;
        }

        closeModalById('resetModal');
        showToast('Senha redefinida. Usuário precisará fazer login novamente.', 'success');
    });

    // ── Toggle active ─────────────────────────────────────────
    window.toggleActive = async function (id, currentActive) {
        const { ok, data } = await apiFetch('/users/' + id + '/toggle-active', { method: 'PATCH' });
        if (!ok) { showToast(data.message || 'Erro ao alterar status.', 'error'); return; }
        showToast(data.message, 'success');
        loadUsers();
    };

    // ── Delete modal ──────────────────────────────────────────
    let deleteTargetId = null;
    window.openDeleteModal = function (id, name) {
        deleteTargetId = id;
        document.getElementById('deleteUserName').textContent = name;
        openModal('deleteModal');
    };
    window.closeDeleteModal = function () { closeModalById('deleteModal'); };

    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        if (!deleteTargetId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true; btn.textContent = 'Excluindo…';

        const { ok, data } = await apiFetch('/users/' + deleteTargetId, { method: 'DELETE' });

        btn.disabled = false; btn.textContent = 'Excluir';

        if (!ok) { showToast(data.message || 'Erro ao excluir.', 'error'); return; }
        closeModalById('deleteModal');
        showToast('Usuário excluído.', 'success');
        deleteTargetId = null;
        loadUsers();
    });

    // ── Modal helpers ─────────────────────────────────────────
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModalById(id) { document.getElementById(id).classList.remove('open'); }
    window.closeModal = function (e) {
        if (e.target.classList.contains('modal-overlay')) {
            e.currentTarget.classList.remove('open');
        }
    };

    function clearModalErrors() {
        ['userNameErr','userEmailErr','userPasswordErr','userRoleErr'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '';
        });
        const me = document.getElementById('modalError');
        if (me) { me.style.display = 'none'; me.textContent = ''; }
    }

    function setFieldErr(id, msg) {
        const el = document.getElementById(id + 'Err');
        if (el) el.textContent = msg;
        const inp = document.getElementById(id);
        if (inp) inp.classList.add('is-invalid');
    }

    // ── Show/hide password fields ─────────────────────────────
    window.togglePassField = function (id) {
        const inp = document.getElementById(id);
        inp.type = inp.type === 'password' ? 'text' : 'password';
    };

    // ── Toast notifications ───────────────────────────────────
    function showToast(msg, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.textContent = msg;
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // ── HTML escape ───────────────────────────────────────────
    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    // ── Init ──────────────────────────────────────────────────
    loadUsers();

}());
</script>

<style>
/* Toast */
.toast {
    position: fixed;
    bottom: 24px; right: 24px;
    padding: 12px 18px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 500;
    color: #fff;
    z-index: 9999;
    opacity: 0;
    transform: translateY(10px);
    transition: all .25s ease;
    max-width: 340px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}
.toast.show { opacity: 1; transform: none; }
.toast-success { background: var(--success); }
.toast-error   { background: var(--danger); }
.toast-info    { background: var(--moss); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
