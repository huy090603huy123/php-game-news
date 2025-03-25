<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $guarded = ['question', 'status', 'start_date', 'end_date'];

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
