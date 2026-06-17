<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'requires_quiz',
        'entity_id',
        'is_approved_for_sharing',
    ];

    protected $casts = [
        'requires_quiz' => 'boolean',
        'is_approved_for_sharing' => 'boolean',
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('completed_at')->withTimestamps();
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function trails()
    {
        return $this->belongsToMany(Trail::class, 'course_trail')->withPivot('position');
    }

    public function sharedEntities()
    {
        return $this->belongsToMany(Entity::class, 'course_entity');
    }
}
