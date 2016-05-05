<?php
    
    namespace Test\ObjectivePHP\Config;

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Config\StackedValuesDirective;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Merger\MergePolicy;

    class StackedValuesDirectiveTest extends TestCase
    {

        public function testStackedValuesDirectiveImport()
        {
            $config = new Config();

            $directive = new TestStackedValuesDirective('test value');

            $config->import($directive);

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestStackedValuesDirective::class));
            $this->assertEquals(['test value'], $config->get(TestStackedValuesDirective::class));

            $config->import(new TestOtherStackedValuesDirective('other value'));

            $this->assertCount(2, $config);
            $this->assertTrue($config->has(TestOtherStackedValuesDirective::class));
            $this->assertEquals(['other value'], $config->get(TestOtherStackedValuesDirective::class));

        }

        public function testOverwritingBehaviour()
        {
            $config = new Config();

            $config->import(new TestStackedValuesDirective('other value'));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestStackedValuesDirective::class));
            $this->assertEquals(['other value'], $config->get(TestStackedValuesDirective::class));

            // this import will stack the second value, because stacking is the default behaviour for StackedValuesDirective
            $config->import(new TestStackedValuesDirective('stacked value'));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestStackedValuesDirective::class));
            $this->assertEquals(['other value', 'stacked value'], $config->get(TestStackedValuesDirective::class));


            // next import is ignored because overwriting ability has been denied to the directive
            $config->import((new TestStackedValuesDirective('overwriting value'))->setMergePolicy(MergePolicy::REPLACE));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestStackedValuesDirective::class));
            $this->assertEquals(['overwriting value'], $config->get(TestStackedValuesDirective::class));

        }

    }


    class TestStackedValuesDirective extends StackedValuesDirective
    {
        const DIRECTIVE = 'test.directive';
    }

    class TestOtherStackedValuesDirective extends StackedValuesDirective
    {
        const DIRECTIVE = 'other.directive';
    }

