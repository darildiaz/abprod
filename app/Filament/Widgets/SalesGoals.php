<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\SalesGoal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesGoals extends ChartWidget
{
    protected static ?string $heading = 'Proceso de complimiento de metas por equipo';
    protected static ?string $pollingInterval = '10s'; // Refresh every 10s

    /**
     * Get the chart type (bar chart)
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Fetch data for the chart
     */
    protected function getData(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Fetch sales goals per team
        $salesGoals = SalesGoal::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->select('team_id', DB::raw('SUM(amount) as goal_amount'))
            ->groupBy('team_id')
            ->get();

        // Fetch actual sales per team
        $sales = Order::whereYear('issue_date', $currentYear)
            ->whereMonth('issue_date', $currentMonth)
            ->select('team_id', DB::raw('SUM(total) as total_sales'))
            ->groupBy('team_id')
            ->get();

        $teams = [];
        $progressData = [];
        $backgroundColors = [];

        foreach ($salesGoals as $goal) {
            $teamId = $goal->team_id;
            $goalAmount = $goal->goal_amount;
            $actualSales = $sales->firstWhere('team_id', $teamId)?->total_sales ?? 0;

            // Calculate progress percentage
            $progress = $goalAmount > 0 ? ($actualSales / $goalAmount) * 100 : 0;
            $progress = round($progress, 2);

            // Assign color based on progress
            $color = match (true) {
                $progress < 75 => 'rgba(255, 99, 132, 0.7)', // Red
                $progress < 90 => 'rgba(255, 205, 86, 0.7)', // Yellow
                default => 'rgba(75, 192, 192, 0.7)', // Green
            };

            $teams[] = $goal->team->name;
            $progressData[] = min($progress, 100); // Cap at 100%
            $backgroundColors[] = $color;
        }

        return [
            'labels' => $teams,
            'datasets' => [
                [
                    'label' => 'Metas completada (%)',
                    'data' => $progressData,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }
}
