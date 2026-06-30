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
];
public function author()
{
    return $this->belongsTo(\App\Models\User::class, 'author_id');
}
protected $casts = [
    'show_from'    => 'date',
    'show_to'      => 'date',
    'is_emergency' => 'boolean',
];

public function views()
{
    return $this->hasMany(\App\Models\NoticeView::class);
}

public function board()
{
    return $this->belongsTo(\App\Models\Board::class);
}
}
