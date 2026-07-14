<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
    'title',
    'body',
    'type',
    'file_path',
    'priority',
    'status',
    'is_emergency',
    'show_from',
    'show_to',
    'time_start',
    'time_end',
    'ai_summary',
    'author_id',
    'board_id',
    'notified_teacher_id',
    'notified_seen',
    'year', 
    'section', 
    'teacher_reply',
    'replied_at',
    'course_id',
];
public function author()
{
    return $this->belongsTo(\App\Models\User::class, 'author_id');
}
protected $casts = [
    'show_from'    => 'date',
    'show_to'      => 'date',
    'is_emergency' => 'boolean',
    'notified_seen' => 'boolean',

    'replied_at' => 'datetime',
];

public function views()
{
    return $this->hasMany(\App\Models\NoticeView::class);
}

public function board()
{
    return $this->belongsTo(\App\Models\Board::class);
}

public function notifiedTeacher()
{
    return $this->belongsTo(\App\Models\User::class, 'notified_teacher_id');
}

public function course()
{
return $this->belongsTo(\App\Models\Course::class);
}

}
