<?php
namespace App\Application\Project\SecurityAdminBundle\Admin;

use App\Application\Project\ContentBundle\Admin\Base\BaseAdmin;
use App\Application\Project\SecurityAdminBundle\Entity\GroupAdmin;
use App\Application\Project\SecurityAdminBundle\Entity\UserAdmin;
use ReflectionException;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/** Components Form */
final class UserAdminAdmin extends BaseAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof UserAdmin ? $object->getId()  . ' - ' . $object->getname() : '';
    }


    /** @throws ReflectionException */
    protected function configureFormFields(FormMapper $form): void
    {
        $isEdit = $this->isCurrentRoute('edit') == 'edit';
        $passwordRequired = ! $isEdit;

        $rolesAdmin = $this->adminACL->getAdminRoles();
        $choicesRoles = [];
        foreach ($rolesAdmin as $role)
            $choicesRoles[] = [ $role => $role ];


        /** START TAB 1 */
        $form->tab('Geral');

            $form->with('Informações Gerais', ['class' => 'col-md-8']);

                $form->add('name', TextType::class, [
                    'label' => 'Nome:',
                    'required' => true,
                ]);

                $form->add('email', TextType::class, [
                    'label' => 'E-mail:',
                    'required' => true,
                ]);

                $form->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array(
                        'label' => 'Senha',
                    ),
                    'second_options' => array(
                        'label' => 'Confirmar Senha',
                    ),
                    'required' => true,
                ));

                $passwordOptions = array(
                    'type' => PasswordType::class,
                    'required' => $passwordRequired,
                    'invalid_message' => 'Os campos de senha devem ser iguais.',
                    'first_options' => array(
                        'label' => 'Senha'
                    ),
                    'second_options' => array(
                        'label' => 'Confirmar senha'
                    )
                );

                if ($passwordRequired) {
                    $passwordOptions['constraints'] = array(
                        new NotBlank(array(
                            'message' => 'É necessário informar uma senha.'
                        )),
                        new Length(array(
                            'min' => 5,
                            'minMessage' => 'A senha deve ter mais de 5 caracteres.'
                        ))
                    );
                }

                $form->add('password', RepeatedType::class,
                    $passwordOptions
                );

            $form->end();

            $form->with('Grupos do Usuário', ['class' => 'col-md-4']);

                $form->add('groups', ModelType::class,[
                    'class' => GroupAdmin::class,
                    'property' => 'name',
                    'label' => 'Grupos',
                    'required' => false,
                    'expanded' => true,
                    'btn_add' => false,
                    'multiple' => true,
                ]);

            $form->end();

        $form->end();
        /** END TAB 1 */

        /** START TAB 2 */
        $form->tab('Módulos');
            $form->with('Permissões de Acesso');

                $form->add('adminRoles', ChoiceType::class, [
                    'label' => ' ',
                    'required' => false,
                    'multiple' => true,
                    'expanded'=> false,
                    'choices' => $choicesRoles,
                    'attr' => [
                        'class' => 'div-select-admin-roles',
                        'style' => 'display:none;'
                    ],
                ]);

            $form->end();
        $form->end();
        /** END TAB 2 */

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('email');
        $datagrid->add('roles');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name');
        $list->addIdentifier('email');
        $list->add('groups', null, [
            'label' => 'Grupos',
            'associated_property' => 'name',
        ]);
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
        $show->add('name');
        $show->add('email');
        $show->add('groups', null, [
            'associated_property' => 'name',
            'label' => 'Grupo:',
        ]);
        $show->add('roles', 'array', [
            'label' =>  'role'
        ]);
    }

    /** @param UserAdmin $object */
    protected function prePersist(object $object): void
    {
        $object->setPassword( $this->passwordHasher->hashPassword( $object, $object->getPassword() ) );
    }

    /** @param UserAdmin $object */
    protected function preUpdate(object $object): void
    {
        if($this->passwordHasher->needsRehash($object))
            $object->setPassword( $this->passwordHasher->hashPassword( $object, $object->getPassword() ) );
    }

}