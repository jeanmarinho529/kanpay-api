<?php

namespace App\Models\BatchFile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchFileType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'display_name',
        'name',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
}
