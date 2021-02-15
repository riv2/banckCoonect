<?php

namespace Tests\Feature;

use \Tests\Feature\TestCaseAuth;

class PlagiarismCode200Test extends TestCaseAuth
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasic()
    {
        $textsOnCheck = $this->get(route('student.plagiarism.texts.oncheck'));
        $textsOnCheck->assertStatus(200);

        $textsSuccess = $this->get(route('student.plagiarism.texts.success'));
        $textsSuccess->assertStatus(200);

        $textsShow = $this->get(route('student.plagiarism.show'));
        $textsShow->assertStatus(200);
    }
}
