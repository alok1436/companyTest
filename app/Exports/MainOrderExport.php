<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MainOrderExport implements FromView
{
	public $orders;

	public function __construct($orders){

		$this->orders = $orders;

	}	

    public function view(): View
    {
        return view('admin.reports.excel.main_orders', [
            'orders' => $this->orders
        ]);
    }
}