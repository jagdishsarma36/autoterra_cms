<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = 'Overview';

    protected function getStats(): array
    {
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $monthlyRevenue = Order::where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('total_amount');

        return [
            Stat::make('Total Revenue', '₹' . number_format($totalRevenue / 100))
                ->description('Lifetime earnings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([30, 40, 35, 50, 49, 60, 70, 91, 86, 55, 40, 65]),

            Stat::make('This Month', '₹' . number_format($monthlyRevenue / 100))
                ->description('Revenue this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Total Orders', number_format(Order::count()))
                ->description(Order::where('status', 'paid')->count() . ' paid')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            Stat::make('Active Subscriptions', number_format(Subscription::where('status', 'active')->count()))
                ->description(Subscription::count() . ' total')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Customers', number_format(User::where('role', 'user')->count()))
                ->description(User::count() . ' total users')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),

            Stat::make('Pending Quotes', number_format(QuoteRequest::where('status', 'pending')->count()))
                ->description(QuoteRequest::count() . ' total requests')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('danger'),
        ];
    }
}
