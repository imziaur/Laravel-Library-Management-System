<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueBook extends Model
{
    protected $fillable = [
    'user_id', 'book_id', 'rent', 'issue_date', 'return_date'
    ];

    public function book()
    {
        return $this->hasMany('App\Book', 'id');
    }
    public function user()
    {
        return $this->hasMany('App\User', 'id');
    }
}
