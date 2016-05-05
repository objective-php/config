<?php
    
    namespace Test\ObjectivePHP\Config;

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Config\SingleValueDirective;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Merger\MergePolicy;

    class SingleValueDirectiveTest extends TestCase
    {

        public function testSingleValueDirectiveImport()
        {
            $config = new Config();

            $directive = new TestSingleValueDirective('test value');

            $config->import($directive);

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestSingleValueDirective::class));
            $this->assertEquals('test value', $config->get(TestSingleValueDirective::class));

            $config->import(new TestOtherSingleValueDirective('other value'));

            $this->assertCount(2, $config);
            $this->assertTrue($config->has(TestOtherSingleValueDirective::class));
            $this->assertEquals('other value', $config->get(TestOtherSingleValueDirective::class));

        }

        public function testMergingBehaviour()
        {
            $config = new Config();

            $config->import(new TestSingleValueDirective('other value'));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestSingleValueDirective::class));
            $this->assertEquals('other value', $config->get(TestSingleValueDirective::class));

            // this import will override previous one, because overwriting is allowed by default
            // on scalar directives
            $config->import(new TestSingleValueDirective('overwriting value'));

            // next import is ignored because overwriting ability has been denied to hte directive
            $config->import((new TestSingleValueDirective('ignored value'))->setMergePolicy(MergePolicy::SKIP));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestSingleValueDirective::class));
            $this->assertEquals('overwriting value', $config->get(TestSingleValueDirective::class));

            // next import is ignored because overwriting ability has been denied to hte directive
            $config->import((new TestSingleValueDirective('combined value'))->setMergePolicy(MergePolicy::COMBINE));

            $this->assertEquals(['overwriting value', 'combined value'], $config->get(TestSingleValueDirective::class));
        }

    }


    class TestSingleValueDirective extends SingleValueDirective
    {
        const DIRECTIVE = 'test.directive';
    }

    class TestOtherSingleValueDirective extends SingleValueDirective
    {
        const DIRECTIVE = 'other.directive';
    }

