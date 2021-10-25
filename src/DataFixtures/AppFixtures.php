<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\BlogPost;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory;
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
       $this->passwordEncoder = $passwordEncoder;
       $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadBlogPost($manager);
        $this->loadComment($manager);
    }

    public function loadBlogPost(ObjectManager $manager): void
    {
        $user = $this->getReference('user_admin');
        for ($i=0; $i < 100; $i++) { 
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30));
            $post->setPublished($this->faker->dateTimeThisYear);
            $post->setContent($this->faker->realText());
            $post->setAuthor($user);
            $post->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $post);
            
            $manager->persist($post);
        }

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

    public function loadComment(ObjectManager $manager): void
    {
        for ($i=0; $i < 100; $i++) { 
            for ($j=0; $j < rand(1, 10); $j++) { 
                $comment = new Comment();
                $comment->setAuthor($this->getReference('user_admin'));
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
