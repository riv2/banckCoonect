<?php 

namespace App\Services;

class LibrarySaveFile 
{
	public function saveFile($file){
		$fileName = time().'_'.$file->getClientOriginalName();
        $file->move(storage_path('app/library/catalog/e-book/'), $fileName);
        
        return $fileName;
	}

	public function getFile($fileName){
		$path = storage_path('app/library/catalog/e-book/').$fileName;

		return $path;
	}
}