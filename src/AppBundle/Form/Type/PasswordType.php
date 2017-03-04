<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Group;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('password', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tags', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('groups', EntityType::class, [
                'label' => 'Groups And Users',
                'class' => Group::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('g')
                        ->leftJoin('g.users', 'u')
                        ->where('u.id = :userId OR g.private = TRUE OR IDENTITY(g.owner) = :userId')
                        ->orderBy('g.private', 'ASC')
                        ->addOrderBy('LOWER(g.name)', 'ASC')
                        ->setParameter('userId', $options['user']->getId());
                },
                'group_by' => function ($val, $key, $index) {
                    return $val->isPrivate() ? 'Users' : 'Groups';
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Password',
            'user' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_password';
    }
}
