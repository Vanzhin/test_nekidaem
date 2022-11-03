<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(User::class, 1000, function (User $user) use ($manager) {
            $user
                ->setName($this->faker->firstName())
                ->setEmail($this->faker->email())
                ->setPassword($this->faker->password())
                ->setCreatedAt($this->faker->dateTimeBetween('-30 days'))
            ->setBlog($this->getRandomReferences(Blog::class, true));
            if ($this->faker->boolean(10)) {
                $user->addBlog($this->getRandomReferences(Blog::class));
            }
        });

    }
    public function getDependencies(): array
    {
        return [
            BlogFixtures::class

        ];
    }
}
