<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Datatables;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model, Request $request)
    {
        if($request->ajax()) {
            return datatables()->of(User::get())
            ->addColumn('action', function ($row) {
                $html = "<a href='#' onclick='reset(".$row->id.")'class='btn btn-danger btn-sm'>Reset</a>";
                $html = $html  . "<a href='#' onclick='admin(".$row->id.")'class='btn btn-success btn-sm'>Admin</a>";
                return $html;
            })
            ->addColumn('typename', function ($row) {
                $html = 'User';
                if($row->type === 1) $html = 'HR';
                if($row->type === 2) $html = 'PM';
                if($row->type === 99) $html = 'Admin';
                return $html;
            })
            ->addColumn('teamname', function ($row) {
                if($row->team_id != null){
                    $teamname = teamModel::where('id',$row->team_id)->first();
                    $name = $teamname->name;
                }else{
                    $name = "No Team";
                }   
                return $name;
            })
            ->rawColumns(['action','typename','teamname'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('users.index');
    }
}
