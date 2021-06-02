<?php
namespace App\Http\Controllers;
use App\Providers\GateWaysServicePrivider;
use App\Providers\BillingExtServiceProvider;

use Illuminate\Http\Request;
use Illuminate\Http\Response;


class GatewaysController extends Controller
{

    private $gateWaysServicePrivider;

    public function __construct(Request $request)
    {
        $path = explode('/', $request->path());
        $gatewayName = $path[0];
        $this->gateWaysServicePrivider = new GateWaysServicePrivider($gatewayName);
    }

    public function robokassaResult(Request $request)
    {
        $this->gateWaysServicePrivider->doResult($request->all());

        return response()->json([
            'status' => 'accepted'
        ]);
    }

    public function robokassaSuccess(Request $request)
    {
       // $result = $this->gateWaysServicePrivider->doSuccess($request->all());
        return redirect()->route('myplan', ['gateway_status' => 'success']);
    }

    public function robokassaFail(Request $request)
    {
     //   $result = $this->gateWaysServicePrivider->doFail($request->all());
        return redirect()->route('myplan', ['gateway_status' => 'fail']);
    }


}


