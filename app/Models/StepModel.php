<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepModel extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $table = 'step';

    protected $fillable = [
        'name',
        'site_id',
    ];
}
