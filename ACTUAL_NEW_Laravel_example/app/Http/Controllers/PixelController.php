<?php

namespace App\Http\Controllers;

use App\Helpers\PartnerProgramStorage;
use App\Jobs\LogPixelOrderProcess;
use App\Jobs\PixelLogProcessJob;
use App\Models\LogPixelClick;
use App\Models\LogPixelFraud;
use App\Models\LogPixelOrder;
use App\Models\PixelLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadesResponse;

class PixelController extends Controller
{
    public function cloud(Request $request)
    {
        $response = response()->noContent();

        $pp = PartnerProgramStorage::getPP();
        if (!$pp) {
            $response->header('x-pixel-error', 'no-pp-domain');

            return $response;
        }

        try {
            $pixel_log = new PixelLog();
            $pixel_log->pp_id = (!empty($pp) && $pp->id) ? $pp->id : -1;
            $pixel_log->data = json_decode($request->getContent(), true);
            $pixel_log->ip = $request->ip();
            $pixel_log->save();
        } catch (\Throwable $th) {
            $pixel_log = new PixelLog();
            $pixel_log->pp_id = (!empty($pp) && $pp->id) ? $pp->id : -1;
            $pixel_log->data = $request->all();
            $pixel_log->ip = $request->ip();
            $pixel_log->save();
        }

        PixelLogProcessJob::dispatch($pixel_log)->onQueue('orders');

        $response->header('x-pixel-id', $pixel_log->id);
        return $response;
    }

    /**
     * Обработка кликов
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function clickpixel(Request $request)
    {
        try {
            $pixel_log = new LogPixelClick();
            $pixel_log->data = $request->except('gtmcb');
            $pixel_log->referer = $request->get('page_url');
            $pixel_log->ip = $request->ip();
            $pixel_log->is_valid = null;
            $pixel_log->status = null;
            $pixel_log->created_at = now();
            $pixel_log->save();
        } catch (\Throwable $th) {
            logger()->stack(['telegram', 'pochtabank_pixel'])->emergency('Ошибка при сохранении клика в лог', [$th->getMessage(), $request->except('gtmcb')]);
            throw $th;
        }

        return $this->responceGif();
    }

    /**
     * Обработка заказов
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cpapixel(Request $request)
    {
        try {
            $pixel_log = new LogPixelOrder();
            $pixel_log->data = $request->except('gtmcb');
            $pixel_log->referer = $request->headers->get('referer');
            $pixel_log->ip = $request->ip();
            $pixel_log->is_valid = null;
            $pixel_log->status = null;
            $pixel_log->created_at = now();
            $pixel_log->save();

            LogPixelOrderProcess::dispatch($pixel_log);
        } catch (\Throwable $th) {
            logger()->stack(['telegram', 'pochtabank_pixel'])->emergency('Ошибка при сохранении заказа в лог', [$th->getMessage(), $request->except('gtmcb')]);
            throw $th;
        }

        return $this->responceGif();
    }

    /**
     * Обработка фрода
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fraudpixel(Request $request)
    {
        try {
            $pixel_log = new LogPixelFraud();
            $pixel_log->data = $request->except('gtmcb');
            $pixel_log->referer = $request->get('page_url');
            $pixel_log->ip = $request->ip();
            $pixel_log->is_valid = null;
            $pixel_log->status = null;
            $pixel_log->created_at = now();
            $pixel_log->save();
        } catch (\Throwable $th) {
            logger()->stack(['telegram', 'pochtabank_pixel'])->emergency('Ошибка при сохранении фрода в лог', [$th->getMessage(), $request->except('gtmcb')]);
            throw $th;
        }

        return $this->responceGif();
    }

    /**
     * Undocumented function
     *
     * @return \Illuminate\Http\Response
     */
    private function responceGif(): Response
    {
        $pixel = sprintf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c', 71, 73, 70, 56, 57, 97, 1, 0, 1, 0, 128, 255, 0, 192, 192, 192, 0, 0, 0, 33, 249, 4, 1, 0, 0, 0, 0, 44, 0, 0, 0, 0, 1, 0, 1, 0, 0, 2, 2, 68, 1, 0, 59);
        $response = FacadesResponse::make($pixel, 200);
        $response->header('Content-type', 'image/gif');
        $response->header('Content-Length', 42);
        $response->header('Cache-Control', 'private, no-cache, no-cache=Set-Cookie, proxy-revalidate');
        $response->header('Expires', 'Wed, 11 Jan 2000 12:59:00 GMT');
        $response->header('Last-Modified', 'Wed, 11 Jan 2006 12:59:00 GMT');
        $response->header('Pragma', 'no-cache');

        return $response;
    }
}
