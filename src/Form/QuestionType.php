<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Question;


class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('question');

        if (isset($options['include_publication_date']) && $options['include_publication_date'] === true) {
            $builder->add('publicationDate', DateTimeType::class);
        }

        $builder
            ->add('validated')
             ->add('tags', TextType::class, [
                'required' => false,
                'mapped' => false, // Ne pas mapper ce champ directement à l'entité
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'include_publication_date' => false,
        ]);
    }
}