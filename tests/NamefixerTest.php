<?php

declare(strict_types = 1);

namespace Rentalhost\Vanilla\Namefixer\Tests;

use PHPUnit\Framework\TestCase;
use Rentalhost\Vanilla\Namefixer\Namefixer;

class NamefixerTest
    extends TestCase
{
    public function dataProviderFixMethod(): array
    {
        return [
            // Simple names.
            [ 'john', 'John' ],

            // Shortned names.
            [ 'john d.', 'John D.' ],
            [ 'john d', 'John D.' ],
            [ 'john e', 'John E.' ],
            [ 'john d. second', 'John D. Second' ],
            [ 'john d second', 'John D. Second' ],
            [ 'john e. second', 'John E. Second' ],
            [ 'john e second', 'John e Second' ],
            [ 'john E second', 'John E. Second' ],
            [ 'john á. second', 'John Á. Second' ],
            [ 'john á second', 'John Á. Second' ],
            [ 'john jr', 'John Jr.' ],
            [ 'john jr.', 'John Jr.' ],
            [ 'john dd', 'John D. D.' ],
            [ 'e doe', 'E. Doe' ],

            // Full names.
            [ 'john doe', 'John Doe' ],
            [ 'john d\'oe', 'John D\'oe' ],
            [ 'john de.', 'John de' ],
            [ 'john sá', 'John Sá' ],
            [ 'john De doe', 'John de Doe' ],
            [ 'john cytryn', 'John Cytryn' ],
            [ 'john mc doe', 'John Mc Doe' ],
            [ 'john mcdoe', 'John McDoe' ],
            [ "john\u{00A0}", 'John' ],

            // With prepositions.
            [ 'john e de do da dos das el la lo di van der den doe', 'John e de do da dos das el la lo di van der den Doe' ],

            // Force first uppercase.
            [ 'do doe', 'do Doe' ],
            [ 'do doe', 'Do Doe', true ],
        ];
    }

    public function dataProviderIsValidMethod(): array
    {
        return [
            [ 'john', true ],
            [ 'john d.', true ],
            [ 'john d\'oe', true ],
            [ 'john (doe)', false ],
            [ '123', false ]
        ];
    }

    /**
     * @dataProvider dataProviderFixMethod
     */
    public function testFixMethod(?string $input, ?string $expected, ?bool $forceFirstUppercase = null): void
    {
        self::assertSame($expected, Namefixer::fix($input, $forceFirstUppercase));
    }

    /**
     * @dataProvider dataProviderIsValidMethod
     */
    public function testIsValidMethod(?string $input, bool $expected): void
    {
        self::assertSame($expected, Namefixer::isValid($input));
    }
}
