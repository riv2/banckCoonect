<?php

namespace App\Services;

use App\Discipline;
use App\EmployeesDepartment;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings as PhpWordSettings;
use PhpOffice\PhpWord\Element\TextRun;
use Illuminate\Support\Facades\{Response, Storage};

class SyllabusDetailDoc
{
    static public function create($disciplineID, $language) {
        $syllabuses = [];
        $literature = [];
        $theoreticalMaterials = [];
        $practicalMaterials = [];
        $sroMaterials = [];
        $sropMaterials = [];
        $questions = [];

        $translate = [
            'ru' => [
                'lecture' => 'Лекция',
                'description' => 'Описание',
                'practice_doc' => 'Практический материал',
            ],
            'kz' => [
                'lecture' => 'Дәріс',
                'description' => 'Сипаттама',
                'practice_doc' => 'Практикалық материал',
            ],
            'en' => [
                'lecture' => 'Links',
                'description' => 'Overview',
                'practice_doc' => 'Exercise/Assignment/Case',
            ],
        ];

        $controlForms = [
            'test' => 'Тест',
            'traditional' => 'Традиционный',
            'report' => 'Отчет',
            'score' => 'Диф. зачет',
            'protect' => 'Защита',
        ];

        $discipline = Discipline::where('id', $disciplineID)->first();

        $syllabusesCollection = $discipline->syllabuses->where('language', $language)->sortBy('module_id')->all();

        $key = 0;
        $discipline_contact_hours = 0;
        $discipline_self_hours = 0;
        $discipline_self_teacher_hours = 0;
        $discipline_sro_hours = 0;
        $discipline_srop_hours = 0;

        foreach ($syllabusesCollection as $syllabus) {
            $syllabuses[] = [
                'module_name' => $syllabus->module->name ?? 'Без модуля',
                'theme_id' => $key + 1,
                'theme_name' => $syllabus->theme_name,
                'theme_contact_hours' => $syllabus->contact_hours,
                'theme_self_hours' => $syllabus->self_hours,
                'theme_self_teacher_hours' => $syllabus->self_with_teacher_hours,
                'theme_srop_hours' => $syllabus->srop_hours,
                'theme_sro_hours' => $syllabus->sro_hours,
                'theoretical_description' => $syllabus->teoretical_description,
                'practical_description' => $syllabus->practical_description,
                'sro_description' => $syllabus->sro_description,
                'srop_description' => $syllabus->srop_description,
            ];

            $discipline_contact_hours += $syllabus->contact_hours;
            $discipline_self_hours += $syllabus->self_hours;
            $discipline_self_teacher_hours += $syllabus->self_with_teacher_hours;
            $discipline_srop_hours += $syllabus->srop_hours;
            $discipline_sro_hours += $syllabus->sro_hours;

            foreach ($syllabus->literature as $syllabus_literature) {
                $literature_data = [
                    $syllabus_literature->name,
                    $syllabus_literature->author,
                    $syllabus_literature->more_authors
                ];

                $literature[$key + 1][] = implode(', ', $literature_data);
            }

            foreach ($syllabus->teoreticalMaterials as $theoreticalMaterial) {
                $theoreticalMaterials[$key + 1][] = [
                    'link' => $theoreticalMaterial->getPublicUrl(),
                    'description' => $theoreticalMaterial->link_description,
                ];
            }

            foreach ($syllabus->practicalMaterials as $practicalMaterial) {
                $practicalMaterials[$key + 1][] = [
                    'link' => $practicalMaterial->getPublicUrl(),
                    'description' => $practicalMaterial->link_description,
                ];
            }

            foreach ($syllabus->sroMaterials as $sroMaterial) {
                $sroMaterials[$key + 1][] = [
                    'link' => $sroMaterial->getPublicUrl(),
                    'description' => $sroMaterial->link_description,
                ];
            }

            foreach ($syllabus->sropMaterials as $sropMaterial) {
                $sropMaterials[$key + 1][] = [
                    'link' => $sropMaterial->getPublicUrl(),
                    'description' => $sropMaterial->link_description,
                ];
            }

            foreach ($syllabus->quizeQuestions as $question_key => $question) {
                $questions[$key + 1][] = [
                    'question' => ($question_key + 1) . '. ' . strip_tags($question->question, '<img>'),
                ];
            }

            $key++;
        }

        $params = [
            'd_name' => $discipline->{($language == 'ru') ? 'name' : 'name_' . $language},
            'd_control_form' => empty($discipline->control_form) ? '' : $controlForms[$discipline->control_form],
            'd_ects' => $discipline->ects,
            'sector_name' => $discipline->sector->name ?? '',
            'd_contact_hours' => $discipline_contact_hours,
            'd_self_hours' => $discipline_self_hours,
            'd_self_teacher_hours' => $discipline_self_teacher_hours,
            'd_sro_hours' => $discipline_sro_hours,
            'd_srop_hours' => $discipline_srop_hours,
            'd_total_hours' => (
                $discipline_contact_hours +
                $discipline_self_hours +
                $discipline_self_teacher_hours +
                $discipline_sro_hours +
                $discipline_srop_hours
            ),
        ];

        $disciplineDetailsTemplate = new TemplateProcessor(resource_path('docx/detail_syllabus_template_' . $language . '.docx'));
        PhpWordSettings::setOutputEscapingEnabled(true);

        foreach ($params as $key => $value) {
            $disciplineDetailsTemplate->setValue($key, $value);
        }

        $disciplineDetailsTemplate->cloneRowAndSetValues('theme_id', $syllabuses);

        /*
         * Remove variable for empty row value
         **/
        foreach ($syllabuses as $syllabus) {
            if (empty($literature[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_literature#' . $syllabus['theme_id'], '');
            }
            if (empty($theoreticalMaterials[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_theoretical#' . $syllabus['theme_id'], '');
            }
            if (empty($practicalMaterials[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_practical#' . $syllabus['theme_id'], '');
            }
            if (empty($sroMaterials[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_sro#' . $syllabus['theme_id'], '');
            }
            if (empty($sropMaterials[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_srop#' . $syllabus['theme_id'], '');
            }
            if (empty($questions[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_questions#' . $syllabus['theme_id'], '');
            }
            if (empty($answers[$syllabus['theme_id']])) {
                $disciplineDetailsTemplate->setValue('theme_answers#' . $syllabus['theme_id'], '');
            }
        }

        if (!empty($literature)) {
            foreach ($literature as $theme_id => $docs) {
                $text = new TextRun();

                foreach ($docs as $doc) {
                    $text->addText($doc);
                    $text->addTextBreak();
                }

                $disciplineDetailsTemplate->setComplexValue('theme_literature#' . $theme_id, $text);
            }
        }

        if (!empty($theoreticalMaterials)) {
            foreach ($theoreticalMaterials as $theme_id => $docs) {
                $text = new TextRun();

                foreach ($docs as $doc) {
                    $text->addText(($translate[$language]['lecture'] ?? '') . ': ' . $doc['link']);
                    $text->addTextBreak();
                    $text->addText(($translate[$language]['description'] ?? '') . ': ' . $doc['description']);
                    $text->addTextBreak();
                }

                $disciplineDetailsTemplate->setComplexValue('theme_theoretical#' . $theme_id, $text);
            }
        }

        if (!empty($practicalMaterials)) {
            foreach ($practicalMaterials as $theme_id => $docs) {
                $text = new TextRun();

                foreach ($docs as $doc) {
                    $text->addText(($translate[$language]['practice_doc'] ?? '') . ': ' . $doc['link']);
                    $text->addTextBreak();
                    $text->addText(($translate[$language]['description'] ?? '') . ': ' . $doc['description']);
                    $text->addTextBreak();
                }

                $disciplineDetailsTemplate->setComplexValue('theme_practical#' . $theme_id, $text);
            }
        }

        if (!empty($sroMaterials)) {
            foreach ($sroMaterials as $theme_id => $docs) {
                $text = new TextRun();

                foreach ($docs as $doc) {
                    $text->addText($doc['link']);
                    $text->addTextBreak();
                    $text->addText(($translate[$language]['description'] ?? '') . ': ' . $doc['description']);
                    $text->addTextBreak();
                }

                $disciplineDetailsTemplate->setComplexValue('theme_sro#' . $theme_id, $text);
            }
        }

        if (!empty($sropMaterials)) {
            foreach ($sropMaterials as $theme_id => $docs) {
                $text = new TextRun();

                foreach ($docs as $doc) {
                    $text->addText($doc['link']);
                    $text->addTextBreak();
                    $text->addText(($translate[$language]['description'] ?? '') . ': ' . $doc['description']);
                    $text->addTextBreak();
                }

                $disciplineDetailsTemplate->setComplexValue('theme_srop#' . $theme_id, $text);
            }
        }

        if (!empty($questions)) {
            $images = [];

            foreach ($questions as $theme_id => $docs) {
                $text = new TextRun();

                foreach ($docs as $doc) {
                    $question = $doc['question'];
                    $docImages = null;

                    preg_match_all('/<img.*src="([\S\s]+?)".*>/', $question, $docImages);

                    if (!empty($docImages[1])) {
                        foreach ($docImages[1] as $key => $docImage) {
                            $imageKey = 'image_' . (count($images) + 1);
                            $imageName = str_random(20);

                            $images[$imageKey] = $imageName;
                            $imageData = explode( ',', $docImage);

                            $question = str_replace($docImages[0][$key], '${' . $imageKey . '}', $question);

                            Storage::disk('local')->put('images/' . $imageName, base64_decode($imageData[1]));
                        }
                    }

                    $text->addText($question);
                    $text->addTextBreak();
                }

                $disciplineDetailsTemplate->setComplexValue('theme_questions#' . $theme_id, $text);
            }

            foreach ($images as $key => $image) {
                $disciplineDetailsTemplate->setImageValue($key, Storage::path('images/' . $image));

                Storage::delete('images/' . $image);
            }
        }

        $file = storage_path('docxReplace/' . str_random(20) . '.docx');
        $disciplineDetailsTemplate->saveAs($file);

        return Response::download($file, 'Syllabus.docx')->deleteFileAfterSend(true);
    }
}
