<?php

namespace App\Rules\Student;

use App\Lecture;
use App\Services\MirasApi;
use App\StudentLecture;
use Illuminate\Contracts\Validation\Rule;

class FreeSeats implements Rule
{
    private $type = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->type == 'online')
        {
            return true;
        }

        $lecture = Lecture::where('id', $value)->first();

        if(isset($lecture->room_booking_id) && $lecture->room_booking_id != '') {
            $room = MirasApi::request(MirasApi::ROOM_RESERVE_INFO, [
                'id' => $lecture->room_booking_id
            ]);

            if ($room) {
                $studentCount = StudentLecture::
                where('lecture_id', $lecture->id)->
                where('type', Lecture::TYPE_OFFLINE)->
                count();

                if ($room->seats_count > $studentCount) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No free seats';
    }
}
