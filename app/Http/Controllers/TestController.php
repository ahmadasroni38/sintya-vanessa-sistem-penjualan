<?php

namespace App\Http\Controllers;

use App\Models\StockMutation;

class TestController extends Controller
{
    public function testStatistics()
    {
        // Get total for current month
        $currentMonthQuery = StockMutation::query()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year);

        $stats = [
            'total_this_month' => $currentMonthQuery->count(),
            'total_transactions' => StockMutation::count(),
            'draft_count' => StockMutation::where('status', 'draft')->count(),
            'pending_count' => StockMutation::where('status', 'pending')->count(),
            'approved_count' => StockMutation::where('status', 'approved')->count(),
            'completed_count' => StockMutation::where('status', 'completed')->count(),
            'cancelled_count' => StockMutation::where('status', 'cancelled')->count(),
            'total_items' => StockMutation::where('status', 'completed')->withCount('details')->get()->sum('details_count'),
        ];

        return response()->json($stats);
    }
}
