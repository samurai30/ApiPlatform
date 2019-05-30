<?php
/**
 * Created by PhpStorm.
 * User: Samurai
 * Date: 30-05-2019
 * Time: 09:25 PM
 */

namespace App\Controller;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    public function __construct(ValidatorInterface $validator,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager,JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->validator = $validator;
        $this->encoder = $encoder;
        $this->manager = $manager;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public function __invoke(User $data)
    {
       // $reset = new ResetPasswordAction();
        // $reset();
//        var_dump($data->getNewPassword(),
//            $data->getNewRetypePassword(),
//            $data->getOldPassword());die;


        $this->validator->validate($data);
        $data->setPassword(
            $this->encoder->encodePassword($data,$data->getNewPassword())
        );

        $this->manager->flush();

        $token = $this->JWTTokenManager->create($data);

        return new JsonResponse(['token'=> $token]);

   }

}