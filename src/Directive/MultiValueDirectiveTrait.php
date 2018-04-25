<?php
/**
 * Created by PhpStorm.
 * User: gauthier
 * Date: 25/04/2018
 * Time: 16:30
 */

namespace ObjectivePHP\Config\Directive;


trait MultiValueDirectiveTrait
{
    
    protected $reference = 'default';
    
    public function setReference(string $reference): MultiValueDirectiveInterface
    {
        $this->reference = $reference;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }
    
}
