<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Saran;
use Illuminate\Http\Request;

class SaranController extends Controller
{
    public function index()
    {
        $sarans = Saran::latest()->get();
        return view('kepala-sekolah.saran.index', compact('sarans'));
    }

    public function updateStatus(Request $request, $id)
    {
        $saran = Saran::findOrFail($id);
        $saran->update(['status' => 'sudah_dibaca']);
        return redirect()->back()->with('success', 'Status saran diperbarui');
    }

    public function destroy($id)
    {
        Saran::findOrFail($id)->delete();
        return redirect()->route('kepala-sekolah.saran.index')->with('success', 'Saran berhasil dihapus');
    }
}
