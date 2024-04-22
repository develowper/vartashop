<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdController extends Controller
{

    public function index()
    {
        return Inertia::render('Ad/Index', [


        ]);

    }
}
