<?php

namespace Tests\Services;

use App\Services\LanguageService;
use Tests\TestCase;

class LanguageServiceTest extends TestCase
{

    public function testGetByType()
    {
        // Native
        $this->assertSame(
            LanguageService::LANGUAGE_RU,
            LanguageService::getByType(LanguageService::TYPE_NATIVE, LanguageService::LANGUAGE_RU)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_KZ,
            LanguageService::getByType(LanguageService::TYPE_NATIVE, LanguageService::LANGUAGE_KZ)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_EN,
            LanguageService::getByType(LanguageService::TYPE_NATIVE, LanguageService::LANGUAGE_EN)
        );

        // Second
        $this->assertSame(
            LanguageService::LANGUAGE_KZ,
            LanguageService::getByType(LanguageService::TYPE_SECOND, LanguageService::LANGUAGE_RU)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_KZ,
            LanguageService::getByType(LanguageService::TYPE_SECOND, LanguageService::LANGUAGE_EN)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_RU,
            LanguageService::getByType(LanguageService::TYPE_SECOND, LanguageService::LANGUAGE_KZ)
        );

        // Other
        $this->assertSame(
            LanguageService::LANGUAGE_EN,
            LanguageService::getByType(LanguageService::TYPE_OTHER, LanguageService::LANGUAGE_RU)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_EN,
            LanguageService::getByType(LanguageService::TYPE_OTHER, LanguageService::LANGUAGE_KZ)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_EN,
            LanguageService::getByType(LanguageService::TYPE_OTHER, LanguageService::LANGUAGE_EN)
        );
    }

    public function testWrongType()
    {
        $this->expectException(\Exception::class);
        LanguageService::getByType('bla', LanguageService::LANGUAGE_EN);
    }

    public function testGetSecond()
    {
        $this->assertSame(
            LanguageService::LANGUAGE_KZ,
            LanguageService::getSecond(LanguageService::LANGUAGE_RU)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_KZ,
            LanguageService::getSecond(LanguageService::LANGUAGE_EN)
        );
        $this->assertSame(
            LanguageService::LANGUAGE_RU,
            LanguageService::getSecond(LanguageService::LANGUAGE_KZ)
        );
    }
}
