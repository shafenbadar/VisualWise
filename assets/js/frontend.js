document.addEventListener('DOMContentLoaded', function () {
    const visualizerApp = document.getElementById('visualwise-jules-visualizer-app');
    if (!visualizerApp) return;

    const addModuleButtons = visualizerApp.querySelectorAll('.add-module-btn');
    const controlsPanel = visualizerApp.querySelector('#controls-panel');
    const moduleTemplate = visualizerApp.querySelector('#module-control-template');
    const projectionChartCanvas = visualizerApp.querySelector('#projectionChart');
    const chartToggles = visualizerApp.querySelectorAll('.chart-toggle');

    // Summary elements
    const totalTrafficEl = visualizerApp.querySelector('#total-traffic');
    const totalLeadsEl = visualizerApp.querySelector('#total-leads');
    const totalSalesEl = visualizerApp.querySelector('#total-sales');

    let projectionChart = null;
    let activeModules = {}; // Store data for active modules by unique ID

    // --- Currency Formatting ---
    function formatCurrency(amount) {
        const { symbol, position, spacing } = visualwise_jules_ajax_object.currency;
        const formattedAmount = parseFloat(amount).toFixed(2);
        if (position === 'prefix') {
            return symbol + spacing + formattedAmount;
        } else {
            return formattedAmount + spacing + symbol;
        }
    }

    // --- Chart Initialization ---
    function initChart() {
        if (!projectionChartCanvas) return;
        const ctx = projectionChartCanvas.getContext('2d');
        projectionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // Months
                datasets: [] // Sales, Traffic, Leads
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    // Potentially format based on metric type (currency, number)
                                    if (context.dataset.metricType === 'currency') {
                                        label += formatCurrency(context.parsed.y);
                                    } else {
                                        label += context.parsed.y;
                                    }
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // --- Add Module ---
    addModuleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const moduleType = this.getAttribute('data-module-type');
            addModuleToPanel(moduleType);
            if(controlsPanel.querySelector('.empty-panel-message')) {
                controlsPanel.querySelector('.empty-panel-message').style.display = 'none';
            }
        });
    });

    function addModuleToPanel(type) {
        if (!moduleTemplate) return;

        const clone = moduleTemplate.content.cloneNode(true);
        const moduleInstance = clone.querySelector('.module-instance');
        const uniqueId = type + '_' + Date.now(); // Simple unique ID
        moduleInstance.setAttribute('data-module-id', uniqueId);
        moduleInstance.dataset.moduleType = type; // Store type for AJAX

        // Customize module based on type
        const title = clone.querySelector('.module-title');
        const budgetTypeSpan = clone.querySelector('.budget-type');
        const budgetSlider = clone.querySelector('.budget-slider');
        const budgetValueDisplay = clone.querySelector('.budget-value');
        const timespanSlider = clone.querySelector('.timespan-slider');
        const timespanValueDisplay = clone.querySelector('.timespan-value');

        let moduleName = '';
        if (type === 'google_ads') {
            moduleName = 'Google Ads';
            budgetTypeSpan.textContent = 'Daily';
            budgetSlider.min = 1; budgetSlider.max = 500; budgetSlider.step = 1; budgetSlider.value = 50;
        } else if (type === 'seo') {
            moduleName = 'SEO';
            budgetTypeSpan.textContent = 'Monthly';
            budgetSlider.min = 100; budgetSlider.max = 5000; budgetSlider.step = 50; budgetSlider.value = 500;
        } else if (type === 'meta_ads') {
            moduleName = 'Meta Ads';
            budgetTypeSpan.textContent = 'Daily';
            budgetSlider.min = 1; budgetSlider.max = 500; budgetSlider.step = 1; budgetSlider.value = 50;
        }
        title.textContent = moduleName;
        budgetValueDisplay.textContent = budgetSlider.value; // Show initial value
        timespanValueDisplay.textContent = timespanSlider.value; // Show initial value

        // Update IDs for labels
        clone.querySelector('label[for^="budget_"]').htmlFor = 'budget_' + uniqueId;
        budgetSlider.id = 'budget_' + uniqueId;
        clone.querySelector('label[for^="timespan_"]').htmlFor = 'timespan_' + uniqueId;
        timespanSlider.id = 'timespan_' + uniqueId;


        // Store initial values
        activeModules[uniqueId] = {
            id: uniqueId,
            type: type,
            budget: parseFloat(budgetSlider.value),
            timespan: parseInt(timespanSlider.value)
        };

        controlsPanel.appendChild(clone);
        attachModuleEventListeners(moduleInstance);
        triggerAjaxCalculation();
    }

    // --- Attach Event Listeners to Module ---
    function attachModuleEventListeners(moduleElement) {
        const moduleId = moduleElement.dataset.moduleId;

        // Remove button
        moduleElement.querySelector('.remove-module-btn').addEventListener('click', function () {
            delete activeModules[moduleId];
            moduleElement.remove();
            if (Object.keys(activeModules).length === 0 && controlsPanel.querySelector('.empty-panel-message')) {
                 controlsPanel.querySelector('.empty-panel-message').style.display = 'block';
            }
            triggerAjaxCalculation();
        });

        // Sliders
        moduleElement.querySelectorAll('.module-slider').forEach(slider => {
            slider.addEventListener('input', function () {
                const valueDisplay = this.parentElement.querySelector('.slider-value');
                valueDisplay.textContent = this.value;
                activeModules[moduleId][this.name] = this.name === 'budget' ? parseFloat(this.value) : parseInt(this.value);
                triggerAjaxCalculation();
            });
        });
    }

    // --- AJAX Calculation ---
    let debounceTimer;
    function triggerAjaxCalculation() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (Object.keys(activeModules).length === 0) {
                // Clear chart and summary if no modules are active
                updateChart([], { labels: [], datasets: [] });
                updateSummary({ traffic: 0, leads: 0, sales: 0 });
                if(projectionChart && projectionChart.data.datasets.length > 0){
                    projectionChart.data.labels = [];
                    projectionChart.data.datasets = [];
                    projectionChart.update();
                }
                return;
            }

            jQuery.ajax({
                url: visualwise_jules_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'visualwise_jules_calculate_projections',
                    nonce: visualwise_jules_ajax_object.nonce,
                    modules: JSON.stringify(Object.values(activeModules)) // Send array of active modules
                },
                success: function (response) {
                    if (response.success) {
                        // Update individual module displays
                        for (const modId in response.data.modules) {
                            const moduleData = response.data.modules[modId];
                            const moduleElement = controlsPanel.querySelector(`.module-instance[data-module-id="${modId}"]`);
                            if (moduleElement) {
                                moduleElement.querySelector('.projection-traffic').textContent = parseFloat(moduleData.traffic).toLocaleString();
                                moduleElement.querySelector('.projection-leads').textContent = parseFloat(moduleData.leads).toLocaleString();
                                moduleElement.querySelector('.projection-sales').textContent = formatCurrency(moduleData.sales);
                            }
                        }
                        // Update summary
                        updateSummary(response.data.totals);
                        // Update chart
                        updateChartWithData(response.data.graph_data);
                    } else {
                        console.error('AJAX Error:', response.data);
                        // Handle error display if needed
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Request Failed:', textStatus, errorThrown);
                }
            });
        }, 500); // Debounce requests
    }

    // --- Update UI Elements ---
    function updateSummary(totals) {
        totalTrafficEl.textContent = parseFloat(totals.traffic).toLocaleString();
        totalLeadsEl.textContent = parseFloat(totals.leads).toLocaleString();
        totalSalesEl.textContent = formatCurrency(totals.sales);
    }

    function updateChartWithData(graphData) {
        if (!projectionChart || !graphData) return;

        projectionChart.data.labels = graphData.labels || [];

        const newDatasets = [];
        const visibleMetrics = {};
        chartToggles.forEach(toggle => {
            visibleMetrics[toggle.dataset.metric] = toggle.checked;
        });

        // This is a simplified version. The backend AJAX response should provide datasets structured for Chart.js
        // For now, the dummy AJAX provides one dataset for 'Total Projected Sales'
        // We need to expand this to include Traffic and Leads, and potentially per-module lines.

        // Example: if graphData.datasets is an array of Chart.js dataset objects
        if (graphData.datasets && Array.isArray(graphData.datasets)) {
            graphData.datasets.forEach(ds => {
                let metricType = 'number'; // default
                if (ds.label && ds.label.toLowerCase().includes('sales')) {
                     metricType = 'currency';
                     if (!visibleMetrics.sales) return; // Skip if not toggled
                } else if (ds.label && ds.label.toLowerCase().includes('traffic')) {
                     if (!visibleMetrics.traffic) return;
                } else if (ds.label && ds.label.toLowerCase().includes('leads')) {
                     if (!visibleMetrics.leads) return;
                }
                ds.metricType = metricType; // Pass to tooltip formatter
                newDatasets.push(ds);
            });
        }

        projectionChart.data.datasets = newDatasets;
        projectionChart.update();
    }

    // Chart metric toggles
    chartToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            // This ideally should re-filter existing data or re-fetch if necessary
            // For now, let's just re-trigger calculation which will rebuild datasets
            triggerAjaxCalculation();
        });
    });


    // --- Initial Setup ---
    initChart();
    if (Object.keys(activeModules).length === 0 && controlsPanel.querySelector('.empty-panel-message')) {
        controlsPanel.querySelector('.empty-panel-message').style.display = 'block';
    } else if (controlsPanel.querySelector('.empty-panel-message')) {
         controlsPanel.querySelector('.empty-panel-message').style.display = 'none';
    }
});
