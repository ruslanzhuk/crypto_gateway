<?php

namespace App\Form;

use App\Entity\CryptoCurrency;
use App\Entity\FiatCurrency;
use App\Entity\PaymentConfirmation;
use App\Entity\PaymentStatus;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expiredAt', null, [
                'widget' => 'single_text',
            ])
            ->add('isAutomatic')

            // Статуси
            ->add('mainStatus', EntityType::class, [
                'class' => PaymentStatus::class,
                'choice_label' => 'code',
            ])
            ->add('manualStatus', EntityType::class, [
                'class' => PaymentStatus::class,
                'choice_label' => 'code',
            ])
            ->add('automaticStatus', EntityType::class, [
                'class' => PaymentStatus::class,
                'choice_label' => 'code',
            ])
            ->add('confirmation', EntityType::class, [
                'class' => PaymentConfirmation::class,
                'choice_label' => 'id',
                'required' => false,
                'placeholder' => 'Не підтверджено',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
