<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class PostController extends AbstractController
{
    #[Route('/admin/post', name: 'app_admin_post_index', methods: [Request::METHOD_GET])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('admin/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/admin/post/new', name: 'app_admin_post_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/admin/post/show/{id}', name: 'app_admin_post_show', methods: [Request::METHOD_GET])]
    public function show(Post $post): Response
    {
        return $this->render('admin/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/admin/post/edit/{id}', name: 'app_admin_post_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/admin/post/delete/{id}', name: 'app_admin_post_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/post/trash', name: 'app_admin_post_trash', methods: [Request::METHOD_GET])]
    public function trash(PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $entityManager->getFilters()->disable('softDeleteable');

        return $this->render('admin/post/trash.html.twig', [
            'posts' => $postRepository->findDeletedPosts(),
        ]);
    }

    #[Route('/admin/post/restore/{id}', name: 'app_admin_post_restore', methods: [Request::METHOD_GET])]
    public function restore(PostRepository $postRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $entityManager->getFilters()->disable('softDeleteable');

        $post = $postRepository->findOneById($id);

        if (!$post) {
            throw $this->createNotFoundException('Пост не найден.');
        }

        $post->setDeletedAt(null);
    
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_post_trash', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/post/truncate/{id}', name: 'app_admin_post_truncate', methods: [Request::METHOD_DELETE])]
    public function truncate(Request $request, PostRepository $postRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        if ($this->isCsrfTokenValid('truncate'.$id, $request->getPayload()->getString('_token'))) {
            $entityManager->getFilters()->disable('softDeleteable');

            $post = $postRepository->findOneById($id);

            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_post_trash', [], Response::HTTP_SEE_OTHER);
    }
}
