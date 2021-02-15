<?php

namespace Tests\Unit;

use App\DisciplinePayCancel;

//use PHPUnit\Framework\TestCase;
use App\User;
use Tests\TestCase;

class DisciplinePayCancelTest extends TestCase
{

    public function testGetDisciplineArray()
    {
        $user = factory(User::class)->create();

        $pc1 = factory(DisciplinePayCancel::class)->create([
            'user_id' => $user->id,
            'status' => DisciplinePayCancel::STATUS_NEW,
            'executed_1c' => false,
            'executed_miras' => false
        ]);

        $pc2 = factory(DisciplinePayCancel::class)->create([
            'user_id' => $user->id,
            'status' => DisciplinePayCancel::STATUS_APPROVE,
            'executed_1c' => false,
            'executed_miras' => false
        ]);

        $pc3 = factory(DisciplinePayCancel::class)->create([
            'user_id' => $user->id,
            'status' => DisciplinePayCancel::STATUS_NEW,
            'executed_1c' => true,
            'executed_miras' => false
        ]);

        $pc4 = factory(DisciplinePayCancel::class)->create([
            'user_id' => $user->id,
            'status' => DisciplinePayCancel::STATUS_NEW,
            'executed_1c' => false,
            'executed_miras' => true
        ]);

        $pc5 = factory(DisciplinePayCancel::class)->create([
            'status' => DisciplinePayCancel::STATUS_NEW,
            'executed_1c' => false,
            'executed_miras' => false
        ]);

        $PCIds = DisciplinePayCancel::getDisciplineArray($user->id);

        $this->assertIsArray($PCIds);
        $this->assertCount(2, $PCIds);
        $this->assertTrue(in_array($pc1->discipline_id, $PCIds));
        $this->assertTrue(in_array($pc2->discipline_id, $PCIds));

        $pc1->delete();
        $pc2->delete();
        $pc3->delete();
        $pc4->delete();
        $pc5->delete();
        $user->forceDelete();
    }

    public function testRedisCacheRefresh()
    {
        $this->markTestIncomplete();
    }

    public function testGetListForAdmin()
    {
        $this->markTestIncomplete();
    }
}
