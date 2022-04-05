<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $table = 'sites';

    protected $fillable = [
        'sitecode',
        'duid',
        'area_id',
    ];
}
