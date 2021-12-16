<?php

namespace App\Exports;
use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AllReportExport implements FromView
{
	public $orders;

	public function __construct($orders){

		$this->orders = $orders;

	}	

    public function view(): View
    {
        return view(Auth::user()->roles()->first()->name.'.reports.excel.allReport', [
            'orders' => $this->orders
        ]);
    }
}