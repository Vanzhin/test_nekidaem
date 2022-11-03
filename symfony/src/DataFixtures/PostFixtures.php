<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends BaseFixtures implements DependentFixtureInterface
{

    function loadData(ObjectManager $manager)
    {
        $this->createMany(Post::class, 10000, function (Post $post) use ($manager) {
            $post
                ->setTitle($this->faker->words(2, true))
                ->setContent($this->faker->text(140))
                ->setCreatedAt($this->faker->dateTimeBetween('-30 days'))
            ->setBlog($this->getRandomReferences(Blog::class));
            if ($this->faker->boolean) {
                $post->addUsersRead($this->getRandomReferences(User::class));
            }

        });
    }

    public function getDependencies(): array
    {
        return [
            BlogFixtures::class,
            UserFixtures::class,

        ];
    }
}
