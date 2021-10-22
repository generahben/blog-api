<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Serializable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @Route("/{page}", name="blog_list", defaults={"page"=1}, requirements={"page": "\d+"})
     */
    public function list($page = 1, Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $repo = $this->getDoctrine()->getRepository(BlogPost::class);
        $posts = $repo->findAll();

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' => array_map(function(BlogPost $post){
                return $this->generateUrl('blog_by_slug', ['slug' => $post->getSlug()]);
            }, $posts)
        ]);
    }

    /**
     * @Route(
     *          "/post/{id}",
     *          name="blog_by_id",
     *          requirements={"id"="\d+"}
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function post(int $id): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function slug(string $slug): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' => $slug])
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     * @param Request $request
     */
    public function add(Request $request)
    {
        /**@var Serializable $serializer */
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }


}