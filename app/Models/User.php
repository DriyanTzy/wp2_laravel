<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'photo',
        'bio',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];



    // Relasi
    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function datasets()
    {
        return $this->hasMany(Dataset::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function accessedDatasets()
    {
        return $this->belongsToMany(Dataset::class, 'dataset_access')->withTimestamps();
    }

    public function filledSurveys()
    {
        return $this->belongsToMany(Survey::class, 'responses')->withTimestamps();
    }
}