<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\OrderName;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int id
 * @property int order_action_id
 */
class Order extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'orders';
    protected $fillable = [
        'order_name_id',
        'number',
        'date',
        'npa',
        'order_action_id',
        'study_form_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderName()
    {
        return $this->hasOne(OrderName::class, 'id', 'order_name_id');
    }

    public function action()
    {
        return $this->hasOne(OrderAction::class, 'id', 'order_action_id');
    }

    public function signatures()
    {
        return $this->hasMany(
            OrderUserSignature::class,
            'order_id',
            'id'
        );
    }

    /**
     * @return int
     */
    static function idForNew()
    {
        $model = Order::select('id')->orderBy('id')->first();

        return !$model ? 1 : $model->id + 1;
    }

    public function attachUser(int $userId)
    {
        OrderUser::addIfDoesntExist($this->id, $userId, $this->order_action_id);

        if (in_array($this->order_action_id, [1, 2, 3, 9])) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_SEND_DOWN);
        }
        elseif ($this->order_action_id == 4) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_STUDENT);
        }
        elseif ($this->order_action_id == 5) {
            Profiles::setNextCourse($userId);
        }
        elseif ($this->order_action_id == 6) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_PREGRADUATE);
        }
        elseif ($this->order_action_id == 7) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_ACADEMIC_LEAVE);
        }
        elseif ($this->order_action_id == 8) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_STUDENT);
        }
        elseif (in_array($this->order_action_id, [10, 11])) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_GRADUATE);
        }
        elseif($this->order_action_id == 12) {
            Profiles::changeEducationStatus($userId, Profiles::EDUCATION_STATUS_TEMP_SUSPENDED);
        }
    }

    public static function getUserOrderNumber(int $userId, $orderName)
    {
        return self::select('orders.number', 'orders.created_at', 'orders.date', 'orders.id')
                ->leftJoin('order_names', 'order_names.id', 'orders.order_name_id')
                ->leftJoin('order_user', 'order_user.order_id', 'orders.id')
                ->where('code', $orderName)
                ->where('user_id', $userId)
                ->first();
    }

    /**
     * @param $userId
     * @return bool
     */
    public function checkSignature($userId)
    {
        return (bool)$this
            ->signatures
            ->where('user_id', $userId)
            ->where('signed', true)
            ->count();
    }
}
