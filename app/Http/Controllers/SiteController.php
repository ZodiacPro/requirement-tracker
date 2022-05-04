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
use App\Exports\DataExport;
use App\Exports\ApprovedExport;
use App\Exports\RejectExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use DB;

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
        return view("site.detail", compact('site','area','step','task','task_item','task_count','task_active','step_count','id'));
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
    public function auto_add($id){
        
        $step = StepModel::create([
            'name' => 'PREPARE FOR INSTALLATION',
            'site_id' => $id,
        ]);
                $task1 = TaskModel::create([
                    'name' => 'SITE INFORMATION',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Overall Video of site before installation and show bayface full view of battery and rectifier system',
                            'task_id' => $task1->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of rectifier display to show current load before installation',
                            'task_id' => $task1->id,
                            'remarks' => '',
                    ]);
                $task2 = TaskModel::create([
                    'name' => 'BEFORE INSTALLATION: NOC SCREENSHOT',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Screenshot of VSWR from U2000 NOC Device Panel',
                            'task_id' => $task2->id,
                            'remarks' => '',
                        ]);
        // ---------------------------------------------------------
        $step = StepModel::create([
            'name' => 'ON GROUND EQUIPMENT INSTALLATION',
            'site_id' => $id,
        ]);
                $task1 = TaskModel::create([
                    'name' => 'CABINET/RACK/POLE',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Video showing bolts and nuts completeness and are properly tighthened.',
                            'task_id' => $task1->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Video of Stress Test - Cabinet/Rack/Pole is stable.',
                            'task_id' => $task1->id,
                            'remarks' => '',
                         ]);
                $task2 = TaskModel::create([
                    'name' => 'EQUIPMENT',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'The required additional BBU boards are installed properly on the correct slots with cables neatly arranged, unused ports are covered and no red LED indicator is lit "ON" on WRFU/RRU equipment',
                            'task_id' => $task2->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of equipment grounding with correct terminal lugs, properly crimped and with shrinkable tube.',
                            'task_id' => $task2->id,
                            'remarks' => '',
                         ]);
                         TaskItemModel::create([
                            'name' => 'Video showing BBU Fan are functional and air-flow is not disrupted.',
                            'task_id' => $task2->id,
                            'remarks' => '',
                         ]);
                $task3 = TaskModel::create([
                    'name' => 'POWER',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of All DC connections must be properly tightened with correct terminal lugs/cold end terminal and properly crimped, with heat-shrinkable tube.',
                            'task_id' => $task3->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'DC input voltage measurement on the BBU must be within the range -38.4Vdc to -57.6Vdc',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                        TaskItemModel::create([
                            'name' => 'Photo of LLVD Circuit breaker with readable rating.',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Anti-static wrist strap',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                $task4 = TaskModel::create([
                    'name' => 'COMBINER',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of installed combiner',
                            'task_id' => $task4->id,
                            'remarks' => '',
                        ]);
                $task5 = TaskModel::create([
                    'name' => 'GPS',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of installed GPS',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of GPS surge protection',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of GPS grounding route and termination.',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                $task6 = TaskModel::create([
                    'name' => 'WATERPROOFING / WEATHERPROOFING',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Side view photo of cable entry showing cable drip loop before the porthole/cable inlet',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Front view photo of cabin porthole or outdoor cabinet cable inlet (taken outside)',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Rear view photo of cabin porthole or outdoor cabinet cable inlet (taken inside)',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                $task7 = TaskModel::create([
                    'name' => 'LABELING',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of DC, grounding, and CPRI cables labeling per sector',
                            'task_id' => $task7->id,
                            'remarks' => '',
                        ]);
                $task8 = TaskModel::create([
                    'name' => 'ASSET TAGGING/ SERIAL NUMBER',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Equipment/Board/Module asset tags and serial number',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Cabinet 1 (APM) Asset Tag Number - Scan Bar Code_Photo',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Cabinet 1 (DC Power Cabinet) Asset Tag Number - Scan Bar Code_Photo',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                $task9 = TaskModel::create([
                    'name' => 'LMT CABLE',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of LMT cable (USB to FE Converter) in sealable plastic, properly placed inside the equipment cabinet',
                            'task_id' => $task9->id,
                            'remarks' => '',
                        ]);
                $task10 = TaskModel::create([
                    'name' => 'NEW INSTALLED L-RACK',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Overall video of newly installed L-RACK',
                            'task_id' => $task10->id,
                            'remarks' => '',
                        ]);

        // -----------------------------------------------------------------------------------------------------------------------------------------------
        $step = StepModel::create([
            'name' => 'ON TOWER EQUIPMENT INSTALLATION',
            'site_id' => $id,
        ]);
                $task1 = TaskModel::create([
                    'name' => 'ANTENNA SHOT TAKEN FROM GROUND',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Front view of Sector 0',
                            'task_id' => $task1->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Front view of Sector 1',
                            'task_id' => $task1->id,
                            'remarks' => '',
                         ]);
                         TaskItemModel::create([
                            'name' => 'Front view of Sector 2',
                            'task_id' => $task1->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Front view of Sector 3',
                            'task_id' => $task1->id,
                            'remarks' => '',
                         ]);
                $task2 = TaskModel::create([
                    'name' => 'ODM/ OCB INSTALLATION',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Sector 0 - ODM or OCB showing approved location, waterproofing and connection details',
                            'task_id' => $task2->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Sector 1 - ODM or OCB showing approved location, waterproofing and connection details',
                            'task_id' => $task2->id,
                            'remarks' => '',
                         ]);
                         TaskItemModel::create([
                            'name' => 'Sector 2 - ODM or OCB showing approved location, waterproofing and connection details',
                            'task_id' => $task2->id,
                            'remarks' => '',
                         ]);
                         TaskItemModel::create([
                            'name' => 'Sector 3 - ODM or OCB showing approved location, waterproofing and connection details',
                            'task_id' => $task2->id,
                            'remarks' => '',
                         ]);
                $task3 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA Sector 0_RF',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Video showing Antenna 1 Coverage is clear from any obstruction(s).',
                            'task_id' => $task3->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 coverage(shot behind the antenna)',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 (Compass Shot): 1 photo required, compass above antenna',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Mechanical Tilt (with inclinometer close up shot)',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Electrical tilt (readable)',
                            'task_id' => $task3->id,
                            'remarks' => '',
                            ]);
                $task4 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA Sector 0',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo showing ANT_TX/RX ports of RRU must be connected to correct ports of the antenna 2 and show readble port labels',
                            'task_id' => $task4->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Overall video of Antenna and RRU installation',
                            'task_id' => $task4->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Full back view of Antenna 1',
                            'task_id' => $task4->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 showing unused ports and Smart Bias TEE are properly waterproofed.',
                            'task_id' => $task4->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 asset tag and serial number',
                            'task_id' => $task4->id,
                            'remarks' => '',
                        ]); 
                $task5 = TaskModel::create([
                    'name' => 'RRU INSTALLATION of Sector 0',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Installed RRU 1 (Open & closed cover)',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 showing unused ports are properly waterproofed.',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 grounding termination',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU/MRFU/WRFU 1 asset tag and serial number',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Outdoor cable labels on Antenna and RRU side',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of jumper cable installed',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'CPRI cable management on RRU side (Show cable looping)',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Video of RRU stress test',
                            'task_id' => $task5->id,
                            'remarks' => '',
                        ]);
                $task6 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 1_RF',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Video showing Antenna 1 Coverage is clear from any obstruction(s).',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 coverage(shot behind the antenna)',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 (Compass Shot): 1 photo required, compass above antenna',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Mechanical Tilt (with inclinometer close up shot)',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Electrical tilt (readable)',
                            'task_id' => $task6->id,
                            'remarks' => '',
                        ]);
                $task7 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 1',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo showing ANT_TX/RX ports of RRU must be connected to correct ports of the antenna 2 and show readble port labels',
                            'task_id' => $task7->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Overall video of Antenna and RRU installation',
                            'task_id' => $task7->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Full back view of Antenna 1',
                            'task_id' => $task7->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 showing unused ports and Smart Bias TEE are properly waterproofed',
                            'task_id' => $task7->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 asset tag and serial number',
                            'task_id' => $task7->id,
                            'remarks' => '',
                        ]);
                $task8 = TaskModel::create([
                    'name' => 'RRU INSTALLATION of Sector 1',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Installed RRU 1 (Open & closed cover)',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 showing unused ports are properly waterproofed.',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 grounding termination',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU/MRFU/WRFU 1 asset tag and serial number',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Outdoor cable labels on Antenna and RRU side',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of jumper cable installed',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'CPRI cable management on RRU side (Show cable looping)',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Video of RRU stress test',
                            'task_id' => $task8->id,
                            'remarks' => '',
                        ]);
                $task9 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 2_R',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Video showing Antenna 1 Coverage is clear from any obstruction(s).',
                            'task_id' => $task9->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 coverage(shot behind the antenna)',
                            'task_id' => $task9->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 (Compass Shot): 1 photo required, compass above antenna',
                            'task_id' => $task9->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Mechanical Tilt (with inclinometer close up shot)',
                            'task_id' => $task9->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Electrical tilt (readable)',
                            'task_id' => $task9->id,
                            'remarks' => '',
                        ]);
                $task10 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 2',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo showing ANT_TX/RX ports of RRU must be connected to correct ports of the antenna 2 and show readble port labels',
                            'task_id' => $task10->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Overall video of Antenna and RRU installation',
                            'task_id' => $task10->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Full back view of Antenna 1',
                            'task_id' => $task10->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 showing unused ports are and Smart Bias TEE properly waterproofed',
                            'task_id' => $task10->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 asset tag and serial number',
                            'task_id' => $task10->id,
                            'remarks' => '',
                        ]);
                $task11 = TaskModel::create([
                    'name' => 'RRU INSTALLATION of Sector 2',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Installed RRU 1 (Open & closed cover)',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 showing unused ports are properly waterproofed.',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 grounding termination',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU/MRFU/WRFU 1 asset tag and serial number',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Outdoor cable labels on Antenna and RRU side',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of jumper cable installed',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'CPRI cable management on RRU side (Show cable looping)',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Video of RRU stress test',
                            'task_id' => $task11->id,
                            'remarks' => '',
                        ]);
                $task12 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 3_RF',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Video showing Antenna 1 Coverage is clear from any obstruction(s).',
                            'task_id' => $task12->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 coverage(shot behind the antenna)',
                            'task_id' => $task12->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF antenna 1 (Compass Shot): 1 photo required, compass above antenna',
                            'task_id' => $task12->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Mechanical Tilt (with inclinometer close up shot)',
                            'task_id' => $task12->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RF Antenna 1 Electrical tilt (readable)',
                            'task_id' => $task12->id,
                            'remarks' => '',
                        ]);
                $task13 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 3',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo showing ANT_TX/RX ports of RRU must be connected to correct ports of the antenna 2 and show readble port labels',
                            'task_id' => $task13->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Overall video of Antenna and RRU installation',
                            'task_id' => $task13->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Full back view of Antenna 1',
                            'task_id' => $task13->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 showing unused ports and Smart Bias TEE are properly waterproofed',
                            'task_id' => $task13->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Antenna 1 asset tag and serial number',
                            'task_id' => $task13->id,
                            'remarks' => '',
                        ]);
                $task14 = TaskModel::create([
                    'name' => 'POST-RF AUDIT ANTENNA of Sector 3',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Installed RRU 1 (Open & closed cover)',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 showing unused ports are properly waterproofed.',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU 1 grounding termination',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of RRU/MRFU/WRFU 1 asset tag and serial number',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of Outdoor cable labels on Antenna and RRU side',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Photo of jumper cable installed',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'CPRI cable management on RRU side (Show cable looping)',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                        TaskItemModel::create([
                            'name' => 'Video of RRU stress test',
                            'task_id' => $task14->id,
                            'remarks' => '',
                        ]);
                $task15 = TaskModel::create([
                    'name' => 'GROUNDING',
                    'step_id' => $step->id,
                ]);
                        TaskItemModel::create([
                            'name' => 'Photo or video of RRU grounding busbar termination on tower (show continuity from RRU-busbar-to test pit termination)',
                            'task_id' => $task15->id,
                            'remarks' => '',
                        ]);
// -----------------------------------------------------------------------------------------------------------------------------------------------
$step = StepModel::create([
'name' => 'MINI-CME',
'site_id' => $id,
]);
    $task = TaskModel::create([
        'name' => 'AMB',
        'step_id' => $step->id,
    ]);
            TaskItemModel::create([
                'name' => 'Photo of AMB (per sector) showing completeness of bolts, nuts & washer',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
    $task = TaskModel::create([
        'name' => 'RRU MOUNTING POLE',
        'step_id' => $step->id,
    ]);
            TaskItemModel::create([
                'name' => 'Photo of RRU Mounting pole (per sector)',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
            TaskItemModel::create([
                'name' => 'Photo of RRU Pole Pedestal (per sector). Applicable only if RRU is floor mounted.',
                'task_id' => $task->id,
                'remarks' => '',
                ]);
    $task = TaskModel::create([
        'name' => 'FTP',
        'step_id' => $step->id,
    ]);
            TaskItemModel::create([
                'name' => 'Photo of New FTP (Front and Rear View). Mark this item N/A if no new FTP installed.',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
    $task = TaskModel::create([
        'name' => 'GROUNDING BUSBAR',
        'step_id' => $step->id,
    ]);
            TaskItemModel::create([
                'name' => 'Photo of New Busbar (On-tower)',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
            TaskItemModel::create([
                'name' => 'Photo of New Busbar (On-ground)',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
            TaskItemModel::create([
                'name' => 'Video of Busbar grounding continuity',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
    $task = TaskModel::create([
        'name' => 'CABLE LADDER',
        'step_id' => $step->id,
    ]);
            TaskItemModel::create([
                'name' => 'Photo of new cable ladder / cable clip extension installed showing bolts,nuts,washing, and cable clip spacing.',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
            TaskItemModel::create([
                'name' => 'Video of Cable Ladder / cable stacking solution detailed connections showing bolts,nuts,washing, and cable clip spacing.',
                'task_id' => $task->id,
                'remarks' => '',
            ]);
// ------------------------------------------------------------------------------------------------------------------------------------------------
$step = StepModel::create([
    'name' => 'DC UPGRADE (MARK N/A IF NOT REQUIRED BASED ON TSSR)',
    'site_id' => $id,
]);
        $task = TaskModel::create([
            'name' => 'NEW DC POWER MAINS CONFIGURATION',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => 'Use Standard AC CB based on the Approved TSSR',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Provide Cable shoe/ Terminal Lugs and Shrinkable tube on the tip of the AC cable before termination',
                    'task_id' => $task->id,
                    'remarks' => '',
                    ]);


        $task = TaskModel::create([
            'name' => 'NEW INSTALLED RECTIFIER SYSTEM',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => 'Measure the Voltage Output of the Rectifier',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Measure the Voltage of the AC input',
                    'task_id' => $task->id,
                    'remarks' => '',
                    ]);
                TaskItemModel::create([
                    'name' => 'Measure the Current of the DC Loads',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Measure the Current of the AC input',
                    'task_id' => $task->id,
                    'remarks' => '',
                    ]);
                TaskItemModel::create([
                    'name' => 'PMU Configuration Check, RS Monitoring screen displaying "NO ALARM" / LVBD,LVLD,SYMMETRY ALARM, MODEM and TEMPERATURE COMPENSATION, SYMMETRY ALARM(door), MODEM and TEMPERATURE COMPENSATION',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);


        $task = TaskModel::create([
            'name' => 'NEW INSTALLED MODULES',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => 'OVERVIEW OF INSTALLED RECTIFIER MODULES',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'PHOTO OF PMU SHOWS NO ALARMS',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photo of Installed Rectifier Module Serial Number',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);


        $task = TaskModel::create([
            'name' => 'NEW BATTERY',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => 'OVERVIEW OF INSTALLED BATTERY SYSTEM',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Float Voltage measurement BEFORE DISCHARGE Test',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Float Voltage measurement DISCHARGE TEST Data (10mins)',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photo of Battery connection and screws Bank 1',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photo of Battery connection and screws Bank 2',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photo of Battery connection and screws Bank 3',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photo of Battery connection and screws Bank 4',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);


        $task = TaskModel::create([
            'name' => 'NEW BATTERY RACK CABINET',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => 'OVERVIEW OF NEW BATTERY RACK/CABINET',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Video showing proper location and stress test by pushing the cabinet/rack',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'PHOTO OF ASSET TAG',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);


        $task = TaskModel::create([
            'name' => 'CABLE INSTALLATION',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => 'Video Showing Power and Grounding cable route',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photos showing cabinets, batteries, and power/ground cables',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
// ------------------------------------------------------------------------------------------------------------------------------------------------
$step = StepModel::create([
    'name' => 'SITE CLEANLINESS',
    'site_id' => $id,
]);
        $task = TaskModel::create([
            'name' => 'EXCESS MATERIALS PULLOUT',
            'step_id' => $step->id,
        ]);
                TaskItemModel::create([
                    'name' => '360 Degree video of site showing site cleanliness (inside and outside cabin)',
                    'task_id' => $task->id,
                    'remarks' => '',
                ]);
                TaskItemModel::create([
                    'name' => 'Photo of Dismantled Materials information (S/N & Asset Tag No.)',
                    'task_id' => $task->id,
                    'remarks' => '',
                    ]);
                TaskItemModel::create([
                    'name' => 'Photo of Dismantled Materials properly staged/stacked on site',
                    'task_id' => $task->id,
                    'remarks' => '',
                    ]);
        return back();
    }
    public function export($id) 
    {
        return Excel::download(new DataExport($id), 'all.xlsx');
    }
    public function approved($id) 
    {
        return Excel::download(new ApprovedExport($id), 'approved.xlsx');
    }
    public function rejected($id) 
    {
        return Excel::download(new RejectExport($id), 'rejected.xlsx');
    }
    public function textall($id)
    {
        $data = DB::table('task_item')->selectRaw("step.name as step_name, task.name as task_name, task_item.name as item_name, 
        CASE
        WHEN status = 0 THEN 'Pending'
        WHEN status = 1 THEN 'Approved'
        ELSE 'Rejected'
        END as stats,
        remarks")
            ->join('task','task.id','task_item.task_id')
            ->join('step','step.id','task.step_id')
            ->where('step.site_id', $id)
            ->get();
        $text = "";
        $stepname = "";
        $task = "";
        foreach($data as $datas){
            if($stepname != $datas->step_name){
                $text = $text ."\n". $datas->step_name . "\n";
            }
            if($task != $datas->task_name){
                $text = $text . "    ". $datas->task_name . "\n";
            }
            $text = $text .  "         ". $datas->item_name . "\n";
            $text = $text .  "             Status: ". $datas->stats . "\n";
            $text = $text .  "             Remarks: ". $datas->remarks . "\n";

            $stepname = $datas->step_name;
            $task = $datas->task_name;
        }

        Storage::disk('public')->put('file.txt', $text);

        return Storage::disk('public')->download('file.txt');
    }
    public function textapprove($id)
    {
        $data = DB::table('task_item')->selectRaw("step.name as step_name, task.name as task_name, task_item.name as item_name, 
        CASE
        WHEN status = 0 THEN 'Pending'
        WHEN status = 1 THEN 'Approved'
        ELSE 'Rejected'
        END as stats,
        remarks")
            ->join('task','task.id','task_item.task_id')
            ->join('step','step.id','task.step_id')
            ->where('step.site_id', $id)
            ->where('status', 1)
            ->get();
        $text = "";
        $stepname = "";
        $task = "";
        foreach($data as $datas){
            if($stepname != $datas->step_name){
                $text = $text ."\n". $datas->step_name . "\n";
            }
            if($task != $datas->task_name){
                $text = $text . "    ". $datas->task_name . "\n";
            }
            $text = $text .  "         ". $datas->item_name . "\n";
            $text = $text .  "             Status: ". $datas->stats . "\n";
            $text = $text .  "             Remarks: ". $datas->remarks . "\n";

            $stepname = $datas->step_name;
            $task = $datas->task_name;
        }

        Storage::disk('public')->put('approved.txt', $text);
        return Storage::disk('public')->download('approved.txt');
    }

    public function textreject($id)
    {
        $data = DB::table('task_item')->selectRaw("step.name as step_name, task.name as task_name, task_item.name as item_name, 
        CASE
        WHEN status = 0 THEN 'Pending'
        WHEN status = 1 THEN 'Approved'
        ELSE 'Rejected'
        END as stats,
        remarks")
            ->join('task','task.id','task_item.task_id')
            ->join('step','step.id','task.step_id')
            ->where('step.site_id', $id)
            ->where('status', 2)
            ->get();
        $text = "";
        $stepname = "";
        $task = "";
        foreach($data as $datas){
            if($stepname != $datas->step_name){
                $text = $text ."\n". $datas->step_name . "\n";
            }
            if($task != $datas->task_name){
                $text = $text . "    ". $datas->task_name . "\n";
            }
            $text = $text .  "         ". $datas->item_name . "\n";
            $text = $text .  "             Status: ". $datas->stats . "\n";
            $text = $text .  "             Remarks: ". $datas->remarks . "\n";

            $stepname = $datas->step_name;
            $task = $datas->task_name;
        }

        Storage::disk('public')->put('rejected.txt', $text);
        return Storage::disk('public')->download('rejected.txt');
    }
}
