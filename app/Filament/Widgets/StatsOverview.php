<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            Carbon::createFromDate(Carbon::now()->year, 1, 1)->startOfDay(); // Januari 1 di tahun ini

        // Default end date: Desember tahun ini
        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            Carbon::createFromDate(Carbon::now()->year, 12, 31)->endOfDay();

        // dd('start'.$startDate, 'end'. $endDate);

        //aku mau ambil yang is_expense di kategori = false
        $pemasukkan = Transaction::whereHas('categories', function ($query) {
            $query->where('is_expense', false);
        })->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $pengeluaran = Transaction::whereHas('categories', function ($query){
            $query->where('is_expense', true);
        })->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        // dd($pemasukkan, $pengeluaran);
        return [
            Stat::make('Total Pemasukan', $pemasukkan),
            Stat::make('Total Pengeluaran', $pengeluaran),
            Stat::make('Selisih',  $pemasukkan - $pengeluaran),
        ];
    }
}
