<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'Usuario' => 'ROLE_USER',
                    'Administrador' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-check'],
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Activo',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
                'label_attr' => ['class' => 'form-check-label'],
            ]);

        // La contraseña es requerida para nuevos usuarios, pero opcional para edición
        $passwordConstraints = [
            new Length([
                'min' => 6,
                'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres',
                'max' => 4096,
            ]),
        ];
        
        if (!$options['is_edit']) {
            $passwordConstraints[] = new NotBlank([
                'message' => 'Por favor ingresa una contraseña',
            ]);
        }
        
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'required' => !$options['is_edit'],
            'attr' => ['autocomplete' => 'new-password'],
            'first_options' => [
                'label' => 'Contraseña',
                'constraints' => $passwordConstraints,
                'attr' => ['class' => 'form-control'],
            ],
            'second_options' => [
                'label' => 'Repetir Contraseña',
                'attr' => ['class' => 'form-control'],
            ],
            'invalid_message' => 'Las contraseñas no coinciden.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}