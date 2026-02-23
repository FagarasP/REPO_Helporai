<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'project_language',
        'job_type',
        'payment_offer',
        'payment_amount',
        'status',
        'start_date',
        'end_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function applications()
    {
        return $this->hasMany(ProjectApplication::class);
    }
}
