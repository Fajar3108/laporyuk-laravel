<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintFile extends Model
{
    use HasFactory;

    protected $fillable = ['path'];

    public $timestamps = false;
}
