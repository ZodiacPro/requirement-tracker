<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaModel;

class AreaController extends Controller
{
    public function list(Request $request){
        if($request->ajax()) {
            return datatables()->of(AreaModel::get())
            ->addColumn('action', function ($row) {
                $html = "<a href='#' onclick='reset(".$row->id.")'class='btn btn-secondary btn-sm'>Edit</a>";
                return $html;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view("area.list");
    }
    public function create(Request $request){
        AreaModel::create([
            'name' => $request->name,
        ]);
        return back()->with('status', 'Created!');
    }
}
