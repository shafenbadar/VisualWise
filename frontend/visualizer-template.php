<div id="visualwise-jules-visualizer-app">
    <div class="visualizer-container">
        <div class="module-selection-area">
            <h3>Add Projection Module</h3>
            <button class="add-module-btn" data-module-type="google_ads">Add Google Ads</button>
            <button class="add-module-btn" data-module-type="seo">Add SEO</button>
            <button class="add-module-btn" data-module-type="meta_ads">Add Meta Ads</button>
            <!-- More modules can be added here -->
        </div>

        <div class="main-content-area">
            <div id="controls-panel" class="controls-panel-area scrollable">
                <p class="empty-panel-message">Select a module to begin.</p>
                <!-- Modules will be dynamically added here -->
            </div>

            <div id="chart-area-container" class="chart-area-container scrollable">
                <div class="chart-toggles">
                    <label><input type="checkbox" class="chart-toggle" data-metric="sales" checked> Sales</label>
                    <label><input type="checkbox" class="chart-toggle" data-metric="traffic"> Traffic</label>
                    <label><input type="checkbox" class="chart-toggle" data-metric="leads"> Leads</label>
                </div>
                <canvas id="projectionChart"></canvas>
            </div>
        </div>
    </div>

    <div id="summary-section" class="summary-section">
        <h3>Combined Projections Summary</h3>
        <p>Total Projected Traffic: <span id="total-traffic">0</span></p>
        <p>Total Projected Leads: <span id="total-leads">0</span></p>
        <p>Total Projected Sales: <span id="total-sales">0</span></p>
    </div>
</div>

<!-- Template for a generic module control -->
<template id="module-control-template">
    <div class="module-instance" data-module-id="">
        <div class="module-header">
            <h4 class="module-title">Module Title</h4>
            <button class="remove-module-btn" title="Remove Module">X</button>
        </div>
        <div class="module-controls">
            <div class="slider-group">
                <label for="budget_">Budget (<span class="budget-type"></span>): <span class="slider-value budget-value">0</span></label>
                <input type="range" id="budget_" class="module-slider budget-slider" name="budget" min="0" max="1000" step="10">
            </div>
            <div class="slider-group">
                <label for="timespan_">Timespan (Months): <span class="slider-value timespan-value">1</span></label>
                <input type="range" id="timespan_" class="module-slider timespan-slider" name="timespan" min="1" max="24" step="1" value="1">
            </div>
        </div>
        <div class="module-projections">
            <p>Projected Traffic: <span class="projection-traffic">0</span></p>
            <p>Projected Leads: <span class="projection-leads">0</span></p>
            <p>Projected Sales: <span class="projection-sales">0</span></p>
        </div>
    </div>
</template>
