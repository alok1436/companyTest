<?php

namespace App\Exports\Vendor;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderExport implements FromView
{
	public $orders;

	public function __construct($orders){

		$this->orders = $orders;

	}	

    public function view(): View
    {
        return view('vendor.reports.excel.orders', [
            'orders' => $this->orders
        ]);
    }
}