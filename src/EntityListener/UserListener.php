<?php

namespace App\EntityListener;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserListener 
{
    private UserPasswordHasherInterface  $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }

    // Doesnt work for the moment 
    // public function preUpdate(User $user)
    // {
    //     $this->encodePassword($user);
    // }

    /**
     * Encode password based on plainPassword
     *
     * @param User $user
     * @return void
     */
    public function encodePassword(User $user)
    {
        if($user->getPlainPassword() === null)
        {
            return ;
        }
        $user->setPassword(
            $this->hasher->hashPassword($user,$user->getPlainPassword())
        );
        $user->setPlainPassword(null);
    }
}

