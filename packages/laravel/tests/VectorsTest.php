<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UniverseIT\KurdishSorani\IraqiPhone;
use UniverseIT\KurdishSorani\Sorani;

/**
 * Runs the shared test vectors (../../test-vectors/vectors.json) that the Dart
 * package also runs, so both packages always behave the same.
 */
final class VectorsTest extends TestCase
{
    private static array $v;

    public static function setUpBeforeClass(): void
    {
        self::$v = json_decode((string) file_get_contents(__DIR__ . '/../../../test-vectors/vectors.json'), true, flags: JSON_THROW_ON_ERROR);
    }

    public function test_normalize(): void
    {
        foreach (self::$v['normalize'] as $c) {
            $this->assertSame($c['out'], Sorani::normalize($c['in']), $c['in']);
        }
        foreach (self::$v['normalizeFinalHeh'] as $c) {
            $this->assertSame($c['out'], Sorani::normalize($c['in'], finalHeh: true), $c['in']);
        }
    }

    public function test_digits(): void
    {
        foreach (self::$v['toKurdishDigits'] as $c) {
            $this->assertSame($c['out'], Sorani::toKurdishDigits($c['in']));
        }
        foreach (self::$v['toLatinDigits'] as $c) {
            $this->assertSame($c['out'], Sorani::toLatinDigits($c['in']));
        }
    }

    public function test_format_iqd(): void
    {
        foreach (self::$v['formatIqd'] as $c) {
            $this->assertSame($c['out'], Sorani::formatIqd($c['amount'], $c['kurdishDigits']));
        }
    }

    public function test_number_to_words(): void
    {
        foreach (self::$v['numberToWords'] as $c) {
            $this->assertSame($c['out'], Sorani::numberToWords($c['in']), (string) $c['in']);
        }
    }

    public function test_negative_number_throws(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Sorani::numberToWords(-1);
    }

    public function test_valid_phones(): void
    {
        foreach (self::$v['phoneValid'] as $c) {
            $p = IraqiPhone::parse($c['in']);
            $this->assertNotNull($p, $c['in']);
            $this->assertSame($c['e164'], $p->e164());
            $this->assertSame($c['national'], $p->national());
            $this->assertSame($c['carrier'], $p->carrier());
        }
    }

    public function test_invalid_phones(): void
    {
        foreach (self::$v['phoneInvalid'] as $in) {
            $this->assertFalse(IraqiPhone::isValid($in), $in);
        }
        $this->assertFalse(IraqiPhone::isValid(null));
    }
}
