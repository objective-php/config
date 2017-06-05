<?php
    
    namespace Test\ObjectivePHP\Config;

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Merger\MergePolicy;
    use Tests\Helper\TestDirectives\TestSingleValueDirectiveGroup;

    class SingleValueDirectiveGroupTest extends TestCase
    {

        public function testSingleValueDirectiveGroupImport()
        {
            $config = new Config();

            $directive = new TestSingleValueDirectiveGroup('first', 'test value');

            $config->import($directive);

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestSingleValueDirectiveGroup::class . '.first'));
            $this->assertEquals('test value', $config->get(TestSingleValueDirectiveGroup::class . '.first'));

            $config->import(new TestSingleValueDirectiveGroup('second', 'other value'));

            $this->assertCount(2, $config);
            $this->assertFalse($config->has(TestSingleValueDirectiveGroup::class));
            $this->assertEquals('other value', $config->get(TestSingleValueDirectiveGroup::class . '.second'));

        }

        public function testMergingBehaviour()
        {
            $config = new Config();

            $config->import(new TestSingleValueDirectiveGroup('first', 'other value'));

            $this->assertCount(1, $config);
            $this->assertTrue($config->has(TestSingleValueDirectiveGroup::class . '.first'));
            $this->assertEquals('other value', $config->get(TestSingleValueDirectiveGroup::class . '.first'));


            $config->import(new TestSingleValueDirectiveGroup('second', 'stacked value'));

            $this->assertCount(2, $config);
            $this->assertTrue($config->has(TestSingleValueDirectiveGroup::class . '.second'));
            $this->assertEquals('stacked value', $config->get(TestSingleValueDirectiveGroup::class . '.second'));


            // override previously imported value (default behaviour for non-array values)
            $config->import(new TestSingleValueDirectiveGroup('second', 'overwriting value'));

            $this->assertCount(2, $config);
            $this->assertTrue($config->has(TestSingleValueDirectiveGroup::class . '.second'));

            // next import is ignored because overwriting ability has been denied to hte directive
            $config->import((new TestSingleValueDirectiveGroup('second', 'over overwriting value'))->setMergePolicy(MergePolicy::SKIP));

            $this->assertEquals('overwriting value', $config->get(TestSingleValueDirectiveGroup::class . '.second'));

            // check that a Multiple Directive full content can be retrieved as subset
            $this->assertEquals(['first' => 'other value', 'second' => 'overwriting value'], $config->subset(TestSingleValueDirectiveGroup::class)
                                                                                                   ->toArray());

            // test default merging behaviour for array values
            $config->import(new TestSingleValueDirectiveGroup('third', ['x' => 'y', 'z']));
            $config->import(new TestSingleValueDirectiveGroup('third', ['x' => 'a', 'b']));
            $config->import(new TestSingleValueDirectiveGroup('third', ['c' => 'd']));
            $config->import(new TestSingleValueDirectiveGroup('third', 'e'));
            $this->assertEquals(['x' => 'a', 'z', 'b', 'c' => 'd', 'e'], $config->get(TestSingleValueDirectiveGroup::class . '.third'));
        }

    }


