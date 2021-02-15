<?php

namespace App\Console\Commands\Fix;

use App\Order;
use App\Profiles;
use App\Speciality;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;

class ShiftOfSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'speciality:shift';

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
        $file = fopen(storage_path('import/shift_speciality.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/shift_speciality.csv')));
        $updatedCount = 0;

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {

            $userId = $row[0];
            $newSpeciality = $row[4];
            $newSpecialityId = $row[3];

            if($this->isExceptionUser($userId))
            {
                $this->printMessage('Exception user', $userId, $newSpeciality);
                continue;
            }

            if(!$newSpecialityId)
            {
                $speciality = null;
            }
            else
            {
                $speciality = Speciality::where('id', $newSpecialityId)->first();
            }

            $userProfile = Profiles::where('user_id', $userId)->first();
            //$declineOrder = $this->getOrderByName($row[7]);
            //$addOrder = $this->getOrderByName($row[12]);

            if(!$userProfile)
            {
                $this->printMessage('User not found', $userId, $newSpeciality);
                continue;
            }

            if(!$speciality && $newSpecialityId)
            {
                $this->printMessage('Speciality not found', $userId, $newSpeciality);
                continue;
            }

            /*if(!$declineOrder)
            {
                $this->printMessage('Decline order not found', $userId, $newSpeciality);
                continue;
            }
*/
            /*if(!$addOrder)
            {
                $this->printMessage('Add order not found', $userId, $newSpeciality);
                continue;
            }*/

            $this->shiftSpeciality(
                $userProfile,
                $speciality//,
            /*$declineOrder,*/
            //$addOrder
            );

            $updatedCount++;

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info('Updated count: ' . $updatedCount);
    }

    /**
     * @param $userProfile
     * @param $speciality
     * @param $orderOut
     * @param $orderInId
     * @return bool
     */
    public function shiftSpeciality(
        $userProfile,
        $speciality//,
        //$declineOrder,
        //$addOrder
    )
    {
        /*$declineOrder->attachUser($userProfile->user_id);*/
        //$addOrder->attachUser($userProfile->user_id);

        if($speciality !== null && $userProfile->education_speciality_id != $speciality->id)
        {
            $userProfile->education_speciality_id = $speciality->id;
            $userProfile->save();
            $userProfile->updateDisciplines();
        }

        return true;
    }

    /**
     * @param $name
     * @return bool
     */
    public function getOrderByName($name)
    {
        $name = str_replace('№', '', $name);
        $name = trim($name);

        $name = explode(' ', $name);

        if(!empty($name[0]))
        {
            return Order::where('number', $name)->first();
        }

        return false;
    }

    /**
     * @param $message
     * @param $userId
     * @param $newSpeciality
     */
    public function printMessage($message, $userId, $newSpeciality)
    {
        $this->info('-----');
        $this->info('user_id = ' . $userId);
        $this->info('new_speciality = ' . $newSpeciality);
        $this->error($message);
        $this->info('-----');
    }

    public function isExceptionUser($userId)
    {
        return in_array($userId, [
        ]);
    }
}
