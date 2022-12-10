<?php
namespace App\Application\Project\SettingBundle\Admin;

use App\Application\Project\ContentBundle\Admin\Base\BaseAdmin;
use App\Application\Project\SettingBundle\Entity\SmtpEmail;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Choice;


final class SmtpEmailAdmin extends BaseAdmin
{
    public function toString(object $object): string
    {
        return $object instanceof SmtpEmail ? $object->getId() . ' - Configurações do SMTP' : '';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);

        /** Não Remove chamada - Faz controle de acesso painel admin */
        $collection->remove('delete');
        $collection->remove('create');
        $collection->add('testeEmail');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->tab('Geral');
            $form->with('Informações Gerais',[
                'class'       => 'col-md-12',
                'description' => 'Informações Gerais',
            ]);

                $form->add('host',  TextType::class, [
                    'label' => 'Host',
                    'required' =>  false,
                ]);

                $form->add('port',  NumberType::class, [
                    'label' => 'Porta',
                    'required' =>  false,
                ]);

                $form->add('username',  TextType::class, [
                    'label' => 'Username',
                    'required' =>  false ,
                ]);

                $form->add('email',  TextType::class, [
                    'label' => 'Email',
                    'required' =>  false ,
                ]);

                $form->add('typeSecurity',  ChoiceType::class, [
                    'label' => 'Tipo de Segurança',
                    'required' =>  false ,
                    'choices'  => [
                        'SSL' => 'SSL',
                        'TLS' => 'TLS',
                        'Sem Segurança' => false,
                    ],
                ]);

                $form->add('password',  PasswordType::class, [
                    'label' => 'Senha',
                    'required' =>  false ,
                ]);

            $form->end();
        $form->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {}

    protected function configureListFields(ListMapper $list): void
    {

    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->tab('Geral');
        $show->with('Informações Gerais', [
            'class'       => 'col-md-12',
            'box_class'   => 'box box-solid box-primary',
            'description' => 'Informações Gerais',
        ]);

            $show->add('id', null, [
                'label' => 'Id',
            ]);

            $show->add('host', null, [
                'label' => 'Host',
            ]);

            $show->add('port', null, [
                'label' => 'Porta',
            ]);

            $show->add('username', null, [
                'label' => 'Username',
            ]);

            $show->add('email', null, [
                'label' => 'Email',
            ]);

            $show->add('typeSecurity', null, [
                'label' => 'Tipo de Segurança',
            ]);



        $show->end();
        $show->end();

    }
}