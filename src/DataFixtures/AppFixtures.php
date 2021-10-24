<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\BlogPost;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
       $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);

        $this->loadBlogPost($manager);
    }

    public function loadBlogPost(ObjectManager $manager): void
    {
        $user = $this->getReference('user_admin');
        $post = new BlogPost();
        $post->setTitle('Post added from fixture seeding');
        $post->setPublished(new \DateTime());
        $post->setContent('This is a post created by fixture. Fixture is similar to seeder in Laravel');
        $post->setAuthor($user);
        $post->setSlug('post-added-from-fixture-seeding');
        
        $manager->persist($post);

        $post = new BlogPost();
        $post->setTitle('Another Post added from fixture seeding');
        $post->setPublished(new \DateTime());
        $post->setContent('This is a post created by fixture. Fixture is similar to seeder in Laravel');
        $post->setAuthor($user);
        $post->setSlug('another-post-added-from-fixture-seeding');
        $manager->persist($post);

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('generah');
        $user->setName('Generah Ben');
        $user->setEmail('ben@generah.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));

        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();

    }
}
