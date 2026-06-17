<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_instructor',
        'is_admin',
        'is_entity_manager',
        'entity_id',
        'invitation_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_instructor' => 'boolean',
        'is_admin' => 'boolean',
        'is_entity_manager' => 'boolean',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('completed_at')->withTimestamps();
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->withPivot('completed_at')->withTimestamps();
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isEnrolled($courseId)
    {
        return $this->courses()->where('course_id', $courseId)->exists();
    }

    public function hasCompletedLesson($lessonId)
    {
        return $this->lessons()->where('lesson_id', $lessonId)->exists();
    }

    public function courseProgress($course)
    {
        $totalLessons = $course->lessons()->count();
        if ($totalLessons === 0) {
            return 100;
        }

        $completedLessons = $this->lessons()
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();

        return round(($completedLessons / $totalLessons) * 100);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

    public function trails()
    {
        return $this->belongsToMany(Trail::class, 'trail_user')->withTimestamps();
    }

    public function trailProgress($trail)
    {
        $totalCourses = $trail->courses()->count();
        if ($totalCourses === 0) {
            return 100;
        }

        $completedCourses = $this->courses()
            ->whereIn('course_id', $trail->courses()->pluck('courses.id'))
            ->whereNotNull('course_user.completed_at')
            ->count();

        return round(($completedCourses / $totalCourses) * 100);
    }
}
