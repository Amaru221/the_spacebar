<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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
        /** @var Article|null $article */
        $article = $options['data'] ?? null;
        //dd($article);
        $isEdit = $article && $article->getId();

        $location = $article ? $article->getLocation() : null;

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

            ->add('location', ChoiceType::class, [
                'placeholder' => 'Chooose a location',
                'choices' => [
                    'The Solar System' => 'solar_system',
                    'Near a star' => 'star',
                    'Interstellar Space' => 'interstellar_space'
                ],
                'required' => false,
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event){
                /** @var Article $data */
                $data = $event->getData();
                if(!$data){
                    return;
                }

                $this->setupSpecificLocationNameField(
                    $event->getForm(),
                    $data->getLocation()
                );
            }
        );

        if($options['include_published_at']){
            $builder
                ->add('publishedAt', null, [
                    'widget' => 'single_text'
                ]);

        }

        $builder->get('location')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event){
                $form = $event->getForm();
                $this->setupSpecificLocationNameField(
                    $form->getParent(),
                    $form->getData()

                );
            }

        );

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

    private function getLocationNameChoices(string $location)
    {
        $planets = [
            'Mercury',
            'Venus',
            'Earth',
            'Mars',
            'Jupiter',
            'Saturn',
            'Uranus',
            'Neptune',
        ];

        $stars = [
            'Polaris',
            'Sirius',
            'Alpha Centauari A',
            'Alpha Centauari B',
            'Betelgeuse',
            'Rigel',
            'Other'
        ];

        $locationNameChoices = [
            'solar_system' => array_combine($planets, $planets),
            'star' => array_combine($stars, $stars),
            'interstellar_space' => null,
        ];

        return $locationNameChoices[$location] ?? null;
    }

    private function setupSpecificLocationNameField(FormInterface $form, ?string $location){
        if($location === null){
            $form->remove('specificLocationName');
            return;
        }

        $choices = $this->getLocationNameChoices($location);
        if(null === $choices){
            $form->remove('specificLocationName');
            return;
        }

        $form->add('specificLocationName', ChoiceType::class, [
            'placeholder' => 'Where exactly?',
            'choices' => $choices,
            'required' => false,
        ]);
    }

}