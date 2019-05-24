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
          "email" => "suyog15122@gmail.com",
          "roles" => [User::ROLE_SUPERADMIN]
      ],
        [
            "username"=>"domnico",
            "password" => "Suyog@100",
            "name" => "Domnic Fernandes",
            "email" => "dom@gmail.com",
            "roles" => [User::ROLE_ADMIN]
        ],
        [
            "username"=>"marshall",
            "password" => "Suyog@100",
            "name" => "Marshall Fernandes-",
            "email" => "marshall@gmail.com",
            "roles" => [User::ROLE_WRITER]
        ],
        [
            "username"=>"tania",
            "password" => "Suyog@100",
            "name" => "Tania Salgaonkar",
            "email" => "tania@gmail.com",
            "roles" =>[User::ROLE_WRITER]
        ],
        [
            "username"=>"Rekha",
            "password" => "Suyog@100",
            "name" => "Rekha Naik",
            "email" => "rekha@gmail.com",
            "roles" =>[User::ROLE_EDITOR]
        ],
        [
            "username"=>"Manjiri",
            "password" => "Suyog@100",
            "name" => "Manjiri Mhambre",
            "email" => "Manju@gmail.com",
            "roles" =>[User::ROLE_COMMENTATOR]
        ]
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
            $authorRef = $this->getRandomUsers($post);
            $post->setAuthor($authorRef);
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
                $authorRef = $this->getRandomUsers($comment);

                $comment->setAuthor($authorRef);
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
            $userEntity->setRoles($user['roles']);
            $this->addReference('user_'.$user['username'], $userEntity);

            $manager->persist($userEntity);

        }


        $manager->flush();
    }

    /**
     * @param $entity
     * @return User
     */
    public function getRandomUsers($entity): User
    {
        $randomUser = self::USERS[rand(0,5)];

        if($entity instanceof Posts && !count(array_intersect($randomUser['roles'],[User::ROLE_SUPERADMIN,User::ROLE_ADMIN,User::ROLE_WRITER]))){
            return $this->getRandomUsers($entity);
        }
        if($entity instanceof Comment && !count(array_intersect($randomUser['roles'],
                [User::ROLE_SUPERADMIN,
                User::ROLE_ADMIN,
                User::ROLE_WRITER,
                User::ROLE_COMMENTATOR]
            ))){
            return $this->getRandomUsers($entity);
        }
        return $this->getReference('user_' . $randomUser['username']);
    }

}
