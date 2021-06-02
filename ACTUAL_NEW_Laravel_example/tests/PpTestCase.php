<?php

namespace Tests;

use App\Helpers\PartnerProgramStorage;
use App\Models\Pp;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class PpTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        $this->afterApplicationCreatedCallbacks[] = function () {
            $pp = factory(Pp::class)->create();
            PartnerProgramStorage::setPP($pp);
            config(['app.url' => 'https://' . $pp->tech_domain]);
            \URL::forceRootUrl('https://' . $pp->tech_domain);
        };

        parent::setUp();
    }
}
