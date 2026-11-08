<?php

namespace Btinet\Test\Service;

use Btinet\Untis\Enum\Substitution\UntisBit;
use Btinet\Untis\Enum\Substitution\UntisType;
use Btinet\Untis\Service\EncodingHelper;
use PHPUnit\Framework\TestCase;

class EncodingHelperTest extends TestCase
{
    private EncodingHelper $encoder;

    public function setUp(): void
    {
        $this->encoder = new EncodingHelper();
    }

    public function testOneClass(): void
    {
        $this->assertSame("7.1", $this->encoder->strToStr("  7.1 "));
    }

    public function testManyClasses(): void
    {
        $this->assertSame(["7.1", "10B", "10C"], $this->encoder->strToStr("7.1~10B~10C", true));
    }

    public function testCounter(): void
    {
        $this->assertSame(["-", "+"], $this->encoder->strToStr("-~+", true));
    }

    public function testBit(): void
    {
        $expected = [
            UntisBit::Cancellation
        ];
        $value = $this->encoder->strToBitArray("1");
        $this->assertEquals($expected, $value);
    }

    public function testManyBits(): void
    {
        $expected = [
            UntisBit::Cancellation,
            UntisBit::SpecialDuty,
        ];
        $value = $this->encoder->strToBitArray("5");
        $this->assertEquals($expected, $value);
    }

    public function testNegativeBitValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->encoder->strToBitArray("-1");
    }

    public function testUntisTypeIsNull(): void
    {
        $this->assertNull($this->encoder->strToType(""));
    }

    public function testUntisTypeIsUnavailable(): void
    {
        $this->assertNull($this->encoder->strToType("X")); // X gibt es nicht
    }

    public function testUntisType(): void
    {
        $this->assertSame(UntisType::Cancellation, $this->encoder->strToType("C"));
    }

    public function testUntisTypeWithWhitespace(): void
    {
        $this->assertSame(UntisType::Cancellation, $this->encoder->strToType("  C  "));
    }

    public function testBitZeroReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->encoder->strToBitArray("   0   "));
    }

    public function testInteger(): void
    {
        $this->assertSame(9, $this->encoder->strToInt("9  "));
    }

    public function testPositiveInteger(): void
    {
        $this->assertSame(9, $this->encoder->strToPositiveInt("9  "));
    }

    public function testNegativeIntegerThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->encoder->strToPositiveInt("-1  ");
    }

    public function testIdWithValidNumber(): void
    {
        $this->assertSame(12345, $this->encoder->strToId("12345"));
    }

    public function testIdWithEmptyStringReturnsNull(): void
    {
        $this->assertNull($this->encoder->strToId(""));
    }

    public function testIdWithNullReturnsNull(): void
    {
        $this->assertNull($this->encoder->strToId(null));
    }

    public function testIdWithWhitespace(): void
    {
        $this->assertSame(123, $this->encoder->strToId("  123  "));
    }

    public function testIdWithZero(): void
    {
        $this->assertSame(0, $this->encoder->strToId("0"));
    }

    public function testIdWithNegativeNumberThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->encoder->strToId("-1");
    }

    public function testIdWithFloatThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->encoder->strToId("123.45");
    }

    public function testIdWithInvalidStringThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->encoder->strToId("abc");
    }

    public function testGermanLetters(): void
    {
        $this->assertSame("Jäg", $this->encoder->strToStr("Jäg"));
        $this->assertSame("Müller für die Große Pause", $this->encoder->strToStr("M\xFCller f\xFCr die Gro\xDFe Pause"));
    }

    public function testDate(): void
    {
        $expected = new \DateTimeImmutable("2006-10-11");
        $this->assertEquals($expected, $this->encoder->strToDateTimeImmutable("20061011"));
    }

    public function testDateAndTime(): void
    {
        $expected = new \DateTimeImmutable("2006-10-11 14:28");
        $this->assertEquals($expected, $this->encoder->strToDateTimeImmutable("200610111428", "YmdHi"));
    }

}