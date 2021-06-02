<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrackingMailsToUsers
 *
 * @property int $id
 * @property int $user_id
 * @property float|null $mail_stage
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TrackingMailsToUsers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackingMailsToUsers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackingMailsToUsers query()
 * @mixin \Eloquent
 */
class TrackingMailsToUsers extends Model
{
    protected $table = 'tracking_mails_to_users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'mail_stage',
    ];
}
