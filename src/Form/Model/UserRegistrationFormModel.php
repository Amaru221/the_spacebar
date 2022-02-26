<?php

namespace App\Form\Model;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @UniqueEntity(
 *     fields={"email"},
 *     message="I think you're already registered!"
 * )
 */
class UserRegistrationFormModel
{

    //Nuestro FormType tiene 3 campos
    /**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Choose a password!")
     * @Assert\Length(min="5", minMessage="Come on, you can think of a password longer than tah!")
     */
    public $plainPassword;

    /**
     * @Assert\IsTrue(message="I know, it's silly, but you must agree to our terms")
     */
    public $agreeTerms;

}