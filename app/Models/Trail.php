<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'entity_id',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_trail')
            ->withPivot('position')
            ->orderBy('course_trail.position');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'trail_user')->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_trail')->withTimestamps();
    }
}
