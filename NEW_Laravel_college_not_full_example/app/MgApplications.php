<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Image;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string education
 * @property int citizenship_id
 */
class MgApplications extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;


    const EDUCATION_VOCATIONAL_EDUCATION = 'vocational_education';
    const EDUCATION_HIGH_SCHOOL = 'high_school';
    const EDUCATION_BACHELOR = 'bachelor';
    const EDUCATION_HIGHER = 'higher';

    protected $table = 'mg_applications';

    protected $fillable = [
        'nationality_id',
        'citizenship_id',
        'family_status',
        'region_id',
        'city_id',
        'country_id',
        'street',
        'building_number',
        'apartment_number',
        'ikt',
        'bceducation',
        'education',
        'numeducation',
        'sereducation',
        'nameeducation',
        'dateeducation',
        'cityeducation',
        'eduspecialization',
        'typevocational',
        'edudegree',
        'kzornot',
        'nostrification',
        'eng_certificate_number',
        'eng_certificate_series',
        'eng_certificate_date',
        'residence_registration_status',
        'r086_status',
        'r063_status',
        'atteducation_status',
        'nostrification_status',
        'eng_certificate_status',
        'work_book_status',
        'military_status',
        'kt_number',
        'kt_total',
        'kt_name_1',
        'kt_val_1',
        'kt_name_2',
        'kt_val_2',
        'kt_name_3',
        'kt_val_3',
        'kt_name_4',
        'kt_val_4',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function nationality()
    {
        return $this->hasOne('App\Nationality', 'id', 'nationality_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function citizenship()
    {
        return $this->hasOne('App\Country', 'id', 'citizenship_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function region()
    {
        return $this->hasOne('App\Region', 'id', 'region_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function familyStatus()
    {
        return $this->hasOne(FamilyStatus::class, 'id', 'family_status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publications()
    {
        return $this->hasMany(StudentPublication::class, 'mg_application_id', 'id');
    }

    /**
     * @param $value
     */
    public function setKzornotAttribute($value)
    {
        $this->attributes['kzornot'] = $value == 'true' ? true : false;
    }

    /**
     * @param $file
     * @param string $side front|back
     * @return bool
     */
    public function syncR086($file = null, $side = 'front')
    {
        if($side == 'front')
        {
            $this->r086_photo = null;
        }

        if($side == 'back')
        {
            $this->r086_photo_back = null;
        }

        if($file)
        {   
            if($side == 'front')
            {
                $docType = ProfileDoc::TYPE_R086;
            }

            if($side == 'back')
            {
                $docType = ProfileDoc::TYPE_R086_BACK;
            }
            
            ProfileDoc::saveDocument($docType, $file, $side);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncR063($file = null)
    {
        $this->r063_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_R063, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncResidenceRegistration($file = null)
    {
        $this->residence_registration_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_RESIDENCE_REGISTRATION, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncMilitary($file = null)
    {
        $this->military_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_MILITARY, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @param string $side front|back
     * @return bool
     */
    public function syncAttEducation($file = null, $side = 'front')
    {
        if($side == 'front')
        {
            $this->atteducation_photo = null;
        }

        if($side == 'back')
        {
            $this->atteducation_photo_back = null;
        }

        if($file)
        {
            if($side == 'front')
            {
                $docType = ProfileDoc::TYPE_ATTEDUCATION;
            }

            if($side == 'back')
            {
                $docType = ProfileDoc::TYPE_ATTEDUCATION_BACK;
            }
            
            ProfileDoc::saveDocument($docType, $file, $side);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncNostrificationAttach($file = null)
    {
        $this->nostrificationattach_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_NOSTRIFICATION, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncNostrificationAttachBack($file = null)
    {
        $this->nostrificationattach_back_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_NOSTRIFICATION_BACK, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncConConfirm($file = null)
    {
        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_CON_CONFIRM, $file);
        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncEngCertificate($file = null)
    {
        $this->eng_certificate_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_ENG_CERTIFICATE, $file);

        }

        return true;
    }

    /**
     * @param $file
     * @return bool
     */
    public function syncWorkBook($file = null)
    {
        $this->work_book_photo = null;

        if($file)
        {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_WORK_BOOK, $file);
        }

        return true;
    }

    /**
     * @param $publicationList
     * @return bool
     */
    public function syncPublications($publicationList)
    {
        foreach ($publicationList as $publication)
        {
            $publicationModel = new StudentPublication();
            $publicationModel->fill($publication);
            $publicationModel->syncFile($publication['file']);
            $publicationModel->mg_application_id = $this->id;
            $publicationModel->save();
        }

        return true;
    }
}
