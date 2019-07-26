<?php
/**
 * File: HelperTest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Class HelperTest
 * @package Tests\Unit
 */
class HelperTest extends TestCase
{
    /**
     * @param string $dirty
     * @dataProvider provideDirtyPhoneNumber
     */
    public function testPhoneFormat(string $dirty): void
    {
        $clean = \phone_format($dirty);

        $this->assertEquals('+7-999-999-99-99', $clean);
    }

    /**
     * @return array
     */
    public function provideDirtyPhoneNumber(): array
    {
        return [
            ['8(999)999 99-99'],
            ['+79999999999'],
            ['+7-999-9999999'],
            ['+7(999)999-99-99'],
            ['+7 (999) 999-99-99'],
            ['+7.9999.99.99.99'],
            ['+7-999-999 99 99'],
            ['+7 999 999 99 99'],
            ['79999999999'],
            ['7-999-9999999'],
            ['7(999)999-99-99'],
            ['7 (999) 999-99-99'],
            ['7.9999.99.99.99'],
            ['7-999-999 99 99'],
            ['7 999 999 99 99'],
            ['89999999999'],
            ['8-999-9999999'],
            ['8(999)999-99-99'],
            ['8 (999) 999-99-99'],
            ['8.9999.99.99.99'],
            ['8-999-999 99 99'],
            ['8 999 999 99 99'],
        ];
    }
}
