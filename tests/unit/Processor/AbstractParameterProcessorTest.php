<?php

namespace ObjectivePHP\Config\ParameterProcessor;

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractParameterProcessorTest
 *
 * @package ObjectivePHP\Config\ParameterProcessor
 */
class AbstractParameterProcessorTest extends TestCase
{
    public function testDoesHandle()
    {
        $processor = new class extends AbstractParameterProcessor {
            protected $referenceKeyword = 'keyword';

            public function process($parameter)
            {
            }
        };

        $this->assertTrue($processor->doesHandle('keyword(value)'));
    }

    public function testDoesNotHandle()
    {
        $processor = new class extends AbstractParameterProcessor {
            protected $referenceKeyword = 'keyword';

            public function process($parameter)
            {
            }
        };

        $this->assertFalse($processor->doesHandle('word(value)'));
    }

    public function testDoesNotHandleArray()
    {
        $processor = new class extends AbstractParameterProcessor {
            protected $referenceKeyword = 'keyword';

            public function process($parameter)
            {
            }
        };

        $this->assertFalse($processor->doesHandle(['key' => 'value']));
    }

    public function testDoesNotHandleObject()
    {
        $processor = new class extends AbstractParameterProcessor {
            protected $referenceKeyword = 'keyword';

            public function process($parameter)
            {
            }
        };

        $this->assertFalse($processor->doesHandle(
            new class {
            }
        ));
    }

    public function testDoesHandleObject()
    {
        $processor = new class extends AbstractParameterProcessor {
            protected $referenceKeyword = 'keyword';

            public function process($parameter)
            {
            }
        };

        $this->assertTrue($processor->doesHandle(
            new class {
                public function __toString()
                {
                    return 'keyword(test)';
                }
            }
        ));

        $this->assertFalse($processor->doesHandle(
            new class {
                public function __toString()
                {
                    return 'word(test)';
                }
            }
        ));
    }
}
