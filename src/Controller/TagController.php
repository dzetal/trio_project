<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;

#[Route('/api/tag')]
class TagController extends AbstractController
{
    #[Route('/', name: 'api_tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        return $this->json($tagRepository->findAll(), 200, [], ['groups' => 'simple-tag']);
    }

    #[Route('/new', name: 'api_tag_new', methods: ['POST'])]
    public function new(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag();
        $jsonData = $request->getContent();
        //$form = $this->createForm(TagType::class, $tag);
        //$form->submit($data);
        $serializer->deserialize($jsonData, Tag::class, 'json', [
            'object_to_populate' => $tag,
        ]);

        $violations = $validator->validate($tag);

        if (!$violations->count()) {
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->json(["status" => "OK", "tag" => $tag->getName()]);
        }
        
        return $this->json(["status" => "NOT OK", "form" => $jsonData, "violations" => $violations]);
    }

    #[Route('/{id}', name: 'api_tag_show', methods: ['GET'])]
    public function show(Tag $tag, SerializerInterface $serializer): Response
    {
        $jsonTag = $serializer->serialize(
            $tag,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::GROUPS => 'complete-tag']
        );

        return new Response($jsonTag);
    }

}