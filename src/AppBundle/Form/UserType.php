<?php

/*
 * This file is part of oc_todolist project
 *
 * @author: Sébastien CHOMY <sebastien.chomy@gmail.com>
 * @since 2018/05
 */

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                    'required'        => true,
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Tapez le mot de passe à nouveau'],
                ]
            )
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'required' => true,
                    'label'    => 'Roles',
                    'choices'  => [
                        'Utilisateur'    => 'ROLE_USER',
                        'Administrateur' => 'ROLE_ADMIN',
                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]
            )
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit'])
        ;
    }

    /**
     * @param FormEvent $formEvent
     */
    public function onPreSetData(FormEvent $formEvent)
    {
        /** @var User $user */
        $user = $formEvent->getData();
        // Conversion Array to string to ChoiceType (expanded=false, multiple=false)
        $user->setRoles(current($user->getRoles()));

        $formEvent->setData($user);
    }

    /**
     * @param FormEvent $formEvent
     */
    public function onPostSubmit(FormEvent $formEvent)
    {
        /** @var User $user */
        $user = $formEvent->getData();
        // Conversion string to Array to user->roles
        $user->setRoles((array) $user->getRoles());

        $formEvent->setData($user);
    }
}
