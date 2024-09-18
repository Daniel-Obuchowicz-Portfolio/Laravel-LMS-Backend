<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'published_year', 'publisher', 'is_borrowed', 'borrowed_by'];

    public function borrowedBy()
    {
        return $this->belongsTo(Client::class, 'borrowed_by');
    }
}