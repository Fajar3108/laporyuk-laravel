<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'visibility', 'status', 'date', 'province', 'city'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function files() {
        return $this->hasMany(ComplaintFile::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function supports()
    {
        return $this->hasMany(ComplaintSupport::class);
    }
}
