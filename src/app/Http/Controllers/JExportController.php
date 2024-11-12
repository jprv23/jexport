<?php

namespace Jeanp\JExport\app\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Export;
use Illuminate\Support\Facades\Session;

class JExportController extends Controller
{
    public function index()
    {
        if(!request()->ajax()){
            return view('jexport.index');
        }

        $exports = Export::active()->orderByDesc('id')->paginate(25);

        return response()->json([
            'table' => view('jexport.table', compact('exports'))->render(),
            'loading' => $exports->where('progress', '<', 100)->count() > 0,
        ]);
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
