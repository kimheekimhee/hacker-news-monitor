<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HackerNews extends Model
{
    protected $fillable = ['title', 'url'];
}
