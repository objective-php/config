<?php


namespace TestDirectives {
    
    use ObjectivePHP\Config\SingleValueDirective;
    
    class TestDirective extends SingleValueDirective
    {
    
    }
}

namespace {
    
    use TestDirectives\TestDirective;
    
    return [
        new TestDirective('test'),
        new UnexistingDirective('plop') // this should not fail!
    ];
}
