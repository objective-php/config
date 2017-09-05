<?php
    
    namespace Test\ObjectivePHP\Config;

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Config\SingleValueDirective;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Merger\MergePolicy;
    use Tests\Helper\TestDirectives\TestOtherSingleValueDirective;
    use Tests\Helper\TestDirectives\SampleSingleValueDirective;

    class SingleValueDirectiveTest extends TestCase
    {

        public function testSingleValueDirectiveImport()
        {
            $config = new Config();

            $directive = new SampleSingleValueDirective('test value');

            $config->import($directive);

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(SampleSingleValueDirective::class));
            $this->assertEquals('test value', $config->get(SampleSingleValueDirective::class));

            $config->import(new TestOtherSingleValueDirective('other value'));

            $this->assertCount(2, $config);
            $this->assertTrue($config->has(TestOtherSingleValueDirective::class));
            $this->assertEquals('other value', $config->get(TestOtherSingleValueDirective::class));

        }

        public function testMergingBehaviour()
        {
            $config = new Config();

            $config->import(new SampleSingleValueDirective('other value'));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(SampleSingleValueDirective::class));
            $this->assertEquals('other value', $config->get(SampleSingleValueDirective::class));

            // this import will override previous one, because overwriting is allowed by default
            // on scalar directives
            $config->import(new SampleSingleValueDirective('overwriting value'));

            // next import is ignored because overwriting ability has been denied to hte directive
            $config->import((new SampleSingleValueDirective('ignored value'))->setMergePolicy(MergePolicy::SKIP));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(SampleSingleValueDirective::class));
            $this->assertEquals('overwriting value', $config->get(SampleSingleValueDirective::class));

            // next import is ignored because overwriting ability has been denied to hte directive
            $config->import((new SampleSingleValueDirective('combined value'))->setMergePolicy(MergePolicy::COMBINE));

            $this->assertEquals(['overwriting value', 'combined value'], $config->get(SampleSingleValueDirective::class));
        }

    }


    
