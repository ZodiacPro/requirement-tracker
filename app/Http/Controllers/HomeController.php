<?php

namespace App\Http\Controllers;

use App\Models\TaskItemModel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $total_pending = 0;
        $total_approved = 0;
        $total_rejected = 0;
        $task_pending = TaskItemModel::selectRaw('sites.sitecode as name, count(*) as count')
            ->join('task','task.id','=','task_item.task_id')
            ->join('step','step.id','=','task.step_id')
            ->join('sites','sites.id','=','step.site_id')
            ->where('status', '0')
            ->groupBy('sites.sitecode')
            ->get();
        $task_active = TaskItemModel::selectRaw('sites.sitecode as name, count(*) as count')
            ->join('task','task.id','=','task_item.task_id')
            ->join('step','step.id','=','task.step_id')
            ->join('sites','sites.id','=','step.site_id')
            ->where('status', '1')
            ->groupBy('sites.sitecode')
            ->get();
        $task_reject = TaskItemModel::selectRaw('sites.sitecode as name, count(*) as count')
            ->join('task','task.id','=','task_item.task_id')
            ->join('step','step.id','=','task.step_id')
            ->join('sites','sites.id','=','step.site_id')
            ->where('status', '2')
            ->groupBy('sites.sitecode')
            ->get();

        $chart =[
            'site1' => [],
            'pending' => [],
            'site2' => [],
            'active' => [],
            'site3' => [],
            'reject' => [],
        ];
        // pending array
        foreach($task_pending as $task){
            $total_pending += $task->count;
            array_push($chart['site1'], $task->name);
            array_push($chart['pending'], $task->count);
        }
        foreach($task_active as $task){
            $total_approved += $task->count;
            array_push($chart['site2'], $task->name);
            array_push($chart['active'], $task->count);
        }
        foreach($task_reject as $task){
            $total_rejected += $task->count;
            array_push($chart['site3'], $task->name);
            array_push($chart['reject'], $task->count);
        }
        return view('dashboard', compact('chart','total_pending','total_approved','total_rejected'));
    }
}
