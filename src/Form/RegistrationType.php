<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => 2,
                    'maxlenght' => 180,
                ],
                'label' => 'Nom / Prénom',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2, 
                        'max' => 180, 
                        'maxMessage'=> 'Le nom et le prénom  ne doivent pas dépasser 180 caractères.', 
                        'minMessage'=> 'Le nom et le prénom doivent faire au minimum 2 caractères.']),
                    new Assert\NotBlank(['message' => 'Le nom et le prénom ne peuvent pas être vide.'])
                ]
            ])
            ->add('pseudo',TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => 2,
                    'maxlenght' => 50,
                ],
                'required' => false,
                'label' => 'Pseudo (facultatif)',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2, 
                        'max' => 50,
                        'maxMessage'=> 'Le pseudo ne doit pas dépasser 50 caractères.', 
                        'minMessage'=> 'Le pseudo doit faire au minimum 2 caractères.']),
                ]


            ])
            ->add('email',EmailType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => 2,
                    'maxlenght' => 180,
                ],
                'label' => 'Adresse email',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2, 
                        'max' => 180, 
                        'maxMessage'=> 'L\'email ne doit pas dépasser 180 caractères.', 
                        'minMessage'=> 'Le pseudo doit faire au minimum 2 caractères.']),
                    new Assert\NotBlank(['message' => 'L\'email n\'est pas valide.'])
                ]


            ])
            ->add('plainPassword',RepeatedType::class,[
                'type' => PasswordType::class,
                'attr' => [
                    'class' => 'form-control',
                ],
                'first_options' =>[
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.'
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'S\'inscrire',
                'attr'=>[
                    'class' => 'btn btn-primary mt-4',
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
