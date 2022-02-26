<?php

namespace App\Form;

use App\Form\DataTransformer\EmailToUserTransformer;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class UserSelectTextType extends AbstractType
{

    private $userRepository;
    private $router;

    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {

        $this->userRepository = $userRepository;
        $this->router = $router;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->addModelTransformer(new EmailToUserTransformer(
           $this->userRepository,
           $options['finder_callback']

       ));
    }


    public function getParent()
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Hmm, user not found!',
            'finder_callback' => function(UserRepository $userRepository, string $email){
                return $userRepository->findOneBy(['email' => $email]);
            },
            /*'attr' => [
                'class' => 'js-user-autocomplete',
                'data-autocomplete-url' => $this->router->generate('admin_utility_users'),
            ]*/
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Recogemos los atributos del field
        $attr = $view->vars['attr'];
        //si existen atributos añadimos un espacio
        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        //añadimos la clase js-user-autocomplete al final del string de class para usarla desde jquery
        $class.= 'js-user-autocomplete';

        //añadimos la nueva clase a los atributos del field
        $attr['class'] = $class;
        //añadimos el atributo data-autocomplete-url para que pueda ser llamado desde AJAX
        $attr['data-autocomplete-url'] = $this->router->generate('admin_utility_users');

        //añadimos los nuevos atributos a la vista
        $view->vars['attr'] = $attr;
    }


}