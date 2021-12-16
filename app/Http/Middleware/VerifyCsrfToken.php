<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'ajax/product/saveImages',
        'order/get_order_options',
        'ajax/product/deleteGalleryImage',
        'ajax/getSubcategories'
    ];
}
