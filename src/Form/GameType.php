<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Game $game */
        $game = $builder->getData();
        $ranged = array_combine($range = range("A","Z"),$range);

        $builder
            ->add('letters',ChoiceType::class,[
                'mapped' => false,
                'choices' => $game->getGuessed() ? array_diff($ranged,$game->getGuessed()) : $ranged,
                'multiple' => false,
                'expanded' => true,
                'label' => false,
                'disabled' => ($game->getGuessed() && count($game->getGuessed()) >= $options['tries']) ||  $game->getStatus() !== "pending"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'tries' => null
        ]);
    }
}
