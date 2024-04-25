<?php

namespace App\Models\BatchFile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_file_status_id',
        'batch_file_type_id',
        'name',
        'path',
        'total_items',
        'total_done',
        'total_failed',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function batchFileStatus(): BelongsTo
    {
        return $this->belongsTo(BatchFileStatus::class);
    }

    public function batchFileType(): BelongsTo
    {
        return $this->belongsTo(BatchFileType::class);
    }

    public function batchFileItemError(): HasMany
    {
        return $this->hasMany(BatchFileItemError::class);
    }
}
