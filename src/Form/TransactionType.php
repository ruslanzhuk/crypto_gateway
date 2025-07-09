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
            ->add('txHash')
            ->add('amountFiat')
            ->add('amountCrypto')
            ->add('isAutomatic')
            ->add('receivedAmountFiat')
            ->add('receivedAmountCrypto')
            ->add('expiredAt', null, [
                'widget' => 'single_text',
            ])
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('wallet', EntityType::class, [
                'class' => Wallet::class,
                'choice_label' => 'id',
            ])
            ->add('fiatCurrency', EntityType::class, [
                'class' => FiatCurrency::class,
                'choice_label' => 'id',
            ])
            ->add('cryptoCurrency', EntityType::class, [
                'class' => CryptoCurrency::class,
                'choice_label' => 'id',
            ])
            ->add('mainStatus', EntityType::class, [
                'class' => PaymentStatus::class,
                'choice_label' => 'id',
            ])
            ->add('manualStatus', EntityType::class, [
                'class' => PaymentStatus::class,
                'choice_label' => 'id',
            ])
            ->add('automaticStatus', EntityType::class, [
                'class' => PaymentStatus::class,
                'choice_label' => 'id',
            ])
            ->add('confirmation', EntityType::class, [
                'class' => PaymentConfirmation::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
