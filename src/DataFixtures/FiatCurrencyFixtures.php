<?php

namespace App\DataFixtures;

use App\Entity\FiatCurrency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FiatCurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $fiatCurrencies = [
            ['code' => 'USD', 'name' => 'United States Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound Sterling', 'symbol' => '£'],
        ];

        foreach ($fiatCurrencies as $data) {
            $fiat = new FiatCurrency();
            $fiat->setCode($data['code']);
            $fiat->setName($data['name']);
            $fiat->setSymbol($data['symbol']);

            $manager->persist($fiat);
        }

        $manager->flush();
    }
}