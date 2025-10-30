<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart of Accounts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .export-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        .summary-table {
            width: 50%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .summary-table th {
            background-color: #e2e2e2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $companyName ?? 'Company Name' }}</div>
        <div class="export-info">
            Chart of Accounts Export<br>
            Format: {{ strtoupper($format) }}<br>
            Date: {{ $exportDate }}<br>
            @if(!empty($filters))
                Filters: {{ implode(', ', array_filter($filters)) }}
            @endif
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th>Account Type</th>
                <th>Normal Balance</th>
                <th>Parent Account</th>
                <th>Level</th>
                <th>Opening Balance</th>
                <th>Current Balance</th>
                <th>Status</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
                <tr>
                    <td>{{ $account->account_code }}</td>
                    <td>{{ $account->account_name }}</td>
                    <td>{{ ucfirst($account->account_type) }}</td>
                    <td>{{ ucfirst($account->normal_balance) }}</td>
                    <td>{{ $account->parent ? $account->parent->account_code . ' - ' . $account->parent->account_name : 'None' }}</td>
                    <td>{{ $account->level }}</td>
                    <td>{{ number_format($account->opening_balance, 2, ',', '.') }}</td>
                    <td>{{ number_format($account->current_balance, 2, ',', '.') }}</td>
                    <td>{{ $account->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $account->description ?? '' }}</td>
                    <td>{{ $account->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $account->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(isset($summary))
        <div class="summary">
            <h3>Summary</h3>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Accounts</td>
                        <td>{{ $summary['total_accounts'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Active Accounts</td>
                        <td>{{ $summary['active_accounts'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Inactive Accounts</td>
                        <td>{{ $summary['inactive_accounts'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Total Assets</td>
                        <td>{{ $summary['by_type']['asset'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Total Liabilities</td>
                        <td>{{ $summary['by_type']['liability'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Total Equity</td>
                        <td>{{ $summary['by_type']['equity'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Total Revenue</td>
                        <td>{{ $summary['by_type']['revenue'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Total Expenses</td>
                        <td>{{ $summary['by_type']['expense'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td>Total Opening Balance</td>
                        <td>{{ number_format($summary['total_opening_balance'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Current Balance</td>
                        <td>{{ number_format($summary['total_current_balance'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Export Date</td>
                        <td>{{ $summary['export_date'] ?? now()->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td>Exported By</td>
                        <td>{{ $summary['exported_by'] ?? auth()->user()->name ?? 'System' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated on {{ now()->format('Y-m-d H:i:s') }}.</p>
        <p>© {{ date('Y') }} {{ $companyName ?? 'Company Name' }}. All rights reserved.</p>
    </div>
</body>
</html>
