<?php
    
    namespace Test\ObjectivePHP\Config;

    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Config\Exception;
    use ObjectivePHP\PHPUnit\TestCase;
    use ObjectivePHP\Primitives\Merger\MergePolicy;
    use ObjectivePHP\Primitives\Merger\ValueMerger;

    class ConfigTest extends TestCase
    {

        public function testDeepAccess()
        {

            $config =  new Config([
                            'app.version'   => 1.0,
                            'app.env'       => 'test',
                            'db.config.host' => 'localhost',
                            'db.config.user' => 'plop',
                            'db.driver' => 'pgsql'
            ]);

            $appConfig = $config->app;
            $this->assertInstanceOf(Config::class, $appConfig);
            $this->assertEquals(['app.version' => '1.0', 'app.env' => 'test'], $appConfig->toArray());

            $appConfig = $config['app'];
            $this->assertInstanceOf(Config::class, $appConfig);
            $this->assertEquals(['app.version' => '1.0', 'app.env' => 'test'], $appConfig->toArray());

            $appConfig = $config->get('app');
            $this->assertInstanceOf(Config::class, $appConfig);
            $this->assertEquals(['app.version' => '1.0', 'app.env' => 'test'], $appConfig->toArray());


            $this->assertEquals('1.0', $appConfig->version);

        }

        public function testFQNAdditions()
        {
            $config = new Config([
                'app.version'    => 1.0,
                'app.env'        => 'test',
            ]);

            $config->setSection('app')->set('debug', true);

            $this->assertTrue($config->get('app.debug'));
        }

        public function testMergingValuesWithFQN()
        {

            $config = new Config([
                'debug.environments' => 'test',
            ]);


            $otherConfig = (new Config([
                'debug.environments' => 'dev',
            ]))->addMerger('debug.environments', new ValueMerger(MergePolicy::COMBINE));

            $config->merge($otherConfig);

            $this->assertEquals(['test', 'dev'], $config->debug->environments);


        }

        public function testMergingValuesWithShortNames()
        {

            $config = new Config([
                'debug.environments' => 'test',
            ]);


            $otherConfig = (new Config([
                'environments' => 'dev',
            ]))
                ->setSection('debug')
                ->addMerger('environments', new ValueMerger(MergePolicy::COMBINE));

            $config->merge($otherConfig);

            $this->assertEquals(['test', 'dev'], $config->debug->environments);


        }

        public function testFactory()
        {
            $config = Config::factory([
                'section' => 'app',
                'mergers' => ['tokens' => new ValueMerger(MergePolicy::COMBINE)],
                'directives' =>
                [
                    'version' => '1.1',
                    'environment' => 'dev',
                    'tokens' => 'first'
                ]
            ]);


            $this->assertEquals('1.1', $config->app->version);

            $config->merge(new Config(['app.tokens' => 'second']));

            $this->assertEquals(['first', 'second'], $config->app->tokens);
        }

        public function testConfigForbidsToSetDirectivesMatchingSectionName()
        {
            $config = new Config(['app.version' => '1.0']);

            $this->expectsException(function() use ($config)
            {
                $config->set('app', 'this is forbidden because app.version already exists!');
            }, Exception::class, null, Exception::FORBIDDEN_DIRECTIVE_NAME);
        }

        public function testConfigForbidsToSetSectionsMatchingDirectiveName()
        {
            $config = new Config(['app.name' => 'my app']);
            $config->setSection('app');

            $this->expectsException(function() use ($config)
            {
                $config->set('name.version', 'this is forbidden because app already exists!');
            }, Exception::class, null, Exception::FORBIDDEN_SECTION_NAME);
        }
    }