<?php

namespace App\Console\Commands;

use App\Models\{
    NobdAcademicLeave,
    NobdAcademicMobility,
    NobdCauseStayYear,
    NobdCountry,
    NobdDisabilityGroup,
    NobdEmploymentOpportunity,
    NobdEvents,
    NobdExchangeSpecialty,
    NobdFormDiplom,
    NobdLanguage,
    NobdPaymentType,
    NobdReasonDisposal,
    NobdReward,
    NobdTrainedQuota,
    NobdTypeDirection,
    NobdTypeEvent,
    NobdTypeViolation,
    NobdStudyExchange
};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};
use Maatwebsite\Excel\Facades\Excel;

class ImportNoBDData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:nobd';

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

        try {


            // nobd_language 47
            $aNobdLanguageInsert = [];
            $nobd_language = Excel::load(public_path('nobd/47.xlsx'));
            if( !empty($nobd_language) )
            {
                $nobd_language = $nobd_language->toArray();
                foreach( $nobd_language as $keyNL => $itemNL )
                {
                    $aNobdLanguageInsert[$keyNL] = [
                        'id'            => $keyNL+1,
                        'code'          => trim($itemNL['code']),
                        'name'          => trim($itemNL['ru_name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_language')->insert($aNobdLanguageInsert);


            // nobd_events 141
            $aNobdEventsInsert = [];
            $nobd_events = Excel::load(public_path('nobd/141.xlsx'));
            if( !empty($nobd_events) )
            {
                $nobd_events = $nobd_events->toArray();
                foreach( $nobd_events as $keyE => $itemE )
                {
                    $aNobdEventsInsert[$keyE] = [
                        'id'            => $keyE+1,
                        'code'          => trim($itemE['code']),
                        'name'          => trim($itemE['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_events')->insert($aNobdEventsInsert);


            // nobd_reward 143
            $aNobdRewardInsert = [];
            $nobd_reward = Excel::load(public_path('nobd/143.xlsx'));
            if( !empty($nobd_reward) )
            {
                $nobd_reward = $nobd_reward->toArray();
                foreach( $nobd_reward as $keyR => $itemR )
                {
                    $aNobdRewardInsert[] =  [
                        'code'          => trim($itemR['code']),
                        'name'          => trim($itemR['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_reward')->insert($aNobdRewardInsert);


            // nobd_payment_type 267
            $aNobdPaymentTypeInsert = [];
            $nobd_payment_type = Excel::load(public_path('nobd/267.xlsx'));
            if( !empty($nobd_payment_type) )
            {
                $nobd_payment_type = $nobd_payment_type->toArray();
                foreach( $nobd_payment_type as $keyPT => $itemPT )
                {
                    $aNobdPaymentTypeInsert[$keyPT] =  [
                        'id'            => $keyPT+1,
                        'code'          => trim($itemPT['code']),
                        'name'          => trim($itemPT['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_payment_type')->insert($aNobdPaymentTypeInsert);


            // nobd_employment_opportunity 270
            $aNobdEmploymentOpportunityInsert = [];
            $nobd_employment_opportunity = Excel::load(public_path('nobd/270.xlsx'));
            if( !empty($nobd_employment_opportunity) )
            {
                $nobd_employment_opportunity = $nobd_employment_opportunity->toArray();
                foreach( $nobd_employment_opportunity as $keyEO => $itemEO )
                {
                    $aNobdEmploymentOpportunityInsert[$keyEO] =  [
                        'id'            => $keyEO+1,
                        'code'          => trim($itemEO['code']),
                        'name'          => trim($itemEO['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }

            }
            \DB::table('nobd_employment_opportunity')->insert($aNobdEmploymentOpportunityInsert);


            // nobd_cause_stay_year 38349
            $aNobdCauseStayYearInsert = [];
            $nobd_cause_stay_year = Excel::load(public_path('nobd/38349.xlsx'));
            if( !empty($nobd_cause_stay_year) )
            {
                $nobd_cause_stay_year = $nobd_cause_stay_year->toArray();
                foreach( $nobd_cause_stay_year as $keyCSE => $itemCSE )
                {
                    $aNobdCauseStayYearInsert[$keyCSE] =  [
                        'id'            => $keyCSE+1,
                        'code'          => trim($itemCSE['code']),
                        'name'          => trim($itemCSE['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_cause_stay_year')->insert($aNobdCauseStayYearInsert);


            // nobd_form_diplom 38631
            $aNobdFormDiplomInsert = [];
            $nobd_form_diplom = Excel::load(public_path('nobd/38631.xlsx'));
            if( !empty($nobd_form_diplom) )
            {
                $nobd_form_diplom = $nobd_form_diplom->toArray();
                foreach( $nobd_form_diplom as $keyFD => $itemFD )
                {
                    $aNobdFormDiplomInsert[$keyFD] =  [
                        'id'            => $keyFD+1,
                        'code'          => trim($itemFD['code']),
                        'name'          => trim($itemFD['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_form_diplom')->insert($aNobdFormDiplomInsert);


            // nobd_country 38934
            $aNobdCountryInsert = [];
            $nobd_country = Excel::load(public_path('nobd/38934.xlsx'));
            if( !empty($nobd_country) )
            {
                $nobd_country = $nobd_country->toArray();
                foreach( $nobd_country as $keyC => $itemC )
                {
                    $aNobdCountryInsert[$keyC] =  [
                        'id'            => $keyC+1,
                        'code'          => trim($itemC['code']),
                        'name'          => trim($itemC['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_country')->insert($aNobdCountryInsert);


            // nobd_academic_leave 38994
            $aNobdAcademicLeaveInsert = [];
            $nobd_academic_leave = Excel::load(public_path('nobd/38994.xlsx'));
            if( !empty($nobd_academic_leave) )
            {
                $nobd_academic_leave = $nobd_academic_leave->toArray();
                foreach( $nobd_academic_leave as $keyAL => $itemAL )
                {
                    $aNobdAcademicLeaveInsert[$keyAL] =  [
                        'id'            => $keyAL+1,
                        'code'          => trim($itemAL['code']),
                        'name'          => trim($itemAL['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_academic_leave')->insert($aNobdAcademicLeaveInsert);


            // nobd_type_violation 39037
            $aNobdTypeViolationInsert = [];
            $nobd_type_violation = Excel::load(public_path('nobd/39037.xlsx'));
            if( !empty($nobd_type_violation) )
            {
                $nobd_type_violation = $nobd_type_violation->toArray();
                foreach( $nobd_type_violation as $keyTV => $itemTV )
                {
                    $aNobdTypeViolationInsert[$keyTV] =  [
                        'id'            => $keyTV+1,
                        'code'          => trim($itemTV['code']),
                        'name'          => trim($itemTV['ru_name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_type_violation')->insert($aNobdTypeViolationInsert);


            // nobd_type_event 39096
            $aNobdTypeEventInsert = [];
            $nobd_type_event = Excel::load(public_path('nobd/39096.xlsx'));
            if( !empty($nobd_type_event) )
            {
                $nobd_type_event = $nobd_type_event->toArray();
                foreach( $nobd_type_event as $keyTE => $itemTE )
                {
                    $aNobdTypeEventInsert[$keyTE] =  [
                        'id'            => $keyTE+1,
                        'code'          => trim($itemTE['code']),
                        'name'          => trim($itemTE['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_type_event')->insert($aNobdTypeEventInsert);


            // nobd_type_direction 39097
            $aNobdTypeDirectionInsert = [];
            $nobd_type_direction = Excel::load(public_path('nobd/39097.xlsx'));
            if( !empty($nobd_type_direction) )
            {
                $nobd_type_direction = $nobd_type_direction->toArray();
                foreach( $nobd_type_direction as $keyTD => $itemTD )
                {
                    $aNobdTypeDirectionInsert[$keyTD] =  [
                        'id'            => $keyTD+1,
                        'code'          => trim($itemTD['code']),
                        'name'          => trim($itemTD['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_type_direction')->insert($aNobdTypeDirectionInsert);


            // nobd_trained_quota 39161
            $aNobdTrainedQuotaInsert = [];
            $nobd_trained_quota = Excel::load(public_path('nobd/39161.xlsx'));
            if( !empty($nobd_trained_quota) )
            {
                $nobd_trained_quota = $nobd_trained_quota->toArray();
                foreach( $nobd_trained_quota as $keyTQ => $itemTQ )
                {
                    $aNobdTrainedQuotaInsert[$keyTQ] =  [
                        'id'            => $keyTQ+1,
                        'code'          => trim($itemTQ['code']),
                        'name'          => trim($itemTQ['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_trained_quota')->insert($aNobdTrainedQuotaInsert);


            // nobd_disability_group 39162
            $aNobdDisabilityGroupInsert = [];
            $nobd_disability_group = Excel::load(public_path('nobd/39162.xlsx'));
            if( !empty($nobd_disability_group) )
            {
                $nobd_disability_group = $nobd_disability_group->toArray();
                foreach( $nobd_disability_group as $keyDG => $itemDG )
                {
                    $aNobdDisabilityGroupInsert[$keyDG] =  [
                        'id'            => $keyDG+1,
                        'code'          => trim($itemDG['code']),
                        'name'          => trim($itemDG['ru_name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_disability_group')->insert($aNobdDisabilityGroupInsert);


            // nobd_reason_disposal 39165
            $aNobdReasonDisposalInsert = [];
            $nobd_reason_disposal = Excel::load(public_path('nobd/39165.xlsx'));
            if( !empty($nobd_reason_disposal) )
            {
                $nobd_reason_disposal = $nobd_reason_disposal->toArray();
                foreach( $nobd_reason_disposal as $keyRD => $itemRD )
                {
                    $aNobdReasonDisposalInsert[$keyRD] =  [
                        'id'            => $keyRD+1,
                        'code'          => trim($itemRD['code']),
                        'name'          => trim($itemRD['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_reason_disposal')->insert($aNobdReasonDisposalInsert);


            // nobd_academic_mobility 39264
            $aNobdAcademicMobilityInsert = [];
            $nobd_academic_mobility = Excel::load(public_path('nobd/39264.xlsx'));
            if( !empty($nobd_academic_mobility) )
            {
                $nobd_academic_mobility = $nobd_academic_mobility->toArray();
                foreach( $nobd_academic_mobility as $keyAM => $itemAM )
                {
                    $aNobdAcademicMobilityInsert[$keyAM] =  [
                        'id'            => $keyAM+1,
                        'code'          => trim($itemAM['code']),
                        'name'          => trim($itemAM['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_academic_mobility')->insert($aNobdAcademicMobilityInsert);


            // nobd_exchange_specialty 39266
            $aNobdExchangeSpecialtyInsert = [];
            $nobd_exchange_specialty = Excel::load(public_path('nobd/39266.xlsx'));
            if( !empty($nobd_exchange_specialty) )
            {
                $nobd_exchange_specialty = $nobd_exchange_specialty->toArray();
                foreach( $nobd_exchange_specialty as $keyES => $itemES )
                {
                    $aNobdExchangeSpecialtyInsert[$keyES] =  [
                        'id'            => $keyES+1,
                        'code'          => trim($itemES['code']),
                        'name'          => trim($itemES['name']),
                        'created_at'    => date('Y-m-d H:i',time())
                    ];
                }
            }
            \DB::table('nobd_exchange_specialty')->insert($aNobdExchangeSpecialtyInsert);


            // nobd_study_exchange
            $aNobdStudyExchange = [];
            $aNobdStudyExchange = [
                0 => [
                    'code'          => '-1',
                    'name'          => 'Нет',
                    'created_at'    => date('Y-m-d H:i',time())
                ],
                1 => [
                    'code'          => '00',
                    'name'          => 'Прибывший из-за рубежа',
                    'created_at'    => date('Y-m-d H:i',time())
                ],
                2 => [
                    'code'          => '01',
                    'name'          => 'Выехавший за рубеж',
                    'created_at'    => date('Y-m-d H:i',time())
                ],
                3 => [
                    'code'          => '03',
                    'name'          => 'Прибывший из ВУЗ РК',
                    'created_at'    => date('Y-m-d H:i',time())
                ],
                4 => [
                    'code'          => '04',
                    'name'          => 'Выехавший в ВУЗ РК',
                    'created_at'    => date('Y-m-d H:i',time())
                ]
            ];
            \DB::table('nobd_study_exchange')->insert($aNobdStudyExchange);


        } catch ( \Exception $e ) {

            Log::info('Import Nobd data error: ' . $e->getMessage());
        }


    }

}
