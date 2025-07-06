<?php
//
//namespace App\DataFixtures;
//
//use Doctrine\Bundle\FixturesBundle\Fixture;
//use Doctrine\Persistence\ObjectManager;
//use App\Entity\Network;
//
//class NetworkFixtures extends Fixture
//{
//    public function load(ObjectManager $manager): void
//    {
//        $networks = [
//            ['ref' => 'network_bitcoin', 'code' => 'BTC', 'name' => 'Bitcoin', 'explorer_url' => 'https://blockchain.com/btc'],
//            ['ref' => 'network_ethereum', 'code' => 'ETH', 'name' => 'Ethereum', 'explorer_url' => 'https://etherscan.io'],
//            ['ref' => 'network_solana', 'code' => 'SOL', 'name' => 'Solana', 'explorer_url' => 'https://solscan.io'],
//            ['ref' => 'network_tron', 'code' => 'TRX', 'name' => 'Tron', 'explorer_url' => 'https://tronscan.org'],
//            ['ref' => 'network_bsc', 'code' => 'BSC', 'name' => 'Binance Smart Chain', 'explorer_url' => 'https://bscscan.com'],
//            ['ref' => 'network_dogecoin', 'code' => 'DOGE', 'name' => 'Dogecoin', 'explorer_url' => 'https://dogechain.info'],
//        ];
//
//        foreach ($networks as $data) {
//            $network = new Network();
//            $network->setCode($data['code']);
//            $network->setName($data['name']);
//            $network->setExplorerUrl($data['explorer_url']);
//
//            $manager->persist($network);
//
//            // Встановлюємо посилання для використання в інших фікстурах
//            $this->addReference($data['ref'], $network);
//        }
//
//        $manager->flush();
//    }
//}


namespace App\DataFixtures;

use App\Entity\Network;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NetworkFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $networks = [
            ['ref' => 'network_bitcoin', 'code' => 'BTC', 'name' => 'Bitcoin', 'explorer_url' => 'https://blockchain.com/btc'],
            ['ref' => 'network_ethereum', 'code' => 'ETH', 'name' => 'Ethereum', 'explorer_url' => 'https://etherscan.io'],
            ['ref' => 'network_solana', 'code' => 'SOL', 'name' => 'Solana', 'explorer_url' => 'https://solscan.io'],
            ['ref' => 'network_tron', 'code' => 'TRX', 'name' => 'Tron', 'explorer_url' => 'https://tronscan.org'],
            ['ref' => 'network_bsc', 'code' => 'BSC', 'name' => 'Binance Smart Chain', 'explorer_url' => 'https://bscscan.com'],
            ['ref' => 'network_dogecoin', 'code' => 'DOGE', 'name' => 'Dogecoin', 'explorer_url' => 'https://dogechain.info'],
        ];

        foreach ($networks as $data) {
            $network = new Network();
            $network->setCode($data['code']);
            $network->setName($data['name']);
            $network->setExplorerUrl($data['explorer_url']);

            $manager->persist($network);

            // Збережемо посилання, щоб інші фікстури могли звернутись по ньому
            $this->addReference($data['ref'], $network);
        }

        $manager->flush();
    }
}