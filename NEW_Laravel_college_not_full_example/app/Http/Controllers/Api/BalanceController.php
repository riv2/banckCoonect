<?php

namespace App\Http\Controllers\Api;

use App\FinanceNomenclature;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class BalanceController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if(!env('API_BALANCE_UPDATE_ENABLED', false))
        {
            return Response::json([
                'status' => 'fail',
                'message' => 'Api disabled'
            ], 404);
        }

        $operationList = $request->all();

        if(!is_array($operationList))
        {
            return Response::json([
                'status' => 'fail',
                'message' => 'Body must be array'
            ], 400);
        }

        $hasError = false;

        foreach ($operationList as $operation)
        {
            $iin = $operation['iin'] ?? null;
            $cost = $operation['cost'] ?? null;
            $code = $operation['code'] ?? null;

            Log::info('Update balance from 1c',
                ['iin' => $iin, 'code' => $code, 'cost' => $cost]);

            if($iin !== null && $code !== null && $cost !== null)
            {
                $users = User
                    /*::whereHas('studentProfile', function($query) use($iin){
                    $query->where('iin', $iin);
                })*/
                    ::select(['users.id as id', 'balance'])
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                    ->where('profiles.iin', $iin)
                    ->get();

                $nomenclature = FinanceNomenclature::where('code', $code)->first();

                if($nomenclature)
                {
                    if($users)
                    {
                        foreach ($users as $user) {
                            $user->changeBalance($cost, $nomenclature);
                        }
                    }
                    else
                    {
                        Log::error('Balance update: User not found',
                            ['iin' => $iin, 'code' => $code, 'cost' => $cost]);
                        $hasError = true;
                    }
                }
                else
                {
                    Log::error('Balance update: Nomenclature not found',
                        ['iin' => $iin, 'code' => $code, 'cost' => $cost]);
                    $hasError = true;
                }
            }
            else
            {
                Log::error('Balance update: Invalid params',
                    ['iin' => $iin, 'code' => $code, 'cost' => $cost]);
                $hasError = true;
            }
        }

        if($hasError)
        {
            throw new \Exception('Balance update error. More info in log file');
        }

        return Response::json([
            'status' => 'success'
        ]);
    }
}
