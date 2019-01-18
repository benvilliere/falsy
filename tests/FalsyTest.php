<?php

use PHPUnit\Framework\TestCase;

use Angle\Falsy\Exceptions\ArrayComparisonException;
use Angle\Falsy\Exceptions\ClosureComparisonException;
use Angle\Falsy\Exceptions\ObjectComparisonException;
use Angle\Falsy\Falsy;

final class FalsyTest extends TestCase
{
    public function testNegativeTypes()
    {
        $falsy = falsy(
            false,
            null,
            0,
            0.0,
            '0',
            [],
            [''],
            ['' => ''],
            [false, null],
            ['' => '', 0 => ['key' => null, 'foo' => [''], 'empty' => []]],
            new stdClass
        );

        $this->assertTrue($falsy);
    }

    public function testPositiveTypes()
    {
        $class = new stdClass;
        $class->foo = 'bar';

        $truthy = truthy(
            true,
            1,
            0.1,
            '1',
            ['1'],
            ['foo' => 'bar'],
            [true],
            $class
        );

        $this->assertTrue($truthy);
    }

    public function testWithoutParameters()
    {
        $this->assertTrue((new Falsy)->isFalsy());
    }

    public function testWithTrue()
    {
        $this->assertTrue((new Falsy(true))->isTruthy());
    }

    public function testWithFalse()
    {
        $this->assertTrue((new Falsy(false))->isFalsy());
    }

    public function testWithBooleans()
    {
        $falsy = new Falsy;
        $this->assertTrue($falsy->isFalsy());
        $this->assertFalse($falsy->isTruthy());

        $this->assertTrue((new Falsy(true))->isTruthy());
        $this->assertTrue((new Falsy(false))->isFalsy());
        $this->assertTrue((new Falsy(false, false))->isFalsy());
        $this->assertTrue((new Falsy(true, true))->isTruthy());
        $this->assertTrue((new Falsy(false, true))->isFalsy());
        $this->assertTrue((new Falsy(true, false))->isFalsy());
        $this->assertTrue((new Falsy(true, false, true, false, false))->isFalsy());
    }

    public function testWithEmptyString()
    {
        $this->assertTrue((new Falsy(''))->isFalsy());
        $this->assertTrue((new Falsy('', '', ''))->isFalsy());
        $this->assertTrue((new Falsy('', '', 'x'))->isFalsy());
        $this->assertTrue((new Falsy('', 'x', ''))->isFalsy());
        $this->assertTrue((new Falsy('x', '', ''))->isFalsy());
        $this->assertTrue((new Falsy('foo', 'bar'))->isTruthy());
    }

    public function testWithNull()
    {
        $this->assertTrue((new Falsy(null))->isFalsy());
        $this->assertTrue((new Falsy(null, null, null))->isFalsy());
    }

    public function testWithZero()
    {
        $this->assertTrue((new Falsy(0))->isFalsy());
        $this->assertFalse((new Falsy(1))->isFalsy());
        $this->assertFalse((new Falsy(-1))->isFalsy());
    }

    public function testWithZeroFloat()
    {
        $this->assertTrue((new Falsy(0.0))->isFalsy());
        $this->assertFalse((new Falsy(0.1))->isFalsy());
        $this->assertFalse((new Falsy(-0.1))->isFalsy());
    }

    public function testWithZeroString()
    {
        $this->assertTrue((new Falsy('0'))->isFalsy());
        $this->assertFalse((new Falsy('1'))->isFalsy());
    }

    public function testWithEmptyArray()
    {
        $this->assertTrue((new Falsy([]))->isFalsy());
        $this->assertTrue((new Falsy([0]))->isFalsy());
        $this->assertTrue((new Falsy(['']))->isFalsy());
        $this->assertTrue((new Falsy(['' => '']))->isFalsy());
        $this->assertTrue((new Falsy(['key' => '']))->isFalsy());
    }

    public function testWithFilledArray()
    {
        $this->assertTrue((new Falsy([1]))->isTruthy());
        $this->assertTrue((new Falsy([true]))->isTruthy());
        $this->assertTrue((new Falsy([true, false]))->isFalsy());
        $this->assertTrue((new Falsy(['' => 'value']))->isTruthy());
        $this->assertTrue((new Falsy(['', '', '']))->isFalsy());
        $this->assertTrue((new Falsy(['key' => 'value']))->isTruthy());
    }

    public function testWithEmptyObject()
    {
        $object = new stdClass;
        $object->boolProperty = false;
        $object->stringProperty = null;
        $object->intProperty = 0;
        $object->numericProperty = '0';
        $object->arrayProperty = [false, null, 0, '0', 'key' => null, 'nested' => [0 => [0 => false]]];

        $this->assertTrue((new Falsy($object))->isFalsy());
        $this->assertTrue((new Falsy($object, $object, $object))->isFalsy());
        $this->assertFalse((new Falsy($object))->isTruthy());
        $this->assertFalse((new Falsy($object, $object, $object))->isTruthy());
    }

    public function testWithFilledObject()
    {
        $object = new stdClass;
        $object->boolProperty = true;
        $object->stringProperty = 'foo';
        $object->intProperty = 1;
        $object->numericProperty = '1';
        $object->arrayProperty = [true, 'foo', 1, '1', 'key' => 'bar', 'nested' => [0 => [0 => true]]];

        $this->assertFalse((new Falsy($object))->isFalsy());
        $this->assertFalse((new Falsy($object, $object, $object))->isFalsy());
        $this->assertTrue((new Falsy($object))->isTruthy());
        $this->assertTrue((new Falsy($object, $object, $object))->isTruthy());
    }

    public function testWithMixedObject()
    {
        $object = new stdClass;
        $object->boolTruthy = true;
        $object->stringTruthy = 'foo';
        $object->intTruthy = 1;
        $object->numericTruthy = '1';
        $object->arrayTruthy = [true, 'foo', 1, '1', 'key' => 'bar', 'nested' => [0 => [0 => true]]];
        $object->boolFalsy = false;
        $object->stringFalsy = null;
        $object->intFalsy = 0;
        $object->numericFalsy = '0';
        $object->arrayFalsy = [false, null, 0, '0', 'key' => null, 'nested' => [0 => [0 => false]]];

        $this->assertTrue((new Falsy($object))->isFalsy());
        $this->assertTrue((new Falsy($object, $object, $object))->isFalsy());
        $this->assertFalse((new Falsy($object))->isTruthy());
        $this->assertFalse((new Falsy($object, $object, $object))->isTruthy());
    }

    public function testWithClosures()
    {
        $true = function () { return true; };
        $false = function () { return false; };
        $null = function () { return null; };
        $zero = function () { return 0; };
        $int = function () { return 1; };
        $void = function () { return; };

        $this->assertTrue((new Falsy($true))->isTruthy());
        $this->assertTrue((new Falsy($false))->isFalsy());
        $this->assertTrue((new Falsy($null))->isFalsy());
        $this->assertTrue((new Falsy($zero))->isFalsy());
        $this->assertTrue((new Falsy($int))->isTruthy());
        $this->assertTrue((new Falsy($void))->isFalsy());

        $this->assertFalse((new Falsy($false))->isTruthy());
        $this->assertFalse((new Falsy($null))->isTruthy());

        $this->assertTrue((new Falsy($false, $false, $false))->isFalsy());
        $this->assertTrue((new Falsy($null, $null, $null))->isFalsy());

        $this->assertTrue((new Falsy($false, $null, $true))->isFalsy());
        $this->assertTrue((new Falsy($true, $int, $true))->isTruthy());
    }

    public function testWithUndefinedVariables()
    {
        $array = ['foo' => 'bar'];

        // One should never use @function to prevent errors from popping up,
        // as it is a bad practice. But this was the only way to make falsy
        // able to assert truthiness or falseness on undefined variables.

        $this->assertTrue(@falsy($array['baz'], $undefined));
        $this->assertFalse(@truthy($array['baz'], $undefined));
    }
}
