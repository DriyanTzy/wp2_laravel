<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'link', 'image_path', 'is_active', 'target_responses',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Tambah image_url otomatis ke response JSON
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

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