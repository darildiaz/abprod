<?php
namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\SalesGoal;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesGoalsBySellerChart extends ChartWidget
{
    use HasWidgetShield;
    protected static ?string $heading = 'Metas cumplidas por vendedor';
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

        // Fetch sales goals per seller
        $salesGoals = SalesGoal::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->select('user_id', DB::raw('SUM(amount) as goal_amount'))
            ->groupBy('user_id')
            ->get();

        // Fetch actual sales per seller
        $sales = Order::whereYear('issue_date', $currentYear)
            ->whereMonth('issue_date', $currentMonth)
            ->select('seller_id', DB::raw('SUM(total) as total_sales'))
            ->groupBy('seller_id')
            ->get();

        $sellers = [];
        $progressData = [];
        $backgroundColors = [];

        foreach ($salesGoals as $goal) {
            $sellerId = $goal->user_id;
            $goalAmount = $goal->goal_amount;
            $actualSales = $sales->firstWhere('seller_id', $sellerId)?->total_sales ?? 0;

            // Calculate progress percentage
            $progress = $goalAmount > 0 ? ($actualSales / $goalAmount) * 100 : 0;
            $progress = round($progress, 2);

            // Assign color based on progress
            $color = match (true) {
                $progress < 75 => 'rgba(255, 99, 132, 0.7)', // Red
                $progress < 90 => 'rgba(255, 205, 86, 0.7)', // Yellow
                default => 'rgba(75, 192, 192, 0.7)', // Green
            };

            $sellers[] = $goal->user->name;
            $progressData[] = min($progress, 100); // Cap at 100%
            $backgroundColors[] = $color;
        }

        return [
            'labels' => $sellers,
            'datasets' => [
                [
                    'label' => 'Meta completada (%)',
                    'data' => $progressData,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }
}
