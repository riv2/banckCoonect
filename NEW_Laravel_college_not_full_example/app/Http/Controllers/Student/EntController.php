<?php
/**
 * User: dadicc
 * Date: 3/19/20
 * Time: 10:31 AM
 */

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('student.ent.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFolders()
    {
        $folders = [];

        foreach (config('ent.folders') as $lang => $langFolders){
            $foldersAndContent = [];

            foreach ($langFolders as $folder_name => $content){
                $files = [];
                foreach ($content['videos'] as $url => $name){
                    $files[] = [
                        'viewed' => false,
                        'views' => $videoViews->views ?? 0,
                        'url' => $url
                    ];
                }
                $foldersAndContent[] = [
                    'original_name' => $folder_name,
                    'name' => $content['name'],
                    'content' => $files
                ];
            }
            $folders[$lang] = $foldersAndContent;
        }

        return response()->json($folders);
    }
}


