<?php

namespace Tests\Unit;

use App\Profiles;
use Tests\TestCase;

class ProfilesTest extends TestCase
{
    public function testGetDocs()
    {
        $this->markTestIncomplete();
    }

    public function testGetLangsArray()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocWorkBookAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testNeedMilitary()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocResidenceRegistrationAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocEntAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetAllTeam()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocBackIdAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetEducationContractAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocNostrificationAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetProfileVisitsList()
    {
        $this->markTestIncomplete();
    }

    public function testIsRedirectToRegisterAgitatorStep()
    {
        $this->markTestIncomplete();
    }

    public function testSetStudyGroupIdAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testSave()
    {
        $this->markTestIncomplete();
    }

    public function testStudyGroup()
    {
        $this->markTestIncomplete();
    }

    public function testIsTest1Time()
    {
        $this->markTestIncomplete();
    }

    public function testGetEducationContractsAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocAtteducationBackAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testStudyFormNumber()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateStatusDoc()
    {
        $this->markTestIncomplete();
    }

    public function testGetStudyFormsArrayFlat()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateSubmodules()
    {
        $this->markTestIncomplete();
    }

    public function testGetUserIdsBySpecialities()
    {
        $this->markTestIncomplete();
    }

    public function testIsSROTime()
    {
        $this->markTestIncomplete();
    }

    public function testNationalityItem()
    {
        $this->markTestIncomplete();
    }

    public function testActivity_logs()
    {
        $this->markTestIncomplete();
    }

    public function testGetEducationStatementAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocR063Attribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetLanguageByType()
    {
        $this->markTestIncomplete();
    }

    public function testStudentCheckins()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocConConfirmAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testSetNextCourse()
    {
        $this->markTestIncomplete();
    }

    public function testGetStudyFormsArray()
    {
        $this->markTestIncomplete();
    }

    public function testGetRegisterPriorityAgitator()
    {
        $this->markTestIncomplete();
    }

    public function testSetElectiveSpecialityId()
    {
        $this->markTestIncomplete();
    }

    public function testSaveProfilePhoto()
    {
        $this->markTestIncomplete();
    }

    public function testOriginalSpeciality()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateElectives()
    {
        $this->markTestIncomplete();
    }

    public function testAllowRemoteExam()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocKtAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetBackIdPhotoAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testIsExamTime()
    {
        $this->markTestIncomplete();
    }

    public function testProfileDocs()
    {
        $this->markTestIncomplete();
    }

    public function testGetNationalityRuAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetNationalityKzAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetUserIdsByGroupId()
    {
        $this->markTestIncomplete();
    }

    public function testGetSecondLanguageAttribute()
    {
        /** @var Profiles $profile */
        $profile = factory(Profiles::class)->make();

        $profile->education_lang = Profiles::EDUCATION_LANG_KZ;
        $this->assertSame(Profiles::EDUCATION_LANG_RU, $profile->second_language);

        $profile->education_lang = Profiles::EDUCATION_LANG_RU;
        $this->assertSame(Profiles::EDUCATION_LANG_KZ, $profile->second_language);

        $profile->education_lang = Profiles::EDUCATION_LANG_EN;
        $this->assertSame(Profiles::EDUCATION_LANG_KZ, $profile->second_language);
    }

    public function testGetDocR086BackAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetRedisData()
    {
        $this->markTestIncomplete();
    }

    public function testNeed063()
    {
        $this->markTestIncomplete();
    }

    public function testGetKtCertificateAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocFrontIdAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDiplomaPhotoAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testEqualSpecialities()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocR086Attribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetFrontIdPhotoAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocAtteducationAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetRegisterRoute()
    {
        $this->markTestIncomplete();
    }

    public function testGetRegisterPriority()
    {
        $this->markTestIncomplete();
    }

    public function testGetNationalityEnAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testStudentsDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testChangeEducationStatus()
    {
        $this->markTestIncomplete();
    }

    public function testGetNativeLanguageAttribute()
    {
        /** @var Profiles $profile */
        $profile = factory(Profiles::class)->make();

        $this->assertSame($profile->education_lang, $profile->native_language);
    }

    public function testGetFioByUserId()
    {
        $this->markTestIncomplete();
    }

    public function testGetDocMilitaryAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testImportFromMirasFull()
    {
        $this->markTestIncomplete();
    }

    public function testGetIsResidentAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateStatusDocContracts()
    {
        $this->markTestIncomplete();
    }

    public function testIsRedirectToRegisterStep()
    {
        $this->markTestIncomplete();
    }

    public function testGetRegisterRouteAgitator()
    {
        $this->markTestIncomplete();
    }
}
