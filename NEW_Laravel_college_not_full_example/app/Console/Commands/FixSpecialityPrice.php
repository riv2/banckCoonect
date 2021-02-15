<?php

namespace App\Console\Commands;

use App\SpecialityPrice;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\Types\Self_;

class FixSpecialityPrice extends Command
{
    const FIELD_ID = 0;
    const FIELD_SPECIALITY = 1;
    const FIELD_YEAR = 2;
    const FIELD_STUDY_FORM = 3;
    const FIELD_BASE_EDUCATION = 4;
    const FIELD_PRICE_TYPE = 5;
    const FILED_PRICE = 6;
    const FIELD_NEW_PRICE = 7;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:speciality:price';

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
        $fileName = storage_path('import/fix_speciality_price.csv');
        $file = fopen($fileName, 'r');
        $this->output->progressStart(sizeof(file ($fileName)));
        $strNum = 0;
        $changePriceCount = 0;

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $strNum++;

            if(!empty($row[self::FIELD_ID]) && !empty($row[self::FIELD_NEW_PRICE]) && is_numeric($row[self::FIELD_NEW_PRICE]))
            {
                $studyForm = $this->convertStudyForm($row[self::FIELD_STUDY_FORM]);
                $baseEducation = $this->convertBaseEducation($row[self::FIELD_BASE_EDUCATION]);
                $priceType = $this->convertPriceType($row[self::FIELD_PRICE_TYPE]);

                if($studyForm && $baseEducation && $priceType)
                {
                    $specPrice = SpecialityPrice
                        ::where('speciality_id', $row[self::FIELD_ID])
                        ->where('study_form', $studyForm)
                        ->where('base_education', $baseEducation)
                        ->where('price_type', $priceType)
                        ->first();

                    if($specPrice)
                    {
                        if($specPrice->price != $row[self::FIELD_NEW_PRICE])
                        {
                            $this->warn('Not equal. Id=' . $row[self::FIELD_ID] . ' study_form=' . $studyForm . ' base_education=' . $baseEducation . ' price_type=' . $priceType);

                            /*$specPrice->price = $row[self::FIELD_NEW_PRICE];
                            $specPrice->save();*/
                            $changePriceCount++;
                        }
                    }
                    else
                    {
                        $this->warn('Spec not found. Id=' . $row[self::FIELD_ID] . ' study_form=' . $studyForm . ' base_education=' . $baseEducation . ' price_type=' . $priceType);
                    }
                }
                else
                {
                    $this->warn('Error convert Id=' . $row[self::FIELD_ID]);
                }
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info('Change price count: ' . $changePriceCount);
    }

    /**
     * @param $form
     * @return mixed|string
     */
    public function convertStudyForm( $form )
    {
        $aliasList = [
            'Дистанционная (онлайн)' => 'online',
            'Очная' => 'fulltime',
            'Заочная' => 'extramural',
            'Вечерняя' => 'evening',
        ];

        return $aliasList[$form] ?? '';
    }

    /**
     * @param $education
     * @return mixed|string
     */
    public function convertBaseEducation( $education )
    {
        $aliasList = [
            'Высшее' => 'higher',
            'Высшее (Бакалавр)' => 'bachelor',
            'Средне-специальное' => 'vocational_education',
            'Среднее' => 'high_school'
        ];

        return $aliasList[$education] ?? '';
    }

    /**
     * @param $education
     * @return mixed|string
     */
    public function convertPriceType( $priceType )
    {
        $aliasList = [
            'Цена за 1 кредит для нерезидента' => 'credit_price_non_resident',
            'Цена за 1 кредит для резидента' => 'credit_price_resident'
        ];

        return $aliasList[$priceType] ?? '';
    }
}
