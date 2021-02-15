<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Notification
 * @package App
 *
 * @property int id
 * @property int user_id
 * @property string text
 * @property bool read
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 */
class Notification extends Model
{
    use SoftDeletes;

    protected $table = 'notifications';

    public $casts = [
        'read' => 'boolean'
    ];

    public static function add(int $userId, string $text)
    {
        $new = new self;
        $new->user_id = $userId;
        $new->text = $text;
        return $new->save();
    }
}
