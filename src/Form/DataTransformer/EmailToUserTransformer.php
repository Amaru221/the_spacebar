<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmailToUserTransformer implements DataTransformerInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {

        $this->userRepository = $userRepository;
    }


    public function transform($value)
    {
        if(null === $value){
            return '';
        }

        if(!$value instanceof User){
            throw new \LogicException('The UserSelectextType can only be used with User object!');
        }

        return $value->getEmail();
    }

    public function reverseTransform($value)
    {
        if(!$value){
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $value]);

        if(!$user){
            throw new TransformationFailedException(sprintf(
                'No user found with email "%s"',
                $value
            ));
        }

        return $user;
    }

}