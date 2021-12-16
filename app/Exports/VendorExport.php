<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VendorExport implements FromView
{
	public $vendors;

	public function __construct($vendors){

		$this->vendors = $vendors;

	}	

    public function view(): View
    {
        return view('admin.reports.excel.vendors', [
            'companies' => $this->vendors
        ]);
    }
}