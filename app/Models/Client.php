<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Upewnij się, że ten trait jest zaimportowany
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory; // Musisz użyć tego traitu, aby działały fabryki

    protected $fillable = ['first_name', 'last_name'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}

