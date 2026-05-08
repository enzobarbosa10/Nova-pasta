<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Media Bank</h1>
            <p class="page-subtitle">Manage expedition photos and videos.</p>
        </div>
        <button class="btn-primary" onclick="openUploadModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            Upload Media
        </button>
    </div>

    <div id="mediaGrid" class="media-grid">
        <!-- Media items will be loaded here -->
    </div>
</div>

<script src="/js/media.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
