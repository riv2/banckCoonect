<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Upload
 *
 * @property int $id
 * @property string $filename
 * @property string $object_type
 * @property int|null $object_id
 * @property string $mime
 * @property string $filesize
 * @property string|null $access
 * @property int|null $user_id
 * @property \Jenssegers\Date\Date|null $created_at
 * @property \Jenssegers\Date\Date|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Upload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Upload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Upload query()
 * @mixin \Eloquent
 */
class Upload extends Model
{
    protected $table = 'uploads';

    protected $casts = [
        'object_id' => 'int',
        'user_id' => 'int'
    ];

    protected $fillable = [
        'filename',
        'object_type',
        'object_id',
        'mime',
        'filesize',
        'access',
        'user_id'
    ];
}
