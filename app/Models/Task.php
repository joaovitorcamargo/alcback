<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey  = 'id';

    protected $fillable = [
        'name',
        'done_date',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
