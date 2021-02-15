<?php

namespace App\Http\Middleware;

use App\NomenclatureFolder;
use App\NomenclatureFolderTemplate;
use Carbon\Carbon;
use Closure;

class NomenclatureCheckFileDate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $templates = NomenclatureFolderTemplate::get();
        foreach($templates as $template){
            if(count($template->files) == 0 && !$template['load_date']->gt(Carbon::now())){
                NomenclatureFolder::where('id', $template['folder_id'])->update([
                    'status' => 'expired_date'
                ]);
            }
        }

        return $next($request);
    }
}
