<?php

namespace Tests\Unit;

use App\BcApplications;
use App\Profiles;
use App\SyllabusTask;

//use PHPUnit\Framework\TestCase;
use App\SyllabusTaskResult;
use App\SyllabusTaskUserPay;
use App\User;
use Tests\TestCase;

class SyllabusTaskTest extends TestCase
{

    public function testRemoveData()
    {
        $this->markTestIncomplete();
    }

    public function testGetImgDataAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testSetEventDateAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetAudioDataAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetPercent()
    {
        $this->markTestIncomplete();
    }

    public function testGetTaskData()
    {
        $this->markTestIncomplete();
    }

    public function testSetProceedButtonShowCorona()
    {
        $this->markTestIncomplete();
    }

    public function testGetTotalCorrectAnswersByTaskId()
    {
        $this->markTestIncomplete();
    }

    public function testGetEventDateAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testSetTextDataAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testSaveData()
    {
        $this->markTestIncomplete();
    }

    public function testSetProceedButtonShow()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->states('active', 'fulltime')->make()
        );

        $task = factory(SyllabusTask::class)->create();

        $task->setProceedButtonShow($user);
        $this->assertTrue($task->proceedButtonShow);

        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $task->setProceedButtonShow($user);
        $this->assertFalse($task->proceedButtonShow);

        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $task->setProceedButtonShow($user);
        $this->assertTrue($task->proceedButtonShow);

        $this->be($user);

        // count < SyllabusTask::FREE_ATTEMPTS
        for ($i = 0; $i < SyllabusTask::FREE_ATTEMPTS - 1; $i++) {
            factory(SyllabusTaskUserPay::class)->create([
                'user_id' => $user->id,
                'task_id' => $task->id
            ]);
            $task->setProceedButtonShow($user);
            $this->assertTrue($task->proceedButtonShow);
        }

        // count == SyllabusTask::FREE_ATTEMPTS && active = 0
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'active' => SyllabusTaskUserPay::STATUS_INACTIVE
        ]);
        $task->setProceedButtonShow($user);
        $this->assertFalse($task->proceedButtonShow);

        unset($task->pay);

        // count > SyllabusTask::FREE_ATTEMPTS && active = 1
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'active' => SyllabusTaskUserPay::STATUS_ACTIVE
        ]);

        $task->setProceedButtonShow($user);
        $this->assertTrue($task->proceedButtonShow);

        $task->payCount()->delete();
        $task->forceDelete();
        $user->studentProfile()->delete();
        $user->forceDelete();
    }

    public function testTestProcessSaveResultGetData()
    {
        $this->markTestIncomplete();
    }

    public function testHasFreeAttempt()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $task = factory(SyllabusTask::class)->create();

        // count < SyllabusTask::FREE_ATTEMPTS
        for ($i = 0; $i < SyllabusTask::FREE_ATTEMPTS - 1; $i++) {
            factory(SyllabusTaskResult::class)->create([
                'user_id' => $user->id,
                'task_id' => $task->id
            ]);
            $this->assertTrue($task->hasFreeAttempt($user->id));
        }

        // count == SyllabusTask::FREE_ATTEMPTS
        factory(SyllabusTaskResult::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
        $this->assertFalse($task->hasFreeAttempt($user->id));

        // count > SyllabusTask::FREE_ATTEMPTS
        factory(SyllabusTaskResult::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
        $this->assertFalse($task->hasFreeAttempt($user->id));

        $task->taskResultAll()->forceDelete();
        $task->forceDelete();
        $user->forceDelete();
    }

    public function testHasFreeAttemptCorona()
    {
        $this->markTestIncomplete();
    }

    public function testIsTrial()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $task = factory(SyllabusTask::class)->create();

        $this->be($user);

        $this->assertFalse($task->isTrial($user->id));

        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_INACTIVE,
            'active' => SyllabusTaskUserPay::STATUS_INACTIVE,
        ]);
        $this->assertFalse($task->isTrial($user->id));

        // Another user
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => User::getRandomStudentId(),
            'task_id' => $task->id,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_ACTIVE,
            'active' => SyllabusTaskUserPay::STATUS_ACTIVE,
        ]);
        $this->assertFalse($task->isTrial($user->id));

        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_ACTIVE,
            'active' => SyllabusTaskUserPay::STATUS_INACTIVE,
        ]);
        $this->assertFalse($task->isTrial($user->id));

        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_INACTIVE,
            'active' => SyllabusTaskUserPay::STATUS_ACTIVE,
        ]);
        $this->assertFalse($task->isTrial($user->id));

        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_ACTIVE,
            'active' => SyllabusTaskUserPay::STATUS_ACTIVE,
        ]);
        $this->assertTrue($task->isTrial($user->id));

        $task->payCount()->delete();
        $task->forceDelete();
        $user->forceDelete();
    }

    public function testGetSyllabusTaskData()
    {
        $this->markTestIncomplete();
    }

    public function testSetRetakeButtonShow()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->states('active', 'fulltime')->make()
        );

        $this->be($user);

        $task = factory(SyllabusTask::class)->create();

        $task->setRetakeButtonShow($user);
        $this->assertFalse($task->retakeButtonShow);

        // count < SyllabusTask::FREE_ATTEMPTS
        for ($i = 0; $i < SyllabusTask::FREE_ATTEMPTS - 1; $i++) {
            factory(SyllabusTaskUserPay::class)->create([
                'user_id' => $user->id,
                'task_id' => $task->id
            ]);
            $task->setRetakeButtonShow($user);
            $this->assertFalse($task->retakeButtonShow);
        }

        // count == SyllabusTask::FREE_ATTEMPTS && active = 0 && payed = 0
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'active' => SyllabusTaskUserPay::STATUS_INACTIVE,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_INACTIVE
        ]);
        $task->setRetakeButtonShow($user);
        $this->assertTrue($task->retakeButtonShow);

        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $task->setRetakeButtonShow($user);
        $this->assertFalse($task->retakeButtonShow);

        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $task->setRetakeButtonShow($user);
        $this->assertTrue($task->retakeButtonShow);

        unset($task->pay);

        // count > SyllabusTask::FREE_ATTEMPTS && active = 0 && payed = 0
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'active' => SyllabusTaskUserPay::STATUS_INACTIVE,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_INACTIVE
        ]);
        $task->setRetakeButtonShow($user);
        $this->assertTrue($task->retakeButtonShow);
        unset($task->pay);

        // count > SyllabusTask::FREE_ATTEMPTS && active = 1 && payed = 0
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'active' => SyllabusTaskUserPay::STATUS_ACTIVE,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_INACTIVE
        ]);
        $task->setRetakeButtonShow($user);
        $this->assertFalse($task->retakeButtonShow);

        unset($task->pay);

        // count > SyllabusTask::FREE_ATTEMPTS && active = 0 && payed = 1
        factory(SyllabusTaskUserPay::class)->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'active' => SyllabusTaskUserPay::STATUS_INACTIVE,
            'payed' => SyllabusTaskUserPay::STATUS_PAYED_ACTIVE
        ]);
        $task->setRetakeButtonShow($user);
        $this->assertFalse($task->retakeButtonShow);

        $task->payCount()->delete();
        $task->forceDelete();
        $user->studentProfile()->delete();
        $user->forceDelete();
    }

    public function testSetRetakeButtonShowCorona()
    {
        $this->markTestIncomplete();
    }
}
