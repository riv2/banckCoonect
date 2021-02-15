<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-23
 * Time: 14:06
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Auth;
use App\Validators\{BuyServiceBuyWifiPackageValidator};
use App\Wifi;
use App\WifiTariff;
use Illuminate\Http\Request;
use UniFi_API\Client as Unifi;
use App\Services\Service1C;
use DateTime;
use DateTimeZone;
use App\WifiCode;
use App\WifiDevice;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeImage;
use Illuminate\Support\Facades\Log;

class WifiController extends Controller
{
    const UNIFI_CONTROLLER_USER     = 'ubnt';
    const UNIFI_CONTROLLER_PASSWORD = 'ubntmiras';
    const UNIFI_CONTROLLER_URL      = 'https://unifi.miras.edu.kz:8443';

    protected $_unifi = null;

    public function userAllowWifi(Request $request)
    {
        $guest = $this->getDeviceInfo($request->input('ip'));
        $user = Auth::User();

        if(!isset($guest->mac)) {
            return \Response::json([
                'status'  => false,
                'message' => __("Can not find your device in Wi-Fi guest session with your IP"). " " . $request->input('ip')
            ]);
        }
        $mac = $guest->mac;

        if( $this->checkIfAlreadyActive($mac) ) {
            return \Response::json([
                'status'  => false,
                'message' => __('You already have active wifi for now')
            ]);
        }

        if( $request->input('code') !== null ) {
            $allowCode = $this->useCode( $request->input('code') );
        } else {
            $tarif = WifiTariff
                ::where('id', $request->input('tarifId'))
                ->whereNull('deleted_at')
                ->first();
            $payCode = '00000007155';

            if(!isset($tarif->id)) {
                return \Response::json([
                    'status'  => false,
                    'message' => __("Can not find selected tariff where taridId") . ": " . $request->input('tarifId')
                ]);
            }
        }
        
        $minsTillMidnight = $this->getMinsTillMidnight();


        //taking money from user
        if(isset($tarif->id) && isset($user->studentProfile->iin) ) {
            $balance = Service1C::getBalance($user->studentProfile->iin);

            if($this->checkGuestAlreadyHadWifi($request->input('ip'))) {
                if($balance === false || $balance < $tarif->cost) {
                    return \Response::json([
                        'status'  => false,
                        'message' => __('Payment error')
                    ]);
                }
                $payResponse = Service1C::pay(
                    $user->studentProfile->iin,
                    $payCode,
                    $tarif->cost
                );
            } else {
                $payResponse = true;
            }
        }


        if(isset($payResponse) || $allowCode) {
            // giving full internet access
            $authorize = $this->_unifi->authorize_guest($mac, $minsTillMidnight);

            //adding data to DB
            $wifi = new Wifi;
            $wifi->user_id = $user->id;
            $wifi->expire = date('Y-m-d H:i:s', time() + $minsTillMidnight*60);
            $wifi->tariff_id = isset($tarif->id)?$tarif->id:0;
            $wifi->code = $request->input('code');
            $wifi->mac = $mac;
            $wifi->save();

            return \Response::json([
                'status'  => true,
                'message' => __('Success! You have been granted to full internet access')
            ]);

        } else {
            return \Response::json([
                'status'  => false,
                'message' => __('Payment error')
            ]);
        }

        
        return \Response::json([
            'status'  => false,
            'message' => __("Unknown error")
        ]);

          
    }

    public function getDeviceInfo($ip)
    {
        $userId = null;
        if(isset(Auth::user()->id)) {
            $userId = Auth::user()->id;
        }
        Log::channel('wifiips')->info('Wifi ip used:', [
            'ip'     => $ip,
            'userId' => $userId,
        ]);

        $this->_unifi = new Unifi(
            self::UNIFI_CONTROLLER_USER,
            self::UNIFI_CONTROLLER_PASSWORD,
            self::UNIFI_CONTROLLER_URL
        );
        $loginresults = $this->_unifi->login();
        if($loginresults) {
            $guests = $this->_unifi->list_clients();
        } else {
            return \Response::json([
                'status'  => false,
                'message' => __('Unifi auth error')
            ]);
        }

        //looking for user in unifi guest list
        foreach ($guests as $guest) {
            if( isset($guest->ip) && $guest->ip == $ip ) {
                return $guest;
            }
        }
    }

    public function getMinsTillMidnight()
    {
        $midnight = strtotime("midnight")+24*60*60;
        $now = time()+6*60*60;
        $secsTillMidnight = $midnight - $now;
        return (int) ($secsTillMidnight / 60);
    }

    public function checkIfAlreadyActive($mac)
    {
        $activeWifi = wifi
            //::where('user_id',Auth::user()->id)
            ::where('mac',$mac)
            ->where('expire', '>', date('Y-m-d H:i:s') )
            ->first();

        if(isset($activeWifi->id)) {
            return true;
        }

        return false;
    }

    public function useCode($code)
    {
        $wifiCode = WifiCode
            ::where('code', $code)
            ->where('used', 0)
            ->first();

        if(isset($wifiCode->id) ) {
            $wifiCode->used = 1;
            $wifiCode->save();
            return true;
        } else {
            return false;
        }
    }

    public function getNewCode()
    {
        if(\Session::get('coffeeKey') != env('COFFEE_KEYNUMBER')) {
            return redirect()->route('coffeeLoginPage');
        }


        $code = $this->generateCode();
        
        $qrImage = QrCodeImage::format('png')->size(400)->generate($code);

        return \Response::json([
            'qr' => 'data:image/png;base64,' . base64_encode($qrImage),
            'numeric_code' => $code
        ]);
    }

    public function generateCode()
    {
        do{
            $code = rand(100000, 999999);
            $wifiCodeExist = WifiCode
                ::where('code', $code)
                ->exists();
        } while($wifiCodeExist);

        $wifiCode = new WifiCode;
        $wifiCode->code = $code;
        $wifiCode->save();

        return $code;
    }

    public function login()
    {
        return view('coffee.login');
    }

    public function loginPost(Request $request)
    {
        $inputs = $request->all();
        //dd('f');
        if(empty($inputs['keynumber'])){
            return redirect()->route('coffeeLoginPage');
        }

        if($inputs['keynumber'] == env('COFFEE_KEYNUMBER')) {
            \Session::put('coffeeKey', $inputs['keynumber']);
        }

        return redirect()->route('coffeeMain');
    }

    public function main()
    {
        if(\Session::get('coffeeKey') == env('COFFEE_KEYNUMBER')) {
            return view('coffee.main');
        }
        return redirect()->route('coffeeLoginPage');
    }

    public function genWifiPage()
    {
        if(\Session::get('coffeeKey') == env('COFFEE_KEYNUMBER')) {
            return view('coffee.genWifi');
        }
        return redirect()->route('coffeeLoginPage');
    }

    public function guestPage()
    {
        if(isset(Auth::user()->id)) {
            return redirect()->route('wifi');
        }

        return view('pages.wifiGuest');
    }

    public function guestAlreadyHadWifi(Request $request)
    {
        $status = $this->checkGuestAlreadyHadWifi($request->input('ip'));

        return \Response::json(['status'  => $status]);
    }

    public function checkGuestAlreadyHadWifi($ip)
    {   
        $guest = $this->getDeviceInfo($ip);
        if(!isset($guest->mac)) {
            return false;
        }

        $exist = wifi::where('mac', $guest->mac)->first();
        if(isset($exist->id)) {
            return false;
        }
        return true;


    }

    public function teacherDashboard()
    {
        return view('teacher.devices');
    }

    public function teacherGetDevices()
    {
        $list = WifiDevice::where('user_id', Auth::user()->id)->get();
        return \Response::json($list);
    }

    public function teacherAddDevice(Request $request)
    {
        $inputs = $request->all();
        if(!isset($inputs['ip'])) {
            return \Response::json([
                'status'  => false
            ]);
        }

        //getting device data
        $guest = $this->getDeviceInfo($request->input('ip'));

        $device = new WifiDevice;
        $device->user_id = Auth::user()->id;
        $device->mac = $guest->mac;
        $device->name = $guest->hostname??'';
        $device->save();

        return \Response::json([
            'status'  => true
        ]);
    }

    public function teacherDeleteDevice(Request $request)
    {
        $inputs = $request->all();
        if(!isset($inputs['id'])) {
            return \Response::json([
                'status' => false
            ]);
        }

        $device = WifiDevice
            ::where('user_id', Auth::user()->id)
            ->where('id', $inputs['id'])
            ->delete();

        return \Response::json([
            'status'  => true
        ]);
    }


    //@todo do we need this function?
    public function ajaxGetList( Request $request )
    {

        $oWifi = Wifi::
        where('user_id',Auth::user()->id)->
        get();

        return \Response::json([
            'status'  => true,
            'message' => __('Success'),
            'data'    => $oWifi
        ]);

    }

}