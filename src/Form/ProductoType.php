<?php

namespace App\Form;

use App\Entity\Producto;
use App\Service\DoctrineMetadataService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ProductoType extends AbstractType
{

    private DoctrineMetadataService $doctrineMetadataService;

    public function __construct(DoctrineMetadataService $doctrineMetadataService)
    {
        $this->doctrineMetadataService = $doctrineMetadataService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tituloMaxLength = $this->doctrineMetadataService->getFieldMaxLength(Producto::class, 'titulo');
        $descripcionMaxLength = $this->doctrineMetadataService->getFieldMaxLength(Producto::class, 'descripcion');

        $builder
            ->add('titulo', TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => $tituloMaxLength, //Set the maxlength property for frontend
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z]+$/',
                        'message' => 'Title field must be a string.',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => $tituloMaxLength,
                    ]),
                ],
            ])
            ->add('descripcion', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => $descripcionMaxLength, //Set the maxlength property for frontend
                ]
                ,
                'constraints' => [
                    new Length([
                        'min' => 1,
                        'max' => $descripcionMaxLength,
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Za-z]+$/',
                    ])
                ]
            ])
            ->add('codigo', TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                ]
                ,
                'constraints' => [
                    new Regex([
                        //Necesito que la regex sea solo numérico
                        'pattern' => '/^[0-9]+$/',
                    ]),
                    new Length([
                        'min' => 1,
                        'max' => 5, //Hasta 5 digitos
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
