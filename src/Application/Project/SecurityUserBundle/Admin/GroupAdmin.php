<?php
namespace App\Application\Project\SecurityUserBundle\Admin;

use App\Application\Project\ContentBundle\Admin\Base\BaseAdmin;
use App\Application\Project\SecurityUserBundle\Entity\Group;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class GroupAdmin extends BaseAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof Group ? $object->getId() . ' - ' . $object->getName() : '';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);

        $collection->add('listAllRoles');
    }

    /** @throws \ReflectionException */
    protected function configureFormFields(FormMapper $form): void
    {
        $apiRoles = $this->apiACL->getApiRoles();
        $webRoles = $this->webACL->getWebRoles();

        $choicesApiRoles = $choicesWebRoles = [];

        foreach ($apiRoles as $role)
            $choicesApiRoles[] = [ $role => $role ];

        foreach ($webRoles as $role)
            $choicesWebRoles[] = [ $role => $role ];

        $form->tab('Geral');
            $form->with('Informações Do Grupo');

                $form->add('name', TextType::class,[
                    'label' => 'Nome do Grupo:',
                    'required' => true,

                ]);

                $form->add('description', TextareaType::class,[
                    'label' => 'Descrição do Grupo:',
                    'required' => false,

                ]);

            $form->end();
        $form->end();

        $form->tab('Módulos Api');
            $form->with('Permissões Api');

                $form->add('apiRoles', ChoiceType::class, [
                    'label' => ' ',
                    'required' => false,
                    'multiple' => true,
                    'expanded'=> false,
                    'choices' => $choicesApiRoles,
                    'attr' => [
                        'class' => 'div-select-api-roles',
                        'style' => 'display:block;'
                    ],
                ]);

            $form->end();
        $form->end();


        $form->tab('Módulos Web');
            $form->with('Permissões Web');

            $form->add('webRoles', ChoiceType::class, [
                'label' => ' ',
                'required' => false,
                'multiple' => true,
                'expanded'=> false,
                'choices' => $choicesWebRoles,
                'attr' => [
                    'class' => 'div-select-web-roles',
                    'style' => 'display:block;'
                ],
            ]);

            $form->end();
        $form->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('description');
        //$datagrid->add('adminRoles');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name');
        $list->addIdentifier('description');
        $list->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
            'actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]
        ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name', null,[
            'label' => 'Nome do Grupo:',
        ]);
        $show->add('description', null,[
            'label' => 'Descrição:',
        ]);
        $show->add('roles', null,[
            'label' => 'Permissões:',
        ]);
    }
}


