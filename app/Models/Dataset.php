<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $fillable = [
        'user_id', 'title', 'class', 'thumbnail',
        'description', 'file_path', 'points_required', 'present_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accessedBy()
    {
        return $this->belongsToMany(User::class, 'dataset_access')->withTimestamps();
    }
}