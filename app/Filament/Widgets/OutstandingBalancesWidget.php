<?php

// namespace App\Filament\Widgets;

// use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Filament\Widgets\TableWidget;
// use Illuminate\Support\Facades\DB;

// class OutstandingBalancesWidget extends TableWidget
// {
//     use HasWidgetShield;
    
//     protected int | string | array $columnSpan = 'full';
//     protected static ?int $sort = 5;
    
//     // Agregar propiedades para filtros
//     public $selectedMonth;
//     public $selectedYear;
//     public $selectedTeam;
//     public $selectedSeller;

//     public function mount(): void
//     {
//         $this->selectedMonth = $this->getFilterValue('selectedMonth', now()->month);
//         $this->selectedYear = $this->getFilterValue('selectedYear', now()->year);
//         $this->selectedTeam = $this->getFilterValue('selectedTeam', null);
//         $this->selectedSeller = $this->getFilterValue('selectedSeller', null);
//     }

//     protected function getFilterValue($property, $default)
//     {
//         // Si está disponible en la sesión (respaldo)
//         if (session()->has("filament.{$property}")) {
//             return session()->get("filament.{$property}");
//         }
        
//         return $default;
//     }

//     // Listener para actualizar los filtros
//     protected function getListeners(): array
//     {
//         return [
//             'filter-updated' => 'updateFilters',
//         ];
//     }

//     public function updateFilters($filters): void
//     {
//         if (isset($filters['month'])) {
//             $this->selectedMonth = $filters['month'];
//         }
        
//         if (isset($filters['year'])) {
//             $this->selectedYear = $filters['year'];
//         }
        
//         if (isset($filters['team'])) {
//             $this->selectedTeam = $filters['team'];
//         }
        
//         if (isset($filters['seller'])) {
//             $this->selectedSeller = $filters['seller'];
//         }
        
//         $this->refresh();
//     }

//     public function getTableHeading(): string
//     {
//         return 'Saldos Pendientes por Cliente';
//     }

//     public function table(Table $table): Table
//     {
//         $month = $this->selectedMonth;
//         $year = $this->selectedYear;
//         $teamId = $this->selectedTeam;
//         $sellerId = $this->selectedSeller;

//         return $table
//             ->query(
//                 DB::table('orders')
//                     ->join('customers', 'orders.customer_id', '=', 'customers.id')
//                     ->select(
//                         'customers.id',
//                         'customers.name',
//                         DB::raw('SUM(orders.total) as total_sales'),
//                         DB::raw('SUM(orders.total - COALESCE(payments.amount, 0)) as outstanding_balance')
//                     )
//                     ->leftJoin(DB::raw('(SELECT order_id, SUM(amount) as amount FROM payments GROUP BY order_id) as payments'), 
//                         'orders.id', '=', 'payments.order_id')
//                     ->whereYear('orders.issue_date', $year)
//                     ->whereMonth('orders.issue_date', $month)
//                     ->when($teamId, fn($query) => $query->where('orders.team_id', $teamId))
//                     ->when($sellerId, fn($query) => $query->where('orders.seller_id', $sellerId))
//                     ->where(DB::raw('orders.total - COALESCE(payments.amount, 0)'), '>', 0)
//                     ->groupBy('customers.id', 'customers.name')
//                     ->orderByDesc('outstanding_balance')
//             )
//             ->columns([
//                 Tables\Columns\TextColumn::make('name')
//                     ->label('Cliente')
//                     ->searchable(),
//                 Tables\Columns\TextColumn::make('total_sales')
//                     ->label('Ventas Totales')
//                     ->money('USD'),
//                 Tables\Columns\TextColumn::make('outstanding_balance')
//                     ->label('Saldo Pendiente')
//                     ->money('USD')
//                     ->color('danger'),
//                 Tables\Columns\TextColumn::make('percentage_paid')
//                     ->label('% Pagado')
//                     ->state(function ($record) {
//                         $percentagePaid = $record->total_sales > 0 
//                             ? 100 - (($record->outstanding_balance / $record->total_sales) * 100) 
//                             : 0;
//                         return round($percentagePaid, 2) . '%';
//                     })
//                     ->color(fn ($record) => 
//                         $record->total_sales > 0 && (1 - ($record->outstanding_balance / $record->total_sales)) >= 0.9 
//                             ? 'success' 
//                             : 'warning'
//                     ),
//             ]);
//     }
// } 