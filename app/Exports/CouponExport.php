<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CouponExport implements FromView
{
	public $coupons;

	public function __construct($coupons){

		$this->coupons = $coupons;

	}	

    public function view(): View
    {
        return view('admin.reports.excel.coupons', [
            'coupons' => $this->coupons
        ]);
    }
}