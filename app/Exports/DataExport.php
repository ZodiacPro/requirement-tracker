<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\StepModel;
use DB;

class DataExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $id;

    public function __construct(int $id) 
    {
        $this->id = $id;
    }

    public function collection()
    {
        return DB::table('task_item')->selectRaw("step.name as step_name, task.name as task_name, task_item.name as item_name, 
        CASE
        WHEN status = 0 THEN 'Pending'
        WHEN status = 1 THEN 'Approved'
        ELSE 'Rejected'
        END,
        remarks")
            ->join('task','task.id','task_item.task_id')
            ->join('step','step.id','task.step_id')
            ->where('step.site_id', $this->id)
            ->get();
    }
}
