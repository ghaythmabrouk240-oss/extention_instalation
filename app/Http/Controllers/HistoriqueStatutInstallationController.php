<?php

namespace App\Http\Controllers;

use App\Models\HistoriqueStatutInstallation;
use Illuminate\Http\Request;

class HistoriqueStatutInstallationController extends Controller
{
    public function index()
    {
        $historiques = HistoriqueStatutInstallation::with('installation')->latest()->get();
        return view('historiques.index', compact('historiques'));
    }
}
