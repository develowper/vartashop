<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShopController extends Controller
{
    public
    function index()
    {
        return Inertia::render('Shop/Index', [

        ]);

    }

    public
    function cartPage()
    {
        return Inertia::render('Shop/Cart', [

        ]);

    }

    public
    function shippingPage()
    {
        return Inertia::render('Shop/Cart', [

        ]);

    }   public
    function paymentPage()
    {
        return Inertia::render('Shop/Cart', [

        ]);

    }
}
