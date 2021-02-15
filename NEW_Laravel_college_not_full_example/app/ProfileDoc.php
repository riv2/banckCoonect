<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\Auth;
use Illuminate\Support\Facades\Log;
use Image;
use File;
use App\ProfileDocsType;

class ProfileDoc extends Model
{
    protected $table = 'profile_docs';

    const STATUS_MODERATION = 'moderation';
    const STATUS_ALLOW      = 'allow';
    const STATUS_DISALLOW   = 'disallow';

    const BASE_PATH = 'images/uploads/';
    const EXT = '-b.jpg';

    const TYPE_R086                             = 'r086_photo';                       // Медицинская справка 086
    const TYPE_R086_BACK                        = 'r086_photo_back';                  
    const TYPE_R063                             = 'r063_photo';                       // Медицинская справка 063
    const TYPE_RESIDENCE_REGISTRATION           = 'residence_registration_photo';
    const TYPE_MILITARY                         = 'military_photo';
    const TYPE_DIPLOMA                          = 'diploma_photo';                    // Диплом (Оригинал) 
    const TYPE_ATTEDUCATION                     = 'atteducation_photo';               // Аттестат (Оригинал) 
    const TYPE_ATTEDUCATION_BACK                = 'atteducation_photo_back';
    const TYPE_NOSTRIFICATION                   = 'nostrificationattach_photo';       // Нострификация
    const TYPE_NOSTRIFICATION_BACK              = 'nostrificationattach_back_photo';
    const TYPE_WORK_BOOK                        = 'work_book_photo';                  // Копия трудовой книжки
    const TYPE_ENG_CERTIFICATE                  = 'eng_certificate_photo';            // Сертификат по английскому языку 
    const TYPE_FRONT_ID                         = 'front_id_photo';                   // Копия удостоверения личности передняя
    const TYPE_BACK_ID                          = 'back_id_photo';                    // Копия удостоверения личности задняя
    const TYPE_APPLY_APPLICATION                = 'apply_application';                // Заявление на поступление
    const TYPE_CONTRACT                         = 'contract';
    const TYPE_DISCOUNT_PROOF                   = 'discount_proof';
    const TYPE_ENT_CERTIFICATE                  = 'ent_certificate';                  // Сертификат ЕНТ
    const TYPE_EDUCATION_CONTRACT               = 'education_contract';
    const TYPE_EDUCATION_STATEMENT              = 'education_statement';
    const TYPE_KT_CERTIFICATE                   = 'kt_certificate';                   // Сертификат КТ
    const TYPE_CON_CONFIRM                      = 'con_confirm';
    const TYPE_TEACHER_MIRAS_ADDRESS_CARD       = 'teacher_miras_address_card';
    const TYPE_TEACHER_MIRAS_RESUME             = 'teacher_miras_resume';
    const TYPE_TEACHER_MIRAS_CERTIFICATE        = 'teacher_miras_certificate';
    const TYPE_TEACHER_MIRAS_LANG_CERTIFICATE   = 'teacher_miras_lang_certificate';
    const TYPE_OTHER   = 'other';


    public static $needUploadDocsList = [
        self::TYPE_R086,
        self::TYPE_R086_BACK,
        self::TYPE_R063,
        self::TYPE_DIPLOMA,
        self::TYPE_ATTEDUCATION,
        self::TYPE_ATTEDUCATION_BACK,
        self::TYPE_FRONT_ID,
        self::TYPE_BACK_ID,
        self::TYPE_CONTRACT,
        self::TYPE_EDUCATION_STATEMENT,
    ];


    /**
     *  require php artisan db:seed --class=ProfileDocsTypeFill
     */

    public function getPathForDoc($docType = null, $fileName = false)
    {
        $docType = !$docType ? $this->type : $docType;

        $abc = '';
        if ($fileName) $abc = substr(trim($fileName), 0,1) . '/';

        $return = self::BASE_PATH;

        if (isset($docType)) {
            $type = ProfileDocsType::where('type', $docType)->first();
            if(!empty($type->folder)) {
                $return .= $type->folder;
            } else {
                $return .= $type->type ?? '';
            }
            $return .= '/';
        }

        if ($fileName) $this->checkAndCreateAbcFolder($return, $abc);

        $return .= $abc;    

        return $return;
    }

    /**
     * @return string
     */
    public function getPublicFileName()
    {
        $abc = substr(trim($this->filename), 0,1) . '/';

        return '/' . $this->getPathForDoc($this->doc_type) . $abc . $this->filename;
    }

    public static function saveDocument($docType, $file, $side = '', $bUpdateLast = true , $id = null)
    {
        if ($id === null) {
            $id = Auth::user()->id;
        }

        $profileDoc = new ProfileDoc;

        $fileName =  strtolower(str_random(5)) . '-' . time() . '-' . $side;
        $tmpFilePath = $profileDoc->getPathForDoc($docType, $fileName);

        $aDocFileExtention = explode('.',$file->getClientOriginalName());
        $aDocFileExtention = end($aDocFileExtention);


        if( $docType == self::TYPE_FRONT_ID || $docType == self::TYPE_BACK_ID ) {
            $file->move(public_path($tmpFilePath), $fileName);
            if( \File::mimeType( public_path($tmpFilePath.$fileName) ) == 'application/octet-stream' ) {
                shell_exec('tifig  -v -p '.$file->getPathName().' ' .public_path($tmpFilePath).$fileName.self::EXT);
            } else {
                rename(public_path($tmpFilePath).$fileName, public_path($tmpFilePath).$fileName.self::EXT);
                //$img = Image::make(public_path($tmpFilePath).$fileName.self::EXT);
                //$img->fit(2500)->save(public_path($tmpFilePath).$fileName.self::EXT, 80);

            }
            $profileDoc->addNewDoc($docType, $fileName, true, $id );

        } elseif( $docType == self::TYPE_TEACHER_MIRAS_RESUME ){

            $sFullFileName = $fileName . '.' . $aDocFileExtention;

            $file->move(public_path('uploads/teacher_miras_resume'), $sFullFileName);
            if( $bUpdateLast )
            {
                $profileDoc->addNewDoc($docType, $sFullFileName, true, $id );
            } else {
                $profileDoc->addNewDoc($docType, $sFullFileName, false, $id );
            }

        } elseif( in_array($aDocFileExtention, ['JPG','JPEG','GIF','BMP','PNG','TIFF','jpg','jpeg','gif','bmp','png','tiff'] ) === false ){

            // if not image
            $sFullFileName = $fileName . '.' . $aDocFileExtention;

            $file->move(public_path($tmpFilePath), $sFullFileName);
            if( $bUpdateLast )
            {
                $profileDoc->addNewDoc($docType, $sFullFileName, true, $id );
            } else {
                $profileDoc->addNewDoc($docType, $sFullFileName,false, $id );
            }

        } else {
            if( is_array($file) && count($file) > 1 ) {
                $updateLast = true;
                foreach ($file as $SingleFile) {

                    $profileDoc = new ProfileDoc;

                    $img = Image::make($SingleFile);
                    $img->resize(1350, 1350, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->save($tmpFilePath.$fileName.self::EXT, 75);

                    if( $bUpdateLast ) {
                        $profileDoc->addNewDoc($docType, $fileName, $updateLast, $id );
                    } else {
                        $profileDoc->addNewDoc($docType, $fileName, false, $id );
                    }
                    $updateLast = false;

                    $fileName =  strtolower(str_random(5)) . '-' . time() . '-' . $side;
                    $tmpFilePath = $profileDoc->getPathForDoc($docType, $fileName);
                }
            } else {
                //Log::info('Upload file', ['user_id' => Auth::user()->id, 'file' => (array)$file]);
                $storageFname = str_random(18) . '.jpg';
                $file->move(storage_path('images'), $storageFname);
                $img = Image::make(storage_path('images/' . $storageFname));
                $img->resize(1350, 1350, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($tmpFilePath.$fileName.self::EXT, 75);
                //$img->fit(450, 321)->save($tmpFilePath.$fileName. '-s.jpg', 75);
                if( $bUpdateLast ) {
                    $profileDoc->addNewDoc($docType, $fileName, true, $id );
                } else {
                    $profileDoc->addNewDoc($docType, $fileName, false, $id );
                }
                unlink( storage_path('images/' . $storageFname) );
            }
            
        }

        $profileDoc->save();
    }


    public function addNewDoc($fileType, $fileName, $updateLast = true, $id)
    {
        if ($id === null) {
            $id = Auth::user()->id;
        }
        $manual = DB::table('profile_docs_type')
                    ->select('manual_old_mark')
                    ->where('type', $fileType)
                    ->first()->manual_old_mark;

        if( $updateLast && !$manual ) {
        	DB::table($this->table)
                ->where([
                	'user_id' => $id,
                	'doc_type' => $fileType
                ])
                ->update([
                    'last' => 0
                ]);
        }

        $this->user_id = $id;
    	$this->doc_type = $fileType;
        $this->filename = $fileName;
        $this->last = 1;
    }

    public function checkAndCreateAbcFolder($path, $abc)
    {
        if(!File::isDirectory( public_path($path.$abc) )) {
            File::makeDirectory( public_path($path.$abc), 0755, true, true);
        }

    }

    public static function getUserDocsList($id = null)
    {
        if ($id === null) {
            $id = Auth::user()->id;
        }
        $listDb = ProfileDoc
            ::where('user_id', $id)
            ->where('last', true)
            ->orderBy('id','DESC')
            ->get();
        $list = [];
        foreach ($listDb as $item) {
            $doc = new ProfileDoc;
            $item->filepath = '/' . $doc->getPathForDoc($item->doc_type, $item->filename) . $item->filename . self::EXT;

            $docItem['filepath'] = url($item->filepath);
            $docItem['doc_type'] = $item->doc_type;
            $docItem['id'] = $item->id;
            $docItem['status'] = __($item->status);
            $docItem['statusval'] = $item->status;
            $docItem['delivered'] = $item->delivered;
            $docItem['doc_name'] = __($item->doc_type);


            $list[] = $docItem;
        }

        return $list;

    }

}
