<?php

namespace App\Console\Commands;

use App\Profiles;
use App\Refund;
use App\StudentDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Service1C;
use Carbon\Carbon;
use App\Mail\RefundBankReject;

class RefundKaspiImport extends Command
{
    const FOLDER_FOR_USER_1C = 'used';
    const MAX_ATTEMPTS_1C_REQUEST = 3;
    const PAUSE_BETWEN_ATTEMPTS_1C_REQUEST = 60; // seconds

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refund:kaspi:import {--transactions-pages=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import 1C file to Kaspi';

    protected $orderId = null;
    protected $baseUrl = 'https://kaspi.kz/business/';
    protected $sessionID = null;
    protected $kaspiBizAuth = null;
    protected $orderNumber = null;

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
        if ( !env('KASPI_ALLOW', false) ) {
            $this->info('Disallowed by env');
            return;
        }
        
        $this->kaspiAuth();

        $this->kaspiFilesProcess();
        $this->kaspiTransactionProcess();

    }

    public function getCurlCookie($result)
    {
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = [];
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        return $cookies;
    }

    public function getCurlInput($result)
    {
        $html = '<!DOCTYPE html>' . explode('<!DOCTYPE html>', $result)[1];
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        $xpath = new \DOMXPath($dom);
        $items = [];
        $tags = $xpath->query('//input');
        foreach ($tags as $tag) {
            $items[trim($tag->getAttribute('name'))] = trim($tag->getAttribute('value'));
        }

        return $items;
    }

    public function getHistoryPaymentDataList($result)
    {
        $html = '<!DOCTYPE html>' . explode('<!DOCTYPE html>', $result)[1];
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        $xpath = new \DOMXPath($dom);
        $items = [];
        $rows = $xpath->query('//table//tr');
        foreach ($rows as $rowKey => $row) {
            if($rowKey <= 1) {
                continue;
            }
            $cells = $row->getElementsByTagName('td');

            $cellData = [];
            foreach ($cells as $key=>$cell) {
                if($key == 1 || $key == 5) {
                    $cellData[] = trim($cell->nodeValue);
                }
            }
            $cellData[1] = explode(' ', $cellData[1])[0];
            
            if(trim($cellData[1]) == 'проведен') {
                $items[] = $cellData[0];
            }
        }
        return $items;
    }

    public function kaspiFilesProcess()
    {   
        $directory = env('1C_KASPI_IMPORT_FOLDER');
        $scanned_directory = array_diff(scandir($directory, 1), array('..', '.'));
        $scanned_file = end($scanned_directory);
        $fileName = $directory . '/' . $scanned_file;
        if (is_dir($fileName)) {
            return "There is no files in folder";
        }
        if (!$this->mapFileToDB($fileName)) {
            return 'Mapping file error';
        }
        
        $result = $this->kaspiFileUpload($fileName);
        if ($result) {
            $refund = Refund::where('id', $this->orderId)->first();
            $refund->status = Refund::STATUS_BANK_PROCESSING;
            $refund->save();

            if (!file_exists($directory . '/' . self::FOLDER_FOR_USER_1C)) {
                mkdir($directory . '/' . self::FOLDER_FOR_USER_1C, 0666, true);
            }
            $file = explode('/', $fileName);
            $file = end($file);
            $usedFile = $directory . '/' . self::FOLDER_FOR_USER_1C . '/' . $file;
            rename($fileName, $usedFile);
            return "File $fileName has been used";
        }
        $refund = Refund::where('id', $this->orderId)->first();
        $refund->increment('attempts');
        $refund->save();
        return "Some error with file $fileName";

    }

    public function mapFileToDB($fileName)
    {
        $content = file_get_contents($fileName); 
        //$content = mb_convert_encoding($content, 'UTF-8', 'Windows-1251'); 
        $rows = explode("\n", $content);
        $fileData = [];
        foreach ($rows as $row) {
            $val = explode('=', $row);
            if($val[0] == 'НомерДокумента') {
                $fileData['order_number'] = (string) (int) trim($val[1]);
            } elseif($val[0] == 'ПолучательБИН_ИИН') {
                $fileData['iin'] = $val[1];
            } elseif($val[0] == 'ПолучательИИК') {
                $fileData['iban'] = substr($val[1], 2);
            }
        }
        if(empty($fileData['order_number']) || empty($fileData['iin']) || empty($fileData['iban'])) {
            $this->error('cant find some fields in file');
            print_r($fileData);
            return false;
        }
        $profile = Profiles::where('iin', trim($fileData['iin']))->first();
        if(!isset($profile)) {
            $this->error('can not find user profile where IIN is ' . $fileData['iin']);
            return false;
        }
        $refund = Refund::where('user_id', $profile->user_id)
            ->where('user_iban', trim($fileData['iban']) )
            ->where('status', Refund::STATUS_PROCESSING)
            ->where(function ($query) use ($fileData) {
                $query->whereNull('order_number')
                    ->orWhere('order_number', $fileData['order_number']);
            })
            ->first();

        if ( time() < Refund::attemptDelay($refund->attempts, $refund->updated_at) ) {
            $this->orderId = $refund->id;
            $this->error('Order is pending for '. Refund::attemptDelayMins($refund->attempts) .' minuts, till ' . date('Y-m-d H:i:s', Refund::attemptDelay($refund->attempts, $refund->updated_at)) );
            return false;
        }

        if(empty($refund)) {
            $this->error('cant find refund record in DB where iban ' . trim($fileData['iban']) );
            return false;
        }
        $refund->order_number = $fileData['order_number'];
        $refund->save();
        
        $this->orderId = $refund->id;

        return true;
    }

    public function kaspiAuth()
    {
        ////////////////////////////// auth ////////////////////////////////////////////
        $ch = curl_init($this->baseUrl.'api/auth/sign-in');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1'));
        $postData = "Login=".base64_decode(env('1C_KASPI_IMPORT_LOGIN'))."&Password=".base64_decode(env('1C_KASPI_IMPORT_PASSWORD'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $result = curl_exec($ch); 
        $cookies = $this->getCurlCookie($result);
        $this->sessionID = $cookies['ASP_NET_SessionId'];
      
        $data = preg_split('/\n|\r\n?/', $result);
        $data = json_decode(end($data));
        $profileId = $data->data->profiles[0]->profileId;
        $this->line('profile id: '. $profileId);
        
        //////////////////////////// getting Cookie .kaspi_biz_auth //////////////////////////
        $ch = curl_init($this->baseUrl.'api/auth/choose-organization');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1;ASP.NET_SessionId=" . $this->sessionID);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "ProfileId=$profileId");
        $result = curl_exec($ch);
        $cookies = $this->getCurlCookie($result);
        $this->kaspiBizAuth = $cookies['_kaspi_biz_auth'];
        $this->line('kaspiBizAuth: '. $this->kaspiBizAuth);
    }

    public function kaspiFileUpload($fileName = null)
    {

        ///////////////////////////////////// import page to get form input ///////////////////////////
        $ch = curl_init($this->baseUrl.'payment/import');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1;ASP.NET_SessionId=" . $this->sessionID . ";.kaspi_biz_auth=" . $this->kaspiBizAuth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1'));
        $result = curl_exec($ch);
        $inputs = $this->getCurlInput($result);
        
        //////////////////////////// Upload file /////////////////////////
        $ch = curl_init($this->baseUrl.'payment/import');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1;ASP.NET_SessionId=" . $this->sessionID . ";.kaspi_biz_auth=" . $this->kaspiBizAuth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1'));
        curl_setopt($ch, CURLOPT_POST, 1);
        $postData = [
            '__VIEWSTATE' => $inputs['__VIEWSTATE'],
            '__EVENTVALIDATION' => $inputs['__EVENTVALIDATION'],
            'ctl07$BtUpload' => '',
            'ctl07$ctl00' => curl_file_create($fileName, 'text/plain')
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        
        $result = curl_exec($ch);
        print_r($result); print "/n";

        ///////////////////////////////////// import page to get form input ///////////////////////////
        $ch = curl_init($this->baseUrl.'payment/import');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1;ASP.NET_SessionId=" . $this->sessionID . ";.kaspi_biz_auth=" . $this->kaspiBizAuth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1'));
        $result = curl_exec($ch);
        $inputs = $this->getCurlInput($result);
        print_r($inputs); print "/n";

        //////////////////////////// Approve imported file /////////////////////////
        $ch = curl_init($this->baseUrl.'payment/import');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1;ASP.NET_SessionId=" . $this->sessionID . ";.kaspi_biz_auth=" . $this->kaspiBizAuth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1'));
        curl_setopt($ch, CURLOPT_POST, 1);
        $postData = [
            '__VIEWSTATE' => $inputs['__VIEWSTATE'],
            '__EVENTVALIDATION' => $inputs['__EVENTVALIDATION'],
            'ctl07$btnSave' => 'Подтвердить'
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($ch);

        $reditect = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        print_r($reditect);
        if (!empty($reditect) and $reditect == $this->baseUrl.'payment/confirm') {
            return true;
        }
        return false;
    }

    public function kaspiTransactionProcess()
    {
        $statusWaitingList = Refund::where('status', Refund::STATUS_BANK_PROCESSING)->get();
/*
        ///////////////////////////////////// history page ///////////////////////////
        $ch = curl_init($this->baseUrl.'payment/history');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=" . $this->sessionID . ";.kaspi_biz_auth=" . $this->kaspiBizAuth);
        $result = curl_exec($ch);
        $list = $this->getHistoryPaymentDataList($result);
        
        //$statusWaitingList = Refund::where('status', Refund::STATUS_BANK_PROCESSING)->get();
        forEach($statusWaitingList as $statusWaiting) {
            if( in_array($statusWaiting->order_number, $list) ) {
                $statusWaiting->status = Refund::STATUS_SUCCESS;
                $statusWaiting->save();
            }
        }
*/

        $transactions = $this->getTransactionList();
        
        foreach ($transactions as $transaction) {
            //parsing sting // Возврат суммы: №1704 от 19/11/2019, н/п ИИН получателя не соответствовал ИИН владельца счета получателя. За прочие услуги. Возврат излишне оплаченной суммы от клиента Цай Владимир Викторович Сумма 10-00 тенге в т.ч. НДС(Без НДС) 0-00 тенге. ИИН получателя не соответствует ИИН владельца счета получателя, будет использован счет невыясненных сумм
            $exploded = explode('Возврат суммы: №', $transaction->purpose);
            if(isset($exploded[1])) {
                $orderNumber = explode(' от ', $exploded[1])[0]; 
                forEach($statusWaitingList as $statusWaiting) {
                    if($orderNumber == $statusWaiting->order_number) {
                        //$comment = explode('НДС(Без НДС) 0-00 тенге.', $transaction->purpose)[1];
                        $comment = 'Recepient IIN does not correspond to Account holder IIN. Funds will be returned to student account on the next business day';
                        $statusWaiting->status = Refund::STATUS_RETURNED;
                        $statusWaiting->bank_comment = $comment;
                        $statusWaiting->save();
                        $this->info('order ' . $orderNumber . ' has been returned');
                        $user = User::where('id', $statusWaiting->user_id)->first();

                        /*
                        Mail::send('emails.refund_bank_reject', [
                            'statusWaiting' => $statusWaiting,
                            'user'          => $user
                            ],
                            function ($message) use ($statusWaiting, $user) {
                                $message->from(getcong('site_email'), getcong('site_name'));
                                $message->to( explode(',',env('EMAIL_BUH_REFUND_INFORM')) )
                                    ->subject('Банк не принял заявку на возврат '. $statusWaiting->order_number);
                        });
                        */
                        Mail::to( explode(',',env('EMAIL_BUH_REFUND_INFORM')) )
                            ->queue(new RefundBankReject($user, $statusWaiting));
                    }
                }
            }
        //parsing string Принятые платежи за 20.12.2019 ком. 0.00 тг. Сумма: 10000.00 Оплата за обучение студент Салахутдинов Руслан Андреевич, ИИН 980916300786, факультет ФК, курс 3,
            $exploded = explode(' Оплата за обучение студент ', $transaction->purpose);
            if(isset($exploded[1])) {
                $kaspiTransaction = $transaction->tranNumber;
                preg_match('/Сумма: ([0-9.]+) Оплата/', $transaction->purpose, $amount);
                $amount = (int) ($amount[1]*100);
                $exploded = explode(', ИИН ', $exploded[1]);
                $fio = $exploded[0];
                $exploded = explode(', факультет', $exploded[1]);
                $iin = $exploded[0];

                $exist = DB::table('kaspi_refills')
                            ->where('kaspi_transaction', $kaspiTransaction)
                            ->exists();
                if (!$exist) {
	                sleep(1);
                    $orderData = [
                        'kaspi_transaction' => $kaspiTransaction,
                        'fio' => $fio,
                        'iin' => $iin,
                        'amount' => $amount,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // 1c
                    $params1c = [
                        "bank"  => "KASPI",
                        "iin"   => $orderData['iin'],
                        "summa"  => strval($orderData['amount']/100),
                        "date"  => Carbon::now('Asia/Almaty')->format('d.m.Y')
                    ];
                    if (!env('API_1C_ENABLED', false) || env('API_1C_EMULATED', false)) {
                        $this->info('service1c emulation');

                        $this->info('New refill ' . $kaspiTransaction);
                        Log::info('New refill', $orderData);
                        DB::table('kaspi_refills')->insert($orderData);

                        $refillResult = true;
                    } else {
                        $attemptTo1c = 0;
                        while(true) {
                            $attemptTo1c++;

                            // attempts ended
                            if( $attemptTo1c > self::MAX_ATTEMPTS_1C_REQUEST ) {
                                Mail::send('emails.refill_1c_error', [
                                    'orderData' => $orderData
                                    ],
                                    function ($message) use ($orderData) {
                                        $message->from(getcong('site_email'), getcong('site_name'));
                                        $message->to( explode(',',env('EMAIL_BUH_REFUND_INFORM')) )
                                            ->subject('Ошибка пополнения 1с '. $orderData['kaspi_transaction']);
                                });
                                break;
                            }

                            // preventing duplicate refills
                            $exist = DB::table('kaspi_refills')
                                        ->where('kaspi_transaction', $kaspiTransaction)
                                        ->exists();
                            if ($exist) {
                                break;
                            }
                            $refillResult = Service1C::sendRequest(Service1C::API_REFILL, $params1c);

                            if ( 
                                 !$refillResult || 
                                 (isset($refillResult['Ошибка']) && $refillResult['Ошибка'] != '') || 
                                 isset($refillResult['error'])
                            ) {
                                $this->info('Refill error, transaction # ' . $kaspiTransaction);
                                Log::info('Refill error, transaction # ' . $kaspiTransaction);

                                sleep(self::PAUSE_BETWEN_ATTEMPTS_1C_REQUEST);
                            } else {
                                // success refill reporting
                                $this->info('New refill ' . $kaspiTransaction);
                                Log::info('New refill', $orderData);
                                DB::table('kaspi_refills')->insert($orderData);
                                break;
                            }


                            
                        }
                    }

                }

            }

        }
                
    }

    private function getTransactionList()
    {
        ////////////////////////////check return status /////////////////////////////////////
        $lastTransactionId = null;
        $allTransactions = [];
        for ($i = 1; $i <= $this->option('transactions-pages'); $i++) {
            $ch = curl_init($this->baseUrl.'api/statement/account');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIE, "XSRF-TOKEN=1;ASP.NET_SessionId=" . $this->sessionID . ";.kaspi_biz_auth=" . $this->kaspiBizAuth);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "period=year&TransactionType=C&accountId=57241&LastTransactionId=$lastTransactionId");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('XSRF-TOKEN: 1', 'Content-Type: application/x-www-form-urlencoded'));
            $result = curl_exec($ch);
            $transactions = json_decode($result)->transactions;
            if( end($transactions) !== null && isset(end($transactions)->tranId) ) {
                $lastTransactionId = end($transactions)->tranId;
            } else {
                break;
            }
            $allTransactions = array_merge($allTransactions, $transactions);

        }
        return $allTransactions;
    }
}
