<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getRangeFromFilter($filter)
    {
        switch ($filter) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $groupBy = 'HOUR(created_at)';
                break;
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $groupBy = 'HOUR(created_at)';
                break;
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $groupBy = 'DAY(created_at)';
                break;
            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end = Carbon::now()->subWeek()->endOfWeek();
                $groupBy = 'DAY(created_at)';
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $groupBy = 'DAY(created_at)';
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                $groupBy = 'DAY(created_at)';
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $groupBy = 'MONTH(created_at)';
                break;
            case 'last_year':
                $start = Carbon::now()->subYear()->startOfYear();
                $end = Carbon::now()->subYear()->endOfYear();
                $groupBy = 'MONTH(created_at)';
                break;
            default:
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $groupBy = 'HOUR(created_at)';
        }

        return [$start, $end, $groupBy];
    }

    public function getPeriodAsPerFilter($filter, $start)
    {
        if (in_array($filter, ['today', 'yesterday'])) {
            for ($i = 0; $i < 24; $i++) {
                $key = sprintf('%02d:00', $i);
                $periods[] = $key;
            }
        } elseif (in_array($filter, ['this_week', 'last_week'])) {
            $periods = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        } elseif (in_array($filter, ['this_month', 'last_month'])) {
            $daysInMonth = $start->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $periods[] = sprintf('%02d', $i);
            }
        } elseif (in_array($filter, ['this_year', 'last_year'])) {
            $periods = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        }

        return $periods;
    }

    public function getAxisData($periods, $dataSet1, $dataSet2)
    {
        $xAxis = [];
        $dataSet1YAxis = [];
        $dataSet2YAxis = [];

        foreach ($periods as $index => $period) {
            $xAxis[] = $period;
            $dataSet1YAxis[] = $dataSet1[$index + 1] ?? 0;
            $dataSet2YAxis[] = $dataSet2[$index + 1] ?? 0;
        }

        return [$xAxis, $dataSet1YAxis, $dataSet2YAxis];
    }

    public function getTransactionsGraphData($filter = 'today')
    {
        $user = Auth::guard('api')->user();
        $data = [];
        list($start, $end, $groupBy) = $this->getRangeFromFilter($filter);

        $paidTransactions = $user->bank->paidTransactions()
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("$groupBy as period, COUNT(id) as total_transactions")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total_transactions', 'period');

        $receivedTransaction = $user->bank->paidTransactionsReceived()
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("$groupBy as period, COUNT(id) as total_transactions")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total_transactions', 'period');

        $periods = $this->getPeriodAsPerFilter($filter, $start);

        list($xAxis, $paidYAxis, $receivedYAxis) = $this->getAxisData($periods, $paidTransactions, $receivedTransaction);

        $data = [
            'paidTransaction' => [
                'xAxis' => $xAxis,
                'yAxis' => $paidYAxis
            ],
            'receivedTransaction' => [
                'xAxis' => $xAxis,
                'yAxis' => $receivedYAxis
            ]
        ];

        return $data;
    }

    public function getAmountGraphData($filter = 'today')
    {
        $user = Auth::guard('api')->user();
        $data = [];
        list($start, $end, $groupBy) = $this->getRangeFromFilter($filter);

        $paidAmount = $user->bank->paidTransactions()
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("$groupBy as period, SUM(amount) as total_amount")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total_amount', 'period');

        $receivedAmount = $user->bank->paidTransactionsReceived()
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("$groupBy as period, SUM(amount) as total_amount")
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total_amount', 'period');

        $periods = $this->getPeriodAsPerFilter($filter, $start);

        list($xAxis, $paidYAxis, $receivedYAxis) = $this->getAxisData($periods, $paidAmount, $receivedAmount);
        
        $data = [
            'paidAmount' => [
                'xAxis' => $xAxis,
                'yAxis' => $paidYAxis
            ],
            'receivedAmount' => [
                'xAxis' => $xAxis,
                'yAxis' => $receivedYAxis
            ]
        ];

        return $data;
    }

    public function getDetail()
    {
        $user = Auth::guard('api')->user();

        return [
            'balance' => $user->bank->balance,
            'totalPaidTransactions' => $user->bank->paidTransactions->count(),
            'totalPaidTransactionsReceived' => $user->bank->paidTransactionsReceived->count(),
            'totalPaidAmount' => $user->bank->paidTransactions()->sum('amount'),
            'totalPaidAmountReceived' => $user->bank->paidTransactionsReceived()->sum('amount'),
            'transactionData' => $this->getTransactionsGraphData('today'),
            'amountData' => $this->getAmountGraphData('today')
        ];
    }
}
