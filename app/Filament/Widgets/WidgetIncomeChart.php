<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Pendapatan';
    protected static string $color = 'success';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            Carbon::createFromDate(Carbon::now()->year, 1, 1)->startOfDay(); // Januari 1 di tahun ini

        // Default end date: Desember tahun ini
        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            Carbon::createFromDate(Carbon::now()->year, 12, 31)->endOfDay();

        $data = Trend::query(Transaction::whereHas('categories', function ($query) {
            $query->where('is_expense', false);
        }))
            ->dateColumn('date_transaction') // Menyebutkan nama kolom tanggal
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
