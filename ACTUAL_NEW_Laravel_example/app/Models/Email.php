<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Email
 *
 * @property int $id
 * @property string|null $from_name
 * @property string|null $from_email
 * @property string|null $to
 * @property string|null $subject
 * @property string|null $msgHTML
 * @property bool $success
 * @property string|null $comment
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email query()
 * @mixin \Eloquent
 */
class Email extends Model
{
    protected $table = 'email';

    protected $casts = [
        'success' => 'bool'
    ];

    protected $fillable = [
        'from_name',
        'from_email',
        'to',
        'subject',
        'msgHTML',
        'success',
        'comment'
    ];
}
