<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Datatables;
use App\Models\SiteModel;
use App\Models\AreaModel;
use App\Models\StepModel;
use App\Models\TaskModel;
use App\Models\TaskItemModel;

class SiteController extends Controller
{
    public function list(Request $request){
        if($request->ajax()) {
            return datatables()->of(SiteModel::get())
            ->addColumn('action', function ($row) {
                $html = "<a href='".route('site.detail',$row->id)."' class='btn btn-secondary btn-sm'>View</a>";
                $html = $html  . "<a href='#' onclick='admin(".$row->id.")'class='btn btn-danger btn-sm'>Delete</a>";
                return $html;
            })
            ->addColumn('area_name', function ($row) {
                $area = AreaModel::where('id',$row->area_id)->first();
                return $area->name;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        $area = AreaModel::get();
        return view('site.list', compact('area'));
    }

    
    public function create(Request $request){
        SiteModel::create([
            'sitecode' => $request->sitecode,
            'duid' => $request->duid,
            'area_id' => $request->area,
        ]);
        return back()->with('status', 'Created!');
    }

    public function detail($id){
        $site = SiteModel::where('id', $id)->first();
        $area = AreaModel::get();
        $step = StepModel::where('site_id', $id)->get();
        $task = TaskModel::selectRaw('task.name as task_name, step_id, site_id, task.id as taskid')
                        ->join('step', 'step.id', '=', 'task.step_id')
                        ->where('site_id', $id)
                        ->get();
        $task_item = TaskItemModel::selectRaw('remarks, task_item.name as taskitem, task_id, status, task_item.id as id')
                        ->join('task', 'task.id', '=', 'task_item.task_id')
                        ->join('step', 'step.id', '=', 'task.step_id')
                        ->where('site_id', $id)
                        ->get();

        // counter

        $task_count = TaskItemModel::selectRaw('task_id, count(*) as count')->join('task','task.id','=','task_item.task_id')->groupBy('task_id')->get();
        $task_active = TaskItemModel::selectRaw('task_id, count(*) as count')->join('task','task.id','=','task_item.task_id')->groupBy('task_id')->where('status', '1')->get();
        $step_count = StepModel::selectRaw('step_id, count(*) as count')->join('task','task.step_id','=','step.id')->groupBy('step_id')->get();
        return view("site.detail", compact('site','area','step','task','task_item','task_count','task_active','step_count'));
    }

    public function update($id, Request $request){
        $formdata = ([
            'sitecode' =>$request->sitecode,
            'duid'     =>$request->duid,
            'area_id'  =>$request->area,
        ]);
        SiteModel::where('id', $id)
                 ->update($formdata);
        return back()->with('status', 'Updated!');
    }

    public function add_step($id, Request $request){
        StepModel::create([
            'name' => $request->step,
            'site_id' => $id,
        ]);
        return back();
    }

    public function add_task(Request $request){
        TaskModel::create([
            'name' => $request->task,
            'step_id' => $request->step_id,
        ]);
        return back();
    }

    public function add_item(Request $request){
        TaskItemModel::create([
            'name' => $request->task_item,
            'task_id' => $request->task_id,
            'remarks' =>$request->remarks,
        ]);
        return back();
    }
    public function remarks($id, Request $request){
        TaskItemModel::where('id', $id)
        ->update(['remarks' => $request->remarks]);
    }
    public function aprrove($id){
        TaskItemModel::where('id', $id)
        ->update(['status' => 1]);

        return back();
    }
    public function reject($id){
        TaskItemModel::where('id', $id)
        ->update(['status' => 2]);

        return back();
    }
    // delete
    public function delete_step(Request $request){
        try { 
            StepModel::where('id', $request->step_id_delete)->delete();
            return back()->with('status', 'Deleted!');

        } catch(\Illuminate\Database\QueryException $ex){ 
            return back()->with('danger', 'Failed! Delete Task/s first');
        }
    }

    public function delete_task(Request $request){
        
        try { 
            $results = TaskModel::where('id', $request->task_id_delete)->delete();
            return back()->with('status', 'Deleted!');

        } catch(\Illuminate\Database\QueryException $ex){ 
            return back()->with('danger', 'Failed! Delete Task Item/s first');
        }
    }

    public function delete_item(Request $request){
        TaskItemModel::where('id', $request->task_id_del)->delete();
        return back()->with('status', 'Deleted!');
    }
}
