<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditLogController extends Controller
{
    /**
     * Menampilkan daftar riwayat audit.
     */
    public function index(Request $request)
    {
        // Filter opsional berdasarkan model, event, atau pengguna
        $query = Audit::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('model')) {
            $query->where('auditable_type', 'like', '%' . $request->model . '%');
        }
        
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $audits = $query->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('audits'));
    }
}
