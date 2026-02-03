<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Exports\UserSurveyExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserSurvey; // Pastikan import Model

class DataSurveyController extends Controller
{
    /**
     * Menampilkan Daftar Seluruh Data Survey
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $surveys = UserSurvey::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('review', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $surveys->appends(['search' => $search]);

        // --- TAMBAHAN LOGIKA AJAX ---
        if ($request->ajax()) {
            // Jika request datang dari Javascript (Live Search), kembalikan hanya tabelnya
            return view('admin.ulasan.table', compact('surveys'))->render();
        }

        return view('admin.ulasan.index', compact('surveys'));
    }

    public function show($id)
    {
        // Cari data berdasarkan ID, jika tidak ketemu tampilkan 404
        $survey = UserSurvey::findOrFail($id);

        return view('admin.ulasan.detail', compact('survey'));
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $fileName = 'laporan-survey-' . date('d-m-Y') . '.xlsx';

        return Excel::download(new UserSurveyExport($search), $fileName);
    }

    public function reset()
    {
        try {
            // truncate() menghapus semua baris dan mereset ID auto-increment ke 0
            UserSurvey::truncate();

            return redirect()->route('data-survey')
                ->with('success', 'Seluruh data survey berhasil di-reset (dihapus).');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }
}
