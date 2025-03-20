<?php

// namespace App\Filament\Widgets;

// use App\Models\User;
// use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Filament\Widgets\TableWidget;

// class TopSellersWidget extends TableWidget
// {
//     use HasWidgetShield;
    
//     protected int | string | array $columnSpan = 'full';
//     protected static ?int $sort = 4;
    
//     // Agregar propiedades para filtros
//     public $selectedMonth;
//     public $selectedYear;
//     public $selectedTeam;

//     public function mount(): void
//     {
//         $this->selectedMonth = $this->getFilterValue('selectedMonth', now()->month);
//         $this->selectedYear = $this->getFilterValue('selectedYear', now()->year);
//         $this->selectedTeam = $this->getFilterValue('selectedTeam', null);
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
        
//         $this->refresh();
//     }

//     public function getTableHeading(): string
//     {
//         return 'Top Vendedores';
//     }

//     public function table(Table $table): Table
//     {
//         $month = $this->selectedMonth;
//         $year = $this->selectedYear;
//         $teamId = $this->selectedTeam;

//         return $table
//             ->query(
//                 User::role('seller')
//                     ->when($teamId, fn($query) => $query->whereHas('teams', fn($q) => $q->where('team_id', $teamId)))
//                     ->withSum(['orders' => function ($query) use ($month, $year) {
//                         $query->whereYear('issue_date', $year)
//                             ->whereMonth('issue_date', $month);
//                     }], 'total')
//                     ->withCount(['orders' => function ($query) use ($month, $year) {
//                         $query->whereYear('issue_date', $year)
//                             ->whereMonth('issue_date', $month);
//                     }])
//                     ->orderByDesc('orders_sum_total')
//             )
//             ->columns([
//                 Tables\Columns\TextColumn::make('name')
//                     ->label('Vendedor')
//                     ->searchable(),
//                 Tables\Columns\TextColumn::make('orders_sum_total')
//                     ->label('Total de Ventas')
//                     ->money('USD')
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('orders_count')
//                     ->label('Órdenes')
//                     ->sortable(),
//                 Tables\Columns\TextColumn::make('avgTicket')
//                     ->label('Ticket Promedio')
//                     ->money('USD')
//                     ->state(function ($record) {
//                         return $record->orders_count > 0 ? $record->orders_sum_total / $record->orders_count : 0;
//                     }),
//             ])
//             ->defaultSort('orders_sum_total', 'desc');
//     }
// } 