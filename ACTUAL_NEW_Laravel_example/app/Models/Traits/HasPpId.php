<?php

namespace App\Models\Traits;

use App\Helpers\PartnerProgramStorage;
use App\Models\Pp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Трейт для моделей, содержащих колонку pp_id
 *
 * @author Tony V <vaninanton@gmail.com>
 * @method mixed methodName()
 */
trait HasPpId
{
    /** @return void */
    public static function bootHasPpId()
    {
        static::addGlobalScope('pp', function (Builder $query) {
            $pp_id = PartnerProgramStorage::getPP()->id ?? null;

            /**
             * Если мы находимся в партнерской программе
             * применяем pp_id для всех записей
             * @todo Сделать проверку на роль юзера, менеджер должен видеть всё
             * @todo сейчас это в принципе и так работает, так как у менеджера pp_id не задан
             */
            return $query->when($pp_id, function (Builder $query, $value) {
                return $query->where('pp_id', '=', $value);
            });
        });

        /**
         * При создании модели прописываем pp_id автоматически
         */
        static::creating(function ($item) {
            if (!$item->pp_id) {
                $item->pp_id = PartnerProgramStorage::getPP()->id ?? null;
            }
        });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function pp(): BelongsTo
    {
        return $this->belongsTo(Pp::class, 'pp_id', 'id');
    }
}
