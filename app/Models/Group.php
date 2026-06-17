<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'entity_id',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function trails()
    {
        return $this->belongsToMany(Trail::class, 'group_trail')->withTimestamps();
    }
}
