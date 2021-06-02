<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MailLogger
 *
 * @property string $id
 * @property array $recipients
 * @property array $sender
 * @property string $subject
 * @property string $message
 * @property string $status
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MailLogger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailLogger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MailLogger query()
 * @mixin \Eloquent
 */
class MailLogger extends Model
{
    protected $table = 'mail_logger';
    public $incrementing = false;

    protected $casts = [
        'recipients' => 'json',
        'sender' => 'json'
    ];

    protected $fillable = [
        'recipients',
        'sender',
        'subject',
        'message',
        'status'
    ];
}
