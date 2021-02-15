<?php

use Illuminate\Database\Seeder;

class SetLectureSeats extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lectureList = \App\Lecture::get();

        foreach ($lectureList as $lecture)
        {
            if($lecture->room_booking_id) {
                $room = \App\Services\MirasApi::request(\App\Services\MirasApi::ROOM_RESERVE_INFO, [
                    'id' => $lecture->room_booking_id
                ]);

                if(isset($room->seats_count)) {
                    $lecture->seats_count = $room->seats_count;
                    $lecture->save();
                }
            }
        }
    }
}
