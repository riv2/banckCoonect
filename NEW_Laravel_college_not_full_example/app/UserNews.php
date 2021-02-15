<?php
/**
 * User: dadicc
 * Date: 4/2/20
 * Time: 8:28 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNews extends Model
{

    protected $table = 'user_news';
    protected $fillable = [
        'user_id',
        'news_id'
    ];

}