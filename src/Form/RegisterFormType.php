<?php

namespace App\Form;

use App\Entity\Auteur;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use App\Validator\Constraints\UniqueEmail;


class RegisterFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your name.']),
                new Regex([
                    'pattern' => '/^\D+$/',
                    'message' => 'Your name cannot contain numbers.',
                ]),
            ],
        ])

        ->add('lastname', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your surname.']),
                new Regex([
                    'pattern' => '/^\D+$/',
                    'message' => 'Your surname cannot contain numbers.',
                ]),
            ],
        ])

        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter your email.']),
                new Email(['message' => 'The email "{{ value }}" is not a valid email.']),
                new UniqueEmail(),
            ],
        ])

        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'required' => true,
            'first_options' => [
                'label' => 'Password',
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your password.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters.',
                    ]),
                ],
            ],
            'second_options' => ['label' => 'Repeat Password'],
        ]) ;
    }

   

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auteur::class,
        ]);
    }

}