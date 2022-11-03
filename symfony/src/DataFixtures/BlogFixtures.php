<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Persistence\ObjectManager;

class BlogFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Blog::class, 1000, function (Blog $blog) use ($manager) {
            $blog
                ->setTitle($this->faker->words(2,true))
                ->setCreatedAt($this->faker->dateTimeBetween('-30 days'));

        });

    }


}
