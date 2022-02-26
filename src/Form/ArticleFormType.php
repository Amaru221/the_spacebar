<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class ArticleFormType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository){

        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Si existe $options[data] lo instanciamos en la variable, si no lo estanciamos a null
        $article = $options['data'] ?? null;
        //dd($article);
        $isEdit = $article && $article->getId();

        $builder
            ->add('title', TextType::class, [
                'help' => 'Choose something catchy!',
            ])
            ->add('content', null, [
                'rows' => 15,
            ])
            ->add('author', UserSelectTextType::class, [
                'invalid_message' => 'this message win to the ConfiguredOptions of UserSelectTextType!',
                //si el usuario deshabilita esta opcion, nuestro formulario desestimará los datos obtenidos
                'disabled' => $isEdit
            ])
        ;

        if($options['include_published_at']){
            $builder
                ->add('publishedAt', null, [
                    'widget' => 'single_text'
                ]);

        }
            /*->add('author', EntityType::class,[
                'class' => User::class,
                'choice_label' => function(User $user){
                    return sprintf('(%d) %s', $user->getId(), $user->getEmail());
                },
                'placeholder' => 'Choose an author',
                'choices' => $this->userRepository
                ->findAllEmailAlphabetical(),

                'invalid_message' => 'Symfony is too smart for your hacking'
                ])*/

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Article::class,
            'include_published_at' => false,
        ]);
    }

}