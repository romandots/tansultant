<?php
/**
 * File: AssertExceptionTrait.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

/**
 * Trait AssertExceptionTrait
 * @package Tests\Unit
 */
trait AssertExceptionTrait
{
    /**
     * @param callable $callback
     * @param string $expectedException
     * @param null $expectedCode
     * @param null $expectedMessage
     *
     * @author https://gist.github.com/VladaHejda/8826707
     */
    protected function assertException(
        callable $callback,
        $expectedException,
        $expectedCode = null,
        $expectedMessage = null
    ): void {
        try {
            $callback();
        } catch (\Throwable $e) {
            $class = \get_class($e);
            $message = $e->getMessage();
            $code = $e->getCode();
            $errorMessage = 'Failed asserting the class of exception';

            if ($message && $code) {
                $errorMessage .= sprintf(' (message was %s, code was %d)', $message, $code);
            } elseif ($code) {
                $errorMessage .= sprintf(' (code was %d)', $code);
            }
            $errorMessage .= '.';

            $this->assertInstanceOf($expectedException, $e, $errorMessage);

            if ($expectedCode !== null) {
                $this->assertEquals($expectedCode, $code, sprintf('Failed asserting code of thrown %s.', $class));
            }

            if ($expectedMessage !== null) {
                $this->assertContains(
                    $expectedMessage,
                    $message,
                    sprintf('Failed asserting the message of thrown %s.', $class)
                );
            }
            return;
        }

        $errorMessage = 'Failed asserting that exception';
        $errorMessage .= sprintf(' of type %s', $expectedException);
        $errorMessage .= ' wasn\'t thrown.';

        $this->fail($errorMessage);
    }
}
