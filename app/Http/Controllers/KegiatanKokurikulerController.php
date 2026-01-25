<?php

namespace App\Http\Controllers;

use App\Models\KegiatanKokurikuler;
use Illuminate\Http\Request;

class KegiatanKokurikulerController extends Controller
{
    public function index()
    {
        $kegiatans = KegiatanKokurikuler::latest()->get();
        return view('kegiatan-kokurikuler.index', compact('kegiatans'));
    }
}
