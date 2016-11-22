<?php

namespace Geekish\Crap;

use Symfony\Component\Console\Output\BufferedOutput;

class ExceptionHandlerTest extends TestCase
{
    public function testOutput()
    {
        $output = new BufferedOutput;
        $handler = new ExceptionHandler($output);

        $message = "Error!";

        $exception = CrapException::create($message);

        $handler($exception, false);

        $this->assertEquals($message . "\n", $output->fetch());
    }
}
