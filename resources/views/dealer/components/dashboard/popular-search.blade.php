{{-- Popular Search Table Component --}}
<div class="popular-search-container">
    <div class="activity-header">
        <div class="activity-title">Popular Search</div>
    </div>

    <div class="ps-tabs">
        <div class="ps-tab active" data-tab="body">Body</div>
        <div class="ps-tab" data-tab="make">Make</div>
        <div class="ps-tab" data-tab="model">Model</div>
        <div class="ps-tab" data-tab="feature">Feature</div>
    </div>

    <div class="ps-content">
        <table class="ps-table">
            <thead>
                <tr>
                    <th>Term</th>
                    <th class="text-end">#</th>
                </tr>
            </thead>
            <tbody id="psTableBody">
                @foreach ($popularSearches['body'] as $term => $count)
                    <tr>
                        <td>{{ $term }}</td>
                        <td class="text-end fw-bold">{{ number_format($count) }}</td>
                    </tr>
                @endforeach
                @if (empty($popularSearches['body']))
                    <tr>
                        <td colspan="2" class="text-center py-4 text-muted">No search data found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="ps-footer mt-4 pt-3 border-top">
        <p class="mb-1 text-dark fw-bold" style="font-size: 14px;">Looking for Hot/Cold cars?</p>
        <a href="{{ route('dealer.website.reports.hot-vehicles') }}" class="text-primary text-decoration-none" style="font-size: 13px;">View the report &rarr;</a>
    </div>
</div>

<style>
    .popular-search-container {
        background: #fff;
        border: 1px solid #eef0f2;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        height: 100%;
    }

    .ps-tabs {
        display: flex;
        gap: 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
    }

    .ps-tab {
        padding: 8px 0;
        font-size: 13px;
        font-weight: 500;
        color: #999;
        cursor: pointer;
        position: relative;
    }

    .ps-tab:hover {
        color: #333;
    }

    .ps-tab.active {
        color: #333;
        font-weight: 600;
    }

    .ps-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: #3498db;
    }

    .ps-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ps-table th {
        font-size: 11px;
        text-transform: uppercase;
        color: #bbb;
        padding: 8px 0;
        border-bottom: 1px solid #f5f5f5;
        font-weight: 600;
    }

    .ps-table td {
        font-size: 13px;
        padding: 10px 0;
        border-bottom: 1px solid #f9f9f9;
        color: #444;
    }

    .ps-table tr:last-child td {
        border-bottom: none;
    }
</style>

@push('page-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popularSearches = @json($popularSearches);
            const tableBody = document.getElementById('psTableBody');
            const tabs = document.querySelectorAll('.ps-tab');

            if (!tableBody || tabs.length === 0) return;

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const type = this.getAttribute('data-tab');

                    // Switch active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Update table content
                    const data = popularSearches[type];
                    let html = '';

                    const entries = Object.entries(data);
                    if (entries.length === 0) {
                        html =
                            '<tr><td colspan="2" class="text-center py-4 text-muted">No search data found</td></tr>';
                    } else {
                        entries.forEach(([term, count]) => {
                            html += `
                        <tr>
                            <td>${term}</td>
                            <td class="text-end fw-bold">${Number(count).toLocaleString()}</td>
                        </tr>
                    `;
                        });
                    }
                    tableBody.innerHTML = html;
                });
            });
        });
    </script>
@endpush
