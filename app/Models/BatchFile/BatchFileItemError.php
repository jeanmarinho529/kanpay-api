<?php

namespace App\Models\BatchFile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchFileItemError extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_file_id',
        'row_number',
        'error',
    ];
}
