<?php

namespace App\Console\Commands;

use App\Lecture;
use App\Services\MirasApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DropReserveRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drop:reserve_room';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lectureList = Lecture::
            where('room_booking_id', '!=', '')->
            where('room_payed', false)->
            where('start', '>', \DB::raw('now()') )->
            whereRaw('(TO_DAYS(`start`) - TO_DAYS(now())) < 3')->
            get();

        $bookingIds = [];
        foreach ($lectureList as $lecture)
        {
            $result = MirasApi::request(MirasApi::ROOM_RESERVE_DELETE, [
                'id' => $lecture->room_booking_id
            ]);

            if($result)
            {
                $bookingIds[] = $lecture->room_booking_id;
                $lecture->room_booking_id = null;
                $lecture->save();
            }
        }

        Log::info('Drop Reserve Room', ['room_booking_ids' => $bookingIds]);
    }
}
