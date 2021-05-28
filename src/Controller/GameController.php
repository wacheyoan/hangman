<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(Request $request,GameRepository $gameRepository){
        return $this->render('game/list.html.twig',[
            'games' => $gameRepository->findBy(['user' => $this->getUser()])
        ]);
    }

    #[Route('/play/{id?}','requirements={"id"="\d+"}', name: 'play')]
    public function index(Request $request,?Game $game,GameRepository $gameRepository,GameService $gameService): Response
    {
        if(!$game){
            $game = $gameService->createNewGame();
            return $this->redirectToRoute('play',['id' => $game->getId()]);
        }

        if($request->isXmlHttpRequest()){
            $game->setGuessed([$request->request->get('game')['letters']]);
            $gameService->handleStatus($game);
        }

        $form = $this->createForm(GameType::class,$game,['tries' => 10]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $gameRepository->persist($game);
            $gameRepository->flush($game);
        }

        return $this->render('game/index.html.twig',[
            'form' => $form->createView(),
            'game' => $game
        ]);
    }

    #[Route('/reset/{id}','requirements={"id"="\d+"}', name: 'reset')]
    public function resetGame(Game $game,GameRepository $gameRepository){
        $game->setStatus("pending");
        $game->setGuessed(null);

        $gameRepository->persist($game);
        $gameRepository->flush($game);

        return $this->redirectToRoute('play',[
            'id' => $game->getId()
        ]);
    }

}
