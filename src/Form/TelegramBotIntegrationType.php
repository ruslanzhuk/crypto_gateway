<?php

namespace App\Form;

use App\Entity\TelegramBotIntegration;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TelegramBotIntegrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('botToken', TextType::class, [
	            'label' => 'Token вашого бота',
	            'mapped' => false,
            ]);
    }
}
