<?php

namespace Jeanp\JExport\app\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Export;
use Illuminate\Support\Facades\Session;

class JExportController extends Controller
{
    public function index()
    {
        $exports = Export::active()->orderByDesc('id')->paginate(25);

        return view('jexport.index', compact('exports'));
    }

    public function destroy($id)
    {

        $export = Export::findOrFail($id);
        $export->status = false;
        $export->save();

        Session::flash('success', 'Eliminado correctamente.');

        return back();
    }

    public function flush()
    {
        Export::where('status', true)->update(['status' => false]);

        Session::flash('success', 'Eliminado correctamente.');

        return back();
    }
}
