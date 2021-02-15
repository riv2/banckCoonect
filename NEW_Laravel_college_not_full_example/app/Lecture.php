<?php

namespace App;

use App\Http\Controllers\Teacher\LectureController;
use App\Services\Auth;
use App\Services\MirasApi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Lecture extends Model
{
    use SoftDeletes;

    const TYPE_ONLINE = 'online';
    const TYPE_OFFLINE = 'offline';
    const TYPE_ALL = 'all';

    const STATUS_FUTURE     = 'future';
    const STATUS_PROCESS    = 'process';
    const STATUS_FINISH     = 'finish';

    protected $table = 'lectures';

    protected $fillable = [
        'title',
        'description',
        'duration',
        'start',
        'type',
        'url',
        'cost',
        'tags'
    ];

    protected $dates = [
        'start'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratingList()
    {
        return $this->hasMany(LectureRating::class, 'lecture_id', 'id');
    }

    /**
     * @param $value
     */
    public function setStartAttribute($value)
    {
        $this->attributes['start'] = Carbon::createFromFormat('d.m.Y H:i', $value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payDocuments()
    {
        return $this->belongsToMany(
            PayDocument::class, 'pay_documents_lectures', 'lecture_id', 'pay_document_id');
    }

    public function payDocument()
    {
        return $this->payDocuments->first();
    }

    /**
     * @return $this
     */
    public function students()
    {
        return $this->belongsToMany(
            User::class, 'student_lecture', 'lecture_id', 'user_id')->withPivot('type');
    }

    public function offlineStudents()
    {
        return $this->belongsToMany(
            User::class, 'student_lecture', 'lecture_id', 'user_id')->
            where('student_lecture.type', StudentLecture::TYPE_OFFLINE)->
            withPivot('type');
    }

    /**
     * @param $start
     * @param $duration
     * @param $roomParams
     * @return bool
     */
    public function reserveRoom($start, $duration, $roomParams)
    {
        $roomParams['start']                = date('Y-m-d H:i:s', strtotime($start));
        $roomParams['duration']             = $duration;
        $roomParams['remote_holder_id']     = Auth::user()->id;
        $roomParams['remote_holder_info']   = json_encode([
            'fio' => Auth::user()->teacherProfile->fio
        ]);
        $roomParams['stuff'] = Room::compareStuff($roomParams['stuff']);

        if(isset($roomParams['building_id']) && $roomParams['building_id'] == 0)
        {
            $roomParams['building_id'] = null;
        }

        $result = MirasApi::request(MirasApi::ROOM_RESERVE, $roomParams);

        if(isset($result->id))
        {
            $this->room_booking_id = $result->id;
            $this->seats_count = $result->seats_count;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getReserveRoomInfo()
    {
        if(isset($this->room_booking_id) && $this->room_booking_id)
        {
            $this->room = MirasApi::request(MirasApi::ROOM_RESERVE_INFO, ['id' => $this->room_booking_id]);

            if(!$this->room)
            {
                return false;
            }

            $this->room->stuffIds = Room::getStuffIdList($this->room->stuff);

            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteReserveRoom()
    {
        if(isset($this->room_booking_id) && $this->room_booking_id)
        {
            MirasApi::request(MirasApi::ROOM_RESERVE_DELETE, ['id' => $this->room_booking_id]);
            $this->room_booking_id = null;
            return true;
        }
        return false;
    }
}
