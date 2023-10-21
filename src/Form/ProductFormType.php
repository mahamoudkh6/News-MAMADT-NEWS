<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\CategoryShop;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichImageType;

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
                'required' => false,
            ])

            ->add('attachment', TextType::class)
            ->add('attchmentFile', VichImageType::class, [
                        'required' => false,
                        'allow_delete' => true,
                        'delete_label' => 'Supprimer',
                        //'download_label' => '...',
                        'download_uri' => true,
                        'attachment_uri' => true,
                        //'imagine_pattern' => '...',
                        'asset_helper' => true,
                    ])

                    ->add('category', EntityType::class, [
                        'class' => CategoryShop::class
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






