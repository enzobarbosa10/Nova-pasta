<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">Relacionamento</span>
            <h1 class="page-title">CRM de Leads</h1>
            <p class="page-subtitle">Pipeline de captação e conversão de viajantes</p>
        </div>
        <button class="btn-primary" onclick="openNewLeadModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Novo Lead
        </button>
    </div>

    <div id="kanbanBoard" class="kanban-board">
        <!-- Pipeline Kanban carregado pelo crm.js -->
    </div>
</div>

<script src="<?= $baseUrl ?>/js/crm.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
