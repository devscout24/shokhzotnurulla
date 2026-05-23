{{-- Visits by Traffic Channel Graph Component --}}
<div class="website-activity-container">
    <div class="activity-header">
        <div class="activity-title">Visits by Traffic Channel</div>
        <div class="activity-actions">
            <div class="analytics-export-wrapper">
                <div class="analytics-dropdown">
                    <button class="export-btn" type="button">
                        <i class="bi bi-download"></i> Export <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="dropdown-content shadow border">
                        <a href="{{ route('dealer.website.export-traffic-day', ['from' => $from, 'to' => $to]) }}">
                            <i class="bi bi-bar-chart-fill me-2 text-primary"></i> 
                            Statistics by Day
                        </a>
                        <a href="{{ route('dealer.website.export-traffic-channel', ['from' => $from, 'to' => $to]) }}">
                            <i class="bi bi-table me-2 text-success"></i> 
                            Statistics by Channel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-wrapper">
        <div style="height: 300px; position: relative;">
            <canvas id="trafficChart"></canvas>
        </div>
    </div>
</div>

<style>
    .analytics-dropdown {
        position: relative;
        display: inline-block;
    }

    .export-btn {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        color: #555;
        cursor: pointer;
        transition: all 0.2s;
    }

    .export-btn:hover {
        background: #f8f9fa;
        border-color: #ccc;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #fff;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        z-index: 9999;
        border-radius: 6px;
        margin-top: 5px;
        overflow: hidden;
    }

    .dropdown-content a {
        color: #444;
        padding: 10px 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 13px;
    }

    .dropdown-content a:hover {
        background-color: #f8f9fa;
    }

    .analytics-dropdown:hover .dropdown-content {
        display: block;
    }
</style>

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trafficCtx = document.getElementById('trafficChart');
            if (!trafficCtx) return;

            const trafficData = @json($trafficData);
            const colors = {
                'Direct': '#e05e4d',
                'Google Business Profile': '#1b6ce6',
                'Referral': '#f7943c',
                'Organic Search': '#8e44ad',
                'Paid Social': '#f89db2',
                'Social': '#3498db',
                'Display': '#9bcdf7',
                'Email': '#d1abf6'
            };

            const datasets = Object.entries(trafficData.channels).map(([channel, data]) => ({
                label: channel,
                data: data,
                borderColor: colors[channel] || '#999',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.4,
                fill: false
            }));

            window.trafficChartInstance = new Chart(trafficCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: trafficData.labels,
                    datasets: datasets
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
                        y: {
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                color: '#999',
                                maxTicksLimit: 6
                            },
                            min: 0,
                        }
                    }
                }
            });
        });
    </script>
@endpush
