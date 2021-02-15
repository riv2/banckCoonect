<?php

namespace App\Console\Commands\Fix;

use App\PayDocument;
use App\Services\Service1C;
use App\StudentDiscipline;
use App\User;
use Auth;
use Illuminate\Console\Command;

class RemoveDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:discount:remove';

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
        $userId = 13726;

        $user = User::with('studentProfile')->where('id', $userId)->first();

        $studentDisciplineList = StudentDiscipline
            ::where('student_id', $userId)
            ->whereNotNull('payed_credits')
            ->get();

        $creditPrice = $user->credit_price;

        foreach($studentDisciplineList as $studentDiscipline)
        {
            $amount = $studentDiscipline->payed_credits * $creditPrice;
            $linkId = StudentDiscipline::getId($userId, $studentDiscipline->discipline_id);
            $payDocument = PayDocument::createForStudentDiscipline($userId, $this->getOrderId($studentDiscipline->id), $amount, $studentDiscipline->payed_credits, $linkId, $user->balance);

            $paySuccess = Service1C::payDiscipline($user->studentProfile->iin, $amount, $payDocument);

            $this->info('-----');
            $this->info('DisciplineId = ' . $studentDiscipline->discipline_id);
            $this->info('Credits = ' . $studentDiscipline->payed_credits);
            $this->info('Credit price = ' . $creditPrice);
            $this->info('Amount = ' . $amount);
            $this->info('Pay result = ' . $paySuccess);
            $this->info('-----');
        }

    }

    private function getOrderId(int $id)
    {
        return time() . $id;
    }
}
