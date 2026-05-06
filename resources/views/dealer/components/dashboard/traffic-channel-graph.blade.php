{{-- Visits by Traffic Channel Graph Component --}}
<div class="website-activity-container">
    <div class="activity-header">
        <div class="activity-title">Visits by Traffic Channel</div>
        <div class="activity-actions">
            <div class="dropdown listing-dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportTrafficDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Export <i class="bi bi-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportTrafficDropdown">
                    <li><a class="dropdown-item" href="{{ route('dealer.website.dashboard.export-traffic-day', ['from' => $from, 'to' => $to]) }}"><i class="bi bi-bar-chart-fill me-2"></i> Statistics by Day</a></li>
                    <li><a class="dropdown-item" href="{{ route('dealer.website.dashboard.export-traffic-channel', ['from' => $from, 'to' => $to]) }}"><i class="bi bi-table me-2"></i> Statistics by Channel</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="chart-wrapper">
        <div class="legend-row">
            <div class="legend-item"><span class="legend-line organic"></span> Organic Search</div>
            <div class="legend-item"><span class="legend-line direct"></span> Direct</div>
            <div class="legend-item"><span class="legend-line referral"></span> Referral</div>
            <div class="legend-item"><span class="legend-line social"></span> Social</div>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="trafficChart"></canvas>
        </div>
    </div>
</div>

<style>
    .legend-line.organic {
        background: #3498db;
    }

    .legend-line.direct {
        background: #e67e22;
    }

    .legend-line.referral {
        background: #2ecc71;
    }

    .legend-line.social {
        background: #9b59b6;
    }
</style>

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trafficCtx = document.getElementById('trafficChart');
            if (!trafficCtx) return;

            const trafficData = @json($trafficData);
            const colors = {
                'Organic Search': '#3498db',
                'Direct': '#e67e22',
                'Referral': '#2ecc71',
                'Social': '#9b59b6'
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

            new Chart(trafficCtx.getContext('2d'), {
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
                            },
                            min: 0,
                        }
                    }
                }
            });
        });
    </script>
@endpush
