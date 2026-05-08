<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Expedition Calendar</h1>
            <p class="page-subtitle">View and manage expedition schedules.</p>
        </div>
    </div>

    <div id="calendar" class="calendar-container">
        <!-- Calendar will be rendered here -->
    </div>
</div>

<script src="/js/calendar.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
