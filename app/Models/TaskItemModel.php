<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskItemModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $table = 'task_item';

    protected $fillable = [
        'name',
        'remarks',
        'task_id',
        'status',
    ];
}
