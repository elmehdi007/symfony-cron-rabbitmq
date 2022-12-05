<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use \Symfony\Component\HttpFoundation\Response;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ArticleController extends AbstractController
{

    /**
     *
     */
    #[Route('/admin/articles', name: 'app_articles')]
    public function index(): Response
    {
        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/admin/articles/listing', name: 'listingArticles', methods: ['GET'])]
    public function listing(ArticleRepository $articleRepository, SerializerInterface $serializer,
                            Request $request,
                            Security $security
                            ): JsonResponse
    {

        $requestInfo = $request->get("sort",[['field'=>'id','dir'=>'desc']])[0]??['field'=>'id','dir'=>'desc'];
        $articelList = $articleRepository->search($request->get("take",10),$request->get("skip",1),$requestInfo["field"]??null,$requestInfo["dir"]??null);
        $jsonarticelList = $serializer->serialize(['data'=>$articelList, 'totalRows'=>$articleRepository->count([]), 'user_can_delete'=> true], 'json');

        //dd($security->getUser()->getRoles(), $this->isGranted('ROLE_ADMIN'), $this->denyAccessUnlessGranted('ROLE_ADMIN'));
        return new JsonResponse($jsonarticelList, Response::HTTP_OK, [], true);
    }

    /**
     *remove article by id. 
     * Require ROLE_ADMIN only for this action
     *
     * @param ArticleRepository $articleRepository
     * @param EntityManagerInterface $entityManagerInterface
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/admin/articles/{id}', name: 'deleteArticle', methods: ['DELETE'])]
    public function delete(Article $article, ArticleRepository $articleRepository): Response
    {
        $response = null;

        if($article){
            $articleRepository->remove($article, true);

            $response = new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        else $response = new JsonResponse(null, Response::HTTP_NOT_FOUND);
             
        return $response;
    }
}
