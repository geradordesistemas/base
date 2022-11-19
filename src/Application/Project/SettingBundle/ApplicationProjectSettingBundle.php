<?php  
         
namespace App\Application\Project\SettingBundle;
                
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApplicationProjectSettingBundle extends Bundle
{
    /** {@inheritdoc} */
    public function getParent()
    {
        return 'ApplicationProjectSettingBundle';
    }
}