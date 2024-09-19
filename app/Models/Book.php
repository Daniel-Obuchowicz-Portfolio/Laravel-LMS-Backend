<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'author', 'year_published', 'publisher', 'is_borrowed', 'client_id'];

    protected $casts = [
        'is_borrowed' => 'boolean',  // Rzutowanie na boolean
    ];
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
