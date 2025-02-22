<?php

namespace App\Filament\Pages;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
class orderDashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = "Dashboard";
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.order-dashboard';
}
