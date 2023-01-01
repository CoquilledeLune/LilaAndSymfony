<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
      $programs = $programRepository->findAll();

      return $this->render('program/index.html.twig', 
        ['programs' => $programs]
      );
    }

    #[Route('/new', name:'new')]
  public function new(Request $request, ProgramRepository $programRepository): Response
    {
      $program = new Program(); 
      $form = $this->createForm(ProgramType::class, $program); 
      // Get data from HTTP request
      $form->handleRequest($request);
      // Was the form submitted ?
      if ($form->isSubmitted()) {
        $programRepository->save($program, true); 
        // Redirect to categories list
        return $this->redirectToRoute('program_index');
      }
      return $this->renderForm('program/new.html.twig', [
        'form' => $form,
      ]);
    }

    #[Route('/{id}', requirements: ['id'=>'\d+'], methods: ['GET'], name: 'show')]
    public function show(Program $program): Response
    {
      return $this->render('program/show.html.twig', ['program' => $program]);   
    }

    #[Route('/{program}/season/{season}', 
    requirements: ['program'=> '\w+', 'season'=>'\w+'], methods: ['GET'], name: 'season_show')]
    public function showSeason(Program $program, Season $season): Response
    {
      return $this->render('program/season_show.html.twig', [
        'season' => $season, 
        'program' => $program,
      ]);
    }

    #[Route('/{program}/season/{season}/episode/{episode}',
    requirements: ['program'=>'\w+', 'season'=>'\w+', 'episode'=>'\d+'], methods: ['GET'], name: 'episode_show')]
    public function showEpisode(Episode $episode, Program $program, Season $season): Response
    {
      $episode = $season->getEpisodes();

      return $this->render('program/episode_show.html.twig', [
        'season'=>$season, 
        'program'=>$program, 
        'episodes'=> $episode,
      ]);
    }
}
