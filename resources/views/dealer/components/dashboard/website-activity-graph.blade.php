{{-- Website Activity Graph Component --}}
<div class="website-activity-container">
    <div class="activity-header">
        <div class="activity-title">Website Activity</div>
        <div class="activity-actions">
            <div class="dropdown listing-dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Export <i class="bi bi-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item"
                            href="{{ route('dealer.website.dashboard.export-activity', ['from' => $from, 'to' => $to]) }}">Download
                            CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="chart-wrapper">
        <div class="legend-row">
            <div class="legend-item"><span class="legend-box leads"></span> Leads</div>
            <div class="legend-item"><span class="legend-line prev-visits"></span> Prev. Visits</div>
            <div class="legend-item"><span class="legend-line visits"></span> Visits</div>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="activityChart"></canvas>
        </div>

        <div class="inventory-legend">
            <div class="legend-item"><span class="legend-box inventory"></span> Inventory Units</div>
        </div>
        <div style="height: 100px; position: relative; margin-top: -10px;">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>
</div>

<style>
    .website-activity-container {
        background: #fff;
        border: 1px solid #eef0f2;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .activity-title {
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }

    .legend-row {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 15px;
        font-size: 12px;
        color: #666;
    }

    .inventory-legend {
        display: flex;
        justify-content: center;
        margin-top: 10px;
        font-size: 12px;
        color: #666;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-box {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }

    .legend-box.leads {
        background: #e54d42;
    }

    .legend-box.inventory {
        background: #dcd7eb;
        border: 1px solid #7c5cc4;
    }

    .legend-line {
        width: 20px;
        height: 2px;
    }

    .legend-line.visits {
        background: #3498db;
    }

    .legend-line.prev-visits {
        border-top: 2px dashed #95a5a6;
    }

    .listing-dropdown .btn {
        font-size: 13px;
        padding: 6px 12px;
        border-color: #ddd;
        color: #555;
    }

    .listing-dropdown .btn:hover {
        background: #f8f9fa;
        border-color: #ccc;
    }
</style>

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activityCtx = document.getElementById('activityChart');
            const inventoryCtx = document.getElementById('inventoryChart');

            if (!activityCtx || !inventoryCtx) return;

            const activityData = @json($activityData);

            const mainChart = new Chart(activityCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: activityData.labels,
                    datasets: [{
                            label: 'Leads',
                            data: activityData.leads,
                            backgroundColor: '#e54d42',
                            borderRadius: 2,
                            barThickness: 8,
                            yAxisID: 'yLeads'
                        },
                        {
                            label: 'Visits',
                            data: activityData.visits,
                            type: 'line',
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0.4,
                            yAxisID: 'yVisits'
                        },
                        {
                            label: 'Prev. Visits',
                            data: activityData.prevVisits,
                            type: 'line',
                            borderColor: '#bdc3c7',
                            borderWidth: 1.5,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            tension: 0.4,
                            yAxisID: 'yVisits'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#999',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 15
                            }
                        },
                        yVisits: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#999',
                                stepSize: 60
                            },
                            min: 0,
                        },
                        yLeads: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#999',
                                stepSize: 3
                            },
                            min: 0,
                        }
                    }
                }
            });

            const inventoryChart = new Chart(inventoryCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: activityData.labels,
                    datasets: [{
                        label: 'Inventory Units',
                        data: activityData.inventory,
                        fill: true,
                        backgroundColor: 'rgba(220, 215, 235, 0.5)',
                        borderColor: '#7c5cc4',
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false,
                            min: 0
                        }
                    }
                }
            });
        });
    </script>
@endpush
