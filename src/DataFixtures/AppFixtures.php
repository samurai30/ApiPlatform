<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Posts;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {

        $this->encoder = $encoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadUsers($manager);
        $this->loadPost($manager);
        $this->loadComments($manager);

        $manager->flush();
    }

    public function loadPost(ObjectManager $manager){

        $user = $this->getReference('user_admin');

        for ($i = 0; $i<50; $i++){
            $post = new Posts();
            $post->setTitle($this->faker->realText(30));
            $post->setPublished($this->faker->dateTimeThisYear);
            $post->setAuthor($user);
            $post->setContent($this->faker->realText());
            $this->addReference("post_$i",$post);

            $manager->persist($post);
        }

        $manager->flush();

    }

    public function loadComments(ObjectManager $manager){

        for ($i = 0; $i<50; $i++){

            for($j = 0; $j< rand(1,10);$j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($this->getReference('user_admin'));
                $comment->setPost($this->getReference("post_$i"));
                $manager->persist($comment);

            }
        }

        $manager->flush();


            }

    public function loadUsers(ObjectManager $manager){
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('suyog15122@gmail.com');
        $user->setName("suyog mishal");
        $user->setPassword($this->encoder->encodePassword($user,'suyog@100'));

        $this->addReference('user_admin', $user);

        $manager->persist($user);
        $manager->flush();
    }

}
