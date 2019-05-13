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

    public const USERS =[
      [
          "username"=>"samurai",
          "password" => "Suyog@100",
          "name" => "Suyog Mishal",
          "email" => "suyog15122@gmail.com"
      ],
        [
            "username"=>"domnico",
            "password" => "Suyog@100",
            "name" => "Domnic Fernandes",
            "email" => "dom@gmail.com"
        ],
        [
            "username"=>"marshall",
            "password" => "Suyog@100",
            "name" => "Marshall Fernandes-",
            "email" => "marshall@gmail.com"
        ],

    ];

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



        for ($i = 0; $i<50; $i++){
            $post = new Posts();
            $post->setTitle($this->faker->realText(30));
            $post->setPublished($this->faker->dateTimeThisYear);
            $authorRef = $this->getRandomUsers();
            $post->setAuthor($this->getReference($authorRef));
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
                $authorRef = $this->getRandomUsers();

                $comment->setAuthor($this->getReference($authorRef));
                $comment->setPost($this->getReference("post_$i"));
                $manager->persist($comment);

            }
        }

        $manager->flush();


            }

    public function loadUsers(ObjectManager $manager){

        foreach (self::USERS as $user){


            $userEntity = new User();
            $userEntity->setUsername($user['username']);
            $userEntity->setEmail($user['email']);
            $userEntity->setName($user['name']);
            $userEntity->setPassword($this->encoder->encodePassword($userEntity,$user['password']));

            $this->addReference('user_'.$user['username'], $userEntity);

            $manager->persist($userEntity);

        }


        $manager->flush();
    }

    /**
     * @return string
     */
    public function getRandomUsers(): string
    {
        return 'user_' . self::USERS[rand(0,2)]['username'];
    }

}
