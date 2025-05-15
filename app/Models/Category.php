<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
     use HasFactory;

    protected $fillable = ['user_id', 'title'];

    // Relasi ke user (pemilik kategori)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke todos (kategori punya banyak todo)
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }
}