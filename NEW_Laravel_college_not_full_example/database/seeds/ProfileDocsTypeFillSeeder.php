<?php

use Illuminate\Database\Seeder;

class ProfileDocsTypeFill extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataList = [
            ['type' => 'r086_photo', 'folder' => 'r086', 'manual_old_mark' => true],
            ['type' => 'r086_photo_back', 'folder' => 'r086', 'hidden' => true],
            ['type' => 'r063_photo', 'folder' => 'r063'],
            ['type' => 'residence_registration_photo', 'folder' => 'residence', 'hidden' => true],
            ['type' => 'military_photo', 'folder' => 'military', 'hidden' => true],
            ['type' => 'diploma_photo', 'folder' => 'atteducation/diploma', 'manual_old_mark' => true],
            ['type' => 'atteducation_photo', 'folder' => 'atteducation', 'manual_old_mark' => true],
            ['type' => 'atteducation_photo_back', 'folder' => 'atteducation', 'hidden' => true],
            ['type' => 'nostrificationattach_photo', 'folder' => 'nostrificationattach'],
            ['type' => 'nostrificationattach_back_photo', 'folder' => 'nostrificationattach', 'hidden' => true],
            ['type' => 'work_book_photo', 'folder' => 'work_book'],
            ['type' => 'eng_certificate_photo', 'folder' => 'eng_certificate'],
            ['type' => 'front_id_photo', 'folder' => 'frontid'],
            ['type' => 'back_id_photo', 'folder' => 'backid'],
            ['type' => 'apply_application', 'folder' => 'apply_application'],
            ['type' => 'contract', 'folder' => 'contract', 'hidden' => true],
            ['type' => 'discount_proof', 'manual_old_mark' => true, 'hidden' => true],
            ['type' => 'ent_certificate', 'folder' => 'certificates/ent'],
            ['type' => 'education_contract', 'manual_old_mark' => true],
            ['type' => 'education_statement', 'folder' => 'education_statement', 'hidden' => true],
            ['type' => 'kt_certificate', 'folder' => 'certificates/kt'],
            ['type' => 'con_confirm', 'folder' => 'con_confirm', 'hidden' => true],
            ['type' => 'teacher_miras_address_card', 'folder' => 'teacher_miras_address_card', 'hidden' => true],
            ['type' => 'teacher_miras_resume', 'folder' => 'teacher_miras_resume', 'hidden' => true],
            ['type' => 'teacher_miras_certificate', 'folder' => 'teacher_miras_certificate', 'hidden' => true],
            ['type' => 'teacher_miras_lang_certificate', 'folder' => 'teacher_miras_lang_certificate', 'hidden' => true],

            ['type' => 'inventory', 'manual_old_mark' => true],
            ['type' => 'entrance_protocol'],
            ['type' => 'creative_exam_ectract'],
            ['type' => 'transcript'],
            ['type' => 'exam_list'],
            ['type' => 'university_license'],
            ['type' => 'passport_translate'],
            ['type' => 'diplom_translate'],
            ['type' => 'cv'],
            ['type' => 'scientific_papers_list'],
            ['type' => 'elimination_pre_requisites'],
            ['type' => 'marriage'],
            ['type' => 'child_birth'],
            ['type' => 'other', 'manual_old_mark' => true],

            ['type' => 'criminal_record'],
            ['type' => 'work_place'],
            ['type' => 'military_call'],
            ['type' => 'vkk'],
            ['type' => 'address_reference'],
            ['type' => 'student_photo'],
            ['type' => 'student_passport'],
            ['type' => 'diplom_add'],
            ['type' => 'atteducation_add'],
            ['type' => 'diplom_copy'],
            ['type' => 'diplom_add_copy'],
            ['type' => 'atteducation_copy'],
            ['type' => 'atteducation_app_copy'],
            
            ['type' => 'recover_app'],
            ['type' => 'recover_another_un_app'],
            ['type' => 'unlink_app'],
            ['type' => 'int_transfer_app'],
            ['type' => 'academic_leave_app'],
            ['type' => 'academic_back_app', 'hidden' => true],
            ['type' => 'change_name_app'],
            ['type' => 'cahnge_lang_app'],
            ['type' => 'drug_reference'],
            ['type' => 'academic_reference'],
            ['type' => 'student_transfer_reference'],
            ['type' => 'student_recovery_reference'],

            ['type' => 'writing_out_miras', 'manual_old_mark' => true],
            ['type' => 'writing_out_other_univer', 'manual_old_mark' => true],

        ];

        foreach ($dataList as $row)
        {
            $model = new \App\ProfileDocsType();
            $model->fill($row);
            $model->save();
        }
    }
}





    





    

    