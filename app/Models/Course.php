<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['course_no', 'course_title'];

    // এই course-এর teacher-রা (many-to-many)
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'user_id');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class);
    }
}