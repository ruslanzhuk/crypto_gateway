<?php
//
//namespace App\DataFixtures;
//
//use App\Entity\CryptoCurrencyExists;
//use Doctrine\Bundle\FixturesBundle\Fixture;
//use Doctrine\Persistence\ObjectManager;
//use Doctrine\Common\DataFixtures\DependentFixtureInterface;
//
//class CryptoCurrencyFixtures extends Fixture implements DependentFixtureInterface
//{
//    public function load(ObjectManager $manager): void
//    {
//        $currencies = [
//            ['code' => 'BTC', 'name' => 'Bitcoin', 'network_ref' => 'network_bitcoin'],
//            ['code' => 'ETH', 'name' => 'Ethereum', 'network_ref' => 'network_ethereum'],
//            ['code' => 'USDT', 'name' => 'Tether (ERC20)', 'network_ref' => 'network_ethereum'],
//            ['code' => 'SOL', 'name' => 'Solana', 'network_ref' => 'network_solana'],
//            ['code' => 'TRX', 'name' => 'Tron', 'network_ref' => 'network_tron'],
//            ['code' => 'BNB', 'name' => 'Binance Coin', 'network_ref' => 'network_bsc'],
//            ['code' => 'DOGE', 'name' => 'Dogecoin', 'network_ref' => 'network_dogecoin'],
//        ];
//
//        foreach ($currencies as $data) {
//            $crypto = new CryptoCurrencyExists();
//            $crypto->setCode($data['code']);
//            $crypto->setName($data['name']);
//            $crypto->setNetwork($this->getReference($data['network_ref'])); // <- 1 аргумент, все ок
//
//            $manager->persist($crypto);
//        }
//
//        $manager->flush();
//    }
//
//    public function getDependencies(): array
//    {
//        return [
//            NetworkFixtures::class,
//        ];
//    }
//}


namespace App\DataFixtures;

use App\Entity\CryptoCurrency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CryptoCurrencyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cryptos = [
            ['code' => 'BTC', 'name' => 'Bitcoin', 'network_ref' => 'network_bitcoin'],
            ['code' => 'ETH', 'name' => 'Ethereum', 'network_ref' => 'network_ethereum'],
            ['code' => 'USDT', 'name' => 'Tether (ERC20)', 'network_ref' => 'network_ethereum'],
            ['code' => 'SOL', 'name' => 'Solana', 'network_ref' => 'network_solana'],
            ['code' => 'TRX', 'name' => 'Tron', 'network_ref' => 'network_tron'],
            ['code' => 'BNB', 'name' => 'Binance Coin', 'network_ref' => 'network_bsc'],
            ['code' => 'DOGE', 'name' => 'Dogecoin', 'network_ref' => 'network_dogecoin'],
        ];

        foreach ($cryptos as $data) {
            $crypto = new CryptoCurrency();
            $crypto->setCode($data['code']);
            $crypto->setName($data['name']);

            // Звертаємось до Network об'єкта через посилання
            $crypto->setNetwork($this->getReference($data['network_ref'], \App\Entity\Network::class));

            $manager->persist($crypto);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Гарантуємо, що спочатку завантажаться мережі
        return [NetworkFixtures::class];
    }
}
