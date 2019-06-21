<?php

namespace App\Controller\Api;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use App\Serializer\UserPublicationArraySerializer;
use App\Service\Jsonizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserPublicationController extends AbstractController
{
    /**
     * @Route("/api/users/publications/", name="api_user_publication_list", options={"expose": true})
     *
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param PublicationRepository          $publicationRepository
     * @param UserPublicationArraySerializer $userPublicationArraySerializer
     *
     * @return JsonResponse
     */
    public function list(
        PublicationRepository $publicationRepository,
        UserPublicationArraySerializer $userPublicationArraySerializer
    ) {
        $publications = $publicationRepository->findBy(['author' => $this->getUser()]);
        return $this->json(['publications' => $userPublicationArraySerializer->listToArray($publications)]);
    }

    /**
     * @Route("/api/users/publications/{id}/delete", name="api_user_publication_delete", options={"expose": true})
     *
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param Publication $publication
     *
     * @return JsonResponse
     */
    public function remove(Publication $publication)
    {
        if ($this->getUser()->getId() !== $publication->getAuthor()->getId()) {
            return $this->json(['data' => ['success' => 0, 'message' => 'Ce publication ne vous appartient pas']], Response::HTTP_FORBIDDEN);
        }

        if ($publication->getStatus() !== Publication::STATUS_DRAFT) {
            return $this->json(['data' => ['success' => 0, 'message' => 'Vous ne pouvez pas supprimer une publication en ligne ou en review']], Response::HTTP_FORBIDDEN);
        }

        $this->getDoctrine()->getManager()->remove($publication);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['data' => ['success' => 1]]);
    }

    /**
     * @Route("/api/users/publications/{id}", name="api_user_publication_show", options={"expose": true})
     *
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param Publication                    $publication
     * @param UserPublicationArraySerializer $userPublicationArraySerializer
     *
     * @return object|JsonResponse
     */
    public function show(Publication $publication, UserPublicationArraySerializer $userPublicationArraySerializer)
    {
        if ($this->getUser()->getId() !== $publication->getAuthor()->getId()) {
            return $this->json(['data' => ['success' => 0, 'message' => 'Ce publication ne vous appartient pas']], Response::HTTP_FORBIDDEN);
        }

        return $this->json(['data' => ['publication' => $userPublicationArraySerializer->toArray($publication, true)]]);
    }

    /**
     * @Route("/api/users/publications/{id}/save", name="api_user_publication_save", options={"expose": true}, methods={"POST"})
     *
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     *
     * @param Publication        $publication
     * @param Request            $request
     * @param Jsonizer           $jsonizer
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function save(Publication $publication, Request $request, Jsonizer $jsonizer, ValidatorInterface $validator)
    {
        $data = $jsonizer->decodeRequest($request);

        $publication->setTitle($data['title']);
        $publication->setShortDescription($data['short_description']);
        $publication->setContent($data['content']);

        $errors = $validator->validate($publication);

        if (count($errors) > 0) {
            return $this->json(['data' => ['errors' => $errors]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->json(['data' => ['success' => 1]]);
    }
}