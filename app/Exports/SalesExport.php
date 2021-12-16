<?php

namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView
{
	public $orders;

	public function __construct($orders){

		$this->orders = $orders;

	}	

    public function view(): View
    {
        return view('admin.reports.excel.sales', [
            'orders' => $this->orders
        ]);
    }
}