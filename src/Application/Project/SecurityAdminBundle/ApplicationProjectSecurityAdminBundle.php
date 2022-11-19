<?php  
         
namespace App\Application\Project\SecurityAdminBundle;
                
use Symfony\Component\HttpKernel\Bundle\Bundle;
                
class ApplicationProjectSecurityAdminBundle extends Bundle
{
    /** {@inheritdoc} */
    public function getParent()
    {
        return 'ApplicationProjectSecurityAdminBundle';
    }
}