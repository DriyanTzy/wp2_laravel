<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'link', 'is_active', 'target_responses',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function respondents()
    {
        return $this->belongsToMany(User::class, 'responses')->withTimestamps();
    }
}