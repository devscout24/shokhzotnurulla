@extends('layouts.dealer.app')

@section('title', __('Website Reports') . ' | ' . __(config('app.name')))

@push('page-styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Shared Dashboard Styles */
        .rc-wrapper {
            display: flex;
            gap: 40px;
            min-height: calc(100vh - 160px);
            padding: 20px 0;
            align-items: flex-start;
        }

        .rc-sidebar {
            width: 280px;
            min-width: 280px;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .rc-sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            border-bottom: 1px solid #f8f9fa;
        }

        .rc-sidebar-item:last-child {
            border-bottom: none;
        }

        .rc-sidebar-item:hover {
            background: #fdfdfd;
            color: #333;
        }

        .rc-sidebar-item.active {
            background: #fff;
            color: #ce4f4b;
            font-weight: 600;
        }

        .rc-sidebar-item i {
            font-size: 16px !important;
            width: 24px;
            text-align: center;
            color: #999;
        }

        .rc-sidebar-item.active i {
            color: #ce4f4b !important;
        }

        .rc-main-container {
            flex: 1;
            background: #fff;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 400px;
            justify-content: center;
            align-items: center;
            padding: 60px;
            text-align: center;
        }

        .report-placeholder-text {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .report-support-text {
            color: #999;
            font-size: 14px;
        }

        .report-support-text a {
            color: #ce4f4b;
            text-decoration: none;
        }

        .report-support-text a:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('page-content')
    <main class="main-content" id="mainContent" style="padding:0; background: #f0f2f5; min-height: 100vh;">
        <div style="padding: 30px 45px;">
            <div class="page-header" style="margin-bottom: 25px; border: none; background: transparent; padding: 0;">
                <h2 class="view-title" style="font-size: 24px; font-weight: 700; color: #222;">
                    {{ __('Website Reports') }}</h2>
            </div>

            <div class="rc-wrapper">
                @include('dealer.partials.reports-sidebar')

                <div class="rc-main-container">
                    <p class="report-placeholder-text">Please select a report from the menu on the left.</p>
                    <p class="report-support-text">
                        If you'd like to request a report, please contact <a href="mailto:support@overfuel.com">support@overfuel.com</a>.
                    </p>
                </div>
            </div>
        </div>
    </main>
@endsection
