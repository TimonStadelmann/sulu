<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\CustomUrl\Tests\Functional\Generator;

use PHPUnit\Framework\TestCase;
use Sulu\Component\CustomUrl\Generator\Generator;
use Sulu\Component\CustomUrl\Generator\MissingDomainPartException;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Url\Replacer;

class GeneratorTest extends TestCase
{
    public function provideGenerateData()
    {
        $locales = [new Localization(), new Localization()];

        $locales[0]->setLanguage('de');
        $locales[0]->setCountry('at');
        $locales[1]->setLanguage('en');

        return [
            [
                '*.sulu.io/test/*',
                ['test-1', 'test-2'],
                null,
                'test-1.sulu.io/test/test-2',
            ],
            [
                '*.sulu.io/test/*',
                ['test-1', 'test-2'],
                null,
                'test-1.sulu.io/test/test-2',
            ],
            [
                '*.sulu.io/*',
                ['test-1', 'test-2'],
                null,
                'test-1.sulu.io/test-2',
            ],
            [
                '*.sulu.io/*',
                ['test-1', 'test-2'],
                null,
                'test-1.sulu.io/test-2',
            ],
            [
                '*.sulu.io/test/*',
                ['test-1', 'test-2'],
                $locales[0],
                'test-1.sulu.io/test/test-2/de_at',
            ],
            [
                '*.sulu.io/test/*',
                ['test-1', 'test-2'],
                $locales[1],
                'test-1.sulu.io/test/test-2/en',
            ],
            [
                '*.sulu.io/{localization}/*',
                ['test-1', 'test-2'],
                $locales[0],
                'test-1.sulu.io/de_at/test-2',
            ],
            [
                '*.sulu.io/{localization}/*',
                ['test-1', 'test-2'],
                $locales[1],
                'test-1.sulu.io/en/test-2',
            ],
            [
                '*.sulu.io/*',
                ['test-1', 'test-2'],
                $locales[0],
                'test-1.sulu.io/test-2/de_at',
            ],
            [
                '*.sulu.io/*',
                ['test-1', 'test-2'],
                $locales[1],
                'test-1.sulu.io/test-2/en',
            ],
            [
                '*.sulu.io/*/*',
                ['test-1', 'test-2', 'test-3'],
                $locales[1],
                'test-1.sulu.io/test-2/test-3/en',
            ],
            [
                '*.sulu.io/*/{localization}/*',
                ['test-1', 'test-2', 'test-3'],
                $locales[1],
                'test-1.sulu.io/test-2/en/test-3',
            ],
            [
                '*.sulu.io/{country}/*/{language}/*',
                ['test-1', 'test-2', 'test-3'],
                $locales[0],
                'test-1.sulu.io/at/test-2/de/test-3',
            ],
            [
                '*.sulu.io/at/*',
                ['test-1', 'test-2'],
                null,
                'test-1.sulu.io/at/test-2',
            ],
            [
                '*.sulu.io/at/*',
                ['test-1'],
                null,
                null,
                MissingDomainPartException::class,
            ],
            [
                '*.sulu.io/at',
                ['test-1'],
                null,
                'test-1.sulu.io/at',
            ],
            [
                '*.sulu.io/at/*',
                ['test-1'],
                null,
                null,
                MissingDomainPartException::class,
            ],
        ];
    }

    /**
     * @dataProvider provideGenerateData
     */
    public function testGenerate($baseDomain, $domainParts, $locales, $expected, $exception = null): void
    {
        if ($exception) {
            self::expectException($exception);
        }

        $generator = new Generator(new Replacer());
        $result = $generator->generate($baseDomain, $domainParts, $locales);

        if (null === $expected) {
            return;
        }

        $this->assertEquals($expected, $result);
    }
}
