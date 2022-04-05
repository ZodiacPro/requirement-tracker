<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $table = 'task';

    protected $fillable = [
        'name',
        'step_id',
    ];
}
