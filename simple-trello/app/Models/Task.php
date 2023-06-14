<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    public const STATUSES = [
        'PENDING' => 'PENDING',
        'IN_PROGRESS' => 'IN_PROGRESS',
        'CONCLUDED' => 'CONCLUDED',
    ];

    public const PRIORITIES = [
        'LOW' => 'LOW',
        'MEDIUM' => 'MEDIUM',
        'HIGH' => 'HIGH',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'deadline',
        'created_by',
        'assigned_to',
        'deadline_notified_at',
        'assignment_notified_at',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'deadline'];

    /**
     * User that created this task
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User that is assigned to this task
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
