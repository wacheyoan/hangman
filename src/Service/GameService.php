<?php


namespace App\Service;


use App\Entity\Game;
use App\Repository\GameRepository;
use Faker\Factory;
use Symfony\Component\Security\Core\Security;

class GameService
{
    public function __construct(
        public GameRepository $gameRepository,
        public Security $security
    ) {}

    public function createNewGame(){
        $faker = Factory::create('fr_FR');

        $game = new Game();
        $game->setUser($this->security->getUser());
        $game->setWord($faker->word);

        $this->gameRepository->persist($game);
        $this->gameRepository->flush($game);

        return $game;
    }

    public function handleStatus(Game $game){
        if(count($game->getGuessed()) >= 10){
            $game->setStatus('Lost');
        }else{
            $len = mb_strlen($game->getWord());
            $exact = true;

            for ($i=0; $i < $len ; $i++) {
                if(!in_array(mb_strtoupper($game->getWord()[$i]), $game->getGuessed(), true)){
                    $exact = false;
                }
            }

            if($exact){$game->setStatus('Won');}
        }

        $this->gameRepository->persist($game);
        $this->gameRepository->flush($game);
    }
}