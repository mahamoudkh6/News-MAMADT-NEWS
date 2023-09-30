<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du produit',
            ])
            ->add('slug', CKEditorType::class, [
                'label' => 'slug du produit',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'contenu du produit',
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
            ])
            ->add('attachment', FileType::class, [
                'label' => 'Image du produit',
                'required' => false, // Permet de ne pas exiger une image à chaque mise à jour
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
