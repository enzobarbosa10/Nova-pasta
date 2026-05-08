<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Catálogo</span>
            <h1 class="page-title">Expedições</h1>
            <p class="page-subtitle">Jornadas cuidadosamente planejadas pela Chapada Diamantina</p>
        </div>
        <button class="btn-primary" onclick="openNewExpeditionModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nova Expedição
        </button>
    </div>

    <div class="expeditions-grid" id="expeditionsGrid">
        <!-- Expedições carregadas pelo expeditions.js -->
    </div>
</div>

<!-- Modal: Nova Expedição -->
<div id="newExpeditionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nova Expedição</h3>
            <button class="modal-close" onclick="closeNewExpeditionModal()">&times;</button>
        </div>
        <form id="newExpeditionForm" onsubmit="handleSubmitExpedition(event)">
            <div class="form-group">
                <label>Nome da Expedição</label>
                <input type="text" name="name" required placeholder="ex.: Vale do Pati — 5 Dias">
            </div>
            <div class="form-group">
                <label>Destino</label>
                <input type="text" name="destination" required placeholder="ex.: Mucugê, Bahia">
            </div>
            <div class="form-group">
                <label>Datas (texto livre)</label>
                <input type="text" name="dates" required placeholder="ex.: 15–20 Mai · 5 dias">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nível da Trilha</label>
                    <select name="trail_level" required>
                        <option value="">Selecione</option>
                        <option value="EASY">Fácil</option>
                        <option value="MODERATE">Moderado</option>
                        <option value="HARD">Difícil</option>
                        <option value="CHALLENGING">Desafiador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Capacidade (vagas)</label>
                    <input type="number" name="capacity" required placeholder="12" min="1">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Vagas Restantes</label>
                    <input type="number" name="remaining_spots" required placeholder="12" min="0">
                </div>
                <div class="form-group">
                    <label>Custo Total (R$)</label>
                    <input type="number" name="costs" required placeholder="2800" min="0" step="0.01">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Acomodação</label>
                    <input type="text" name="accommodation" required placeholder="ex.: Camping / Pousada">
                </div>
                <div class="form-group">
                    <label>Transporte</label>
                    <input type="text" name="transport" required placeholder="ex.: Van 4x4">
                </div>
            </div>
            <div class="form-group">
                <label>Margem Prevista (%)</label>
                <input type="number" name="margin_predicted" required placeholder="35" min="0" max="100" step="0.01">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeNewExpeditionModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Criar Expedição</button>
            </div>
        </form>
    </div>
</div>

<script src="/js/expeditions.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
