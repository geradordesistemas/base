<?php  
         
namespace App\Application\Project\SecurityUserBundle;
                
use Symfony\Component\HttpKernel\Bundle\Bundle;
                
class ApplicationProjectSecurityUserBundle extends Bundle
{
    /** {@inheritdoc} */
    public function getParent()
    {
        return 'ApplicationProjectSecurityUserBundle';
    }
}