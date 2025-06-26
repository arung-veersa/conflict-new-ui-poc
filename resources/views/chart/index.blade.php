<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HHA Conflict Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- jQuery first, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
            margin: 20px 0;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin: 10px 0;
        }
        
        .connection-status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .connection-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .connection-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn-load {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-load.btn-sm {
            padding: 8px 16px;
            border-radius: 20px;
        }
        
        .btn-load:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .d-flex.gap-2 > * {
            margin-right: 8px;
        }
        
        .d-flex.gap-2 > *:last-child {
            margin-right: 0;
        }
        
        .value-type-selector {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary">
                        <i class="fas fa-chart-pie"></i> Payer Dashboard
                    </h2>
                    <div class="connection-status {{ $connectionStatus ? 'connection-success' : 'connection-error' }}">
                        <i class="fas fa-{{ $connectionStatus ? 'check-circle' : 'exclamation-triangle' }}"></i>
                        {{ $connectionStatus ? 'Connected to Snowflake' : 'Database Connection Failed' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-start">
            <div class="col-md-3">
                <div class="value-type-selector">
                    <div class="mb-3">
                        <div class="mb-2">
                            <label for="fromDate" class="form-label">From Date:</label>
                            <input type="date" id="fromDate" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label for="toDate" class="form-label">To Date:</label>
                            <input type="date" id="toDate" class="form-control">
                        </div>
                        <div id="dateError" class="text-danger small" style="display: none;"></div>
                    </div>
                    
                    <button id="applyFiltersBtn" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    
                    <div class="loading mt-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading data from Snowflake...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div id="summaryStats" class="stats-card mb-3">
                    <div id="statsContent">
                        <div class="row text-center">
                            <div class="col">
                                <small>Total Records</small><br>
                                <strong>---</strong>
                            </div>
                            <div class="col">
                                <small>Conflict#</small><br>
                                <strong>---</strong>
                            </div>
                            <div class="col">
                                <small>Shift Price</small><br>
                                <strong>---</strong>
                            </div>
                            <div class="col">
                                <small>Overlap Price</small><br>
                                <strong>---</strong>
                            </div>
                            <div class="col">
                                <small>Full Price</small><br>
                                <strong>---</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie"></i> 
                            Conflict Data by Type
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <select id="valueType" class="form-select form-select-sm" style="width: auto;">
                                @foreach($valueTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $key === 'CO_TO' ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <button id="loadBtn" class="btn btn-load btn-sm">
                                <i class="fas fa-sync-alt"></i> Load Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div id="noDataMessage" class="text-center text-muted" style="display: none;">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>Click "Load Data" to display the chart or "Apply Filters" to refresh both chart and statistics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        let pieChart = null;
        
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        document.getElementById('loadBtn').addEventListener('click', loadChartDataOnly);
        document.getElementById('applyFiltersBtn').addEventListener('click', applyFilters);
        
        // Add date validation
        document.getElementById('fromDate').addEventListener('change', validateDates);
        document.getElementById('toDate').addEventListener('change', validateDates);
        
        function validateDates() {
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const errorDiv = document.getElementById('dateError');
            
            if (fromDate && toDate && new Date(fromDate) >= new Date(toDate)) {
                errorDiv.textContent = 'To date must be greater than from date';
                errorDiv.style.display = 'block';
                return false;
            } else {
                errorDiv.style.display = 'none';
                return true;
            }
        }
        
        function applyFilters() {
            if (!validateDates()) {
                return;
            }
            loadChartAndSummaryData();
        }
        
        function loadChartDataOnly() {
            loadData(false); // false = chart only
        }
        
        function loadChartAndSummaryData() {
            loadData(true); // true = chart and summary
        }
        
        function loadData(includeSummary = true) {
            const valueType = document.getElementById('valueType').value;
            const fromDate = document.getElementById('fromDate').value;
            const toDate = document.getElementById('toDate').value;
            const loadingDiv = document.querySelector('.loading');
            const loadBtn = document.getElementById('loadBtn');
            const applyBtn = document.getElementById('applyFiltersBtn');
            

            
            // Show loading state
            loadingDiv.style.display = 'block';
            loadBtn.disabled = true;
            applyBtn.disabled = true;
            loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
            
            // Hide no data message
            document.getElementById('noDataMessage').style.display = 'none';
            
            // Prepare data object
            const requestData = { value_type: valueType };
            if (fromDate) requestData.from_date = fromDate;
            if (toDate) requestData.to_date = toDate;
            
            // Use jQuery AJAX instead of fetch for better compatibility
            $.ajax({
                url: '/chart/load-data',
                method: 'POST',
                data: requestData,
                success: function(data) {

                    if (data.success) {
                        renderChart(data.chartData);
                        if (includeSummary) {
                            renderSummaryStats(data.summaryStats);
                        }
                    } else {
                        showError('Error loading data: ' + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    showError('Network error: ' + error);
                },
                complete: function() {
                    // Hide loading state
                    loadingDiv.style.display = 'none';
                    loadBtn.disabled = false;
                    applyBtn.disabled = false;
                    loadBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Load Data';
                    applyBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filters';
                }
            });
        }
        
        function renderChart(chartData) {

            const ctx = document.getElementById('pieChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (pieChart) {
                pieChart.destroy();
            }
            
            if (!chartData.labels || chartData.labels.length === 0) {
                document.getElementById('noDataMessage').style.display = 'block';
                return;
            }
            
            // Format numbers for display
            const formattedData = chartData.datasets[0].data.map(value => parseFloat(value));
            
            pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: formattedData,
                        backgroundColor: chartData.datasets[0].backgroundColor,
                        borderColor: chartData.datasets[0].borderColor,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function renderSummaryStats(stats) {

            const statsDiv = document.getElementById('summaryStats');
            const contentDiv = document.getElementById('statsContent');
            
            if (!stats) {
                console.error('No stats data received');
                return;
            }
            
            // Format numbers for display
            const formatNumber = (num) => parseFloat(num || 0).toLocaleString();
            const formatCurrency = (num) => parseFloat(num || 0).toLocaleString('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            
            contentDiv.innerHTML = `
                <div class="row text-center">
                    <div class="col">
                        <small>Total Records</small><br>
                        <strong>${formatNumber(stats.total_records)}</strong>
                    </div>
                    <div class="col">
                        <small>Conflict#</small><br>
                        <strong>${formatNumber(stats.total_co_to)}</strong>
                    </div>
                    <div class="col">
                        <small>Shift Price</small><br>
                        <strong>${formatCurrency(stats.total_co_sp)}</strong>
                    </div>
                    <div class="col">
                        <small>Overlap Price</small><br>
                        <strong>${formatCurrency(stats.total_co_op)}</strong>
                    </div>
                    <div class="col">
                        <small>Full Price</small><br>
                        <strong>${formatCurrency(stats.total_co_fp)}</strong>
                    </div>
                </div>
            `;
            

        }
        
        function showError(message) {
            console.error('Error:', message);
            
            // Create a simple error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row'));
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {

            // Show no data message initially
            document.getElementById('noDataMessage').style.display = 'block';
        });
    </script>
</body>
</html> 