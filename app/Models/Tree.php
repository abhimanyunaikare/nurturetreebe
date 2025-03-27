<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'species', 'lat', 'long', 'last_watered', 'health_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

