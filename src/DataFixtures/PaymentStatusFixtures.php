<?php

namespace App\DataFixtures;

use App\Entity\PaymentStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            ['code' => 'PND', 'name' => 'Pending'],
            ['code' => 'PRC', 'name' => 'Processing'],
            ['code' => 'CMP', 'name' => 'Completed'],
            ['code' => 'FLD', 'name' => 'Failed'],
            ['code' => 'CNL', 'name' => 'Cancelled'],
            ['code' => 'RFD', 'name' => 'Refunded'],
        ];

        foreach ($statuses as $data) {
            $status = new PaymentStatus();
            $status->setCode($data['code']);
            $status->setName($data['name']);

            $manager->persist($status);
        }

        $manager->flush();
    }
}