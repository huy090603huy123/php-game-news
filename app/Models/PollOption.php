<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $guarded = ['poll_id', 'option_text', 'votes']; 

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
