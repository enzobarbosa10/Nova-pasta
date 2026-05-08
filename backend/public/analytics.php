<?php
session_start();
require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Advanced Analytics</h1>
            <p class="page-subtitle">Deep insights into your expedition business.</p>
        </div>
    </div>

    <div class="analytics-dashboard">
        <div class="chart-grid">
            <div class="card">
                <div class="card-header">
                    <h2>Revenue Trend</h2>
                </div>
                <div class="card-content">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Conversion Funnel</h2>
                </div>
                <div class="card-content">
                    <canvas id="conversionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/analytics.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
