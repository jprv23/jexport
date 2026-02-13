<?php

namespace Jeanp\JExport\app\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Export;
use Illuminate\Support\Facades\Session;

class JExportController extends Controller
{
    public function index()
    {
        if (!request()->ajax()) {
            return view('jexport.index');
        }

        $exports = Export::on(config('jexport.connection'))
            ->where('status', true)
            ->where('user_id', auth()->user()->id)
            ->orderByDesc('id')
            ->paginate(25);

        return response()->json([
            'table' => view('jexport.table', compact('exports'))->render(),
            'loading' => $exports->where('progress', '<', 100)->count() > 0,
        ]);
    }

    public function destroy($id)
    {

        $export = Export::on(config('jexport.connection'))->where('id', $id)->first();
        $export->status = false;
        $export->save();

        Session::flash('success', 'Eliminado correctamente.');

        return back();
    }

    public function flush()
    {
        Export::on(config('jexport.connection'))
            ->where('status', true)
            ->where('user_id', auth('web')->user()->id)
            ->update(['status' => false]);

        Session::flash('success', 'Eliminado correctamente.');

        return back();
    }
}
