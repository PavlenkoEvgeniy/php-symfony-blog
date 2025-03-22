<?php

namespace App\Controller\Ui\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'ui_admin_category_index', methods: [Request::METHOD_GET])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('ui/admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/admin/category/new', name: 'ui_admin_category_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form     = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('ui_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ui/admin/category/new.html.twig', [
            'category' => $category,
            'form'     => $form,
        ]);
    }

    #[Route('/admin/category/show/{id}', name: 'ui_admin_category_show', methods: [Request::METHOD_GET])]
    public function show(Category $category): Response
    {
        return $this->render('ui/admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/admin/category/edit/{id}', name: 'ui_admin_category_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ui_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ui/admin/category/edit.html.twig', [
            'category' => $category,
            'form'     => $form,
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'ui_admin_category_delete', methods: [Request::METHOD_DELETE])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ui_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/category/trash', name: 'ui_admin_category_trash', methods: [Request::METHOD_GET])]
    public function trash(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        $entityManager->getFilters()->disable('softDeleteable');

        return $this->render('ui/admin/category/trash.html.twig', [
            'categories' => $categoryRepository->findDeletedCategories(),
        ]);
    }

    #[Route('/admin/category/restore/{id}', name: 'ui_admin_category_restore', methods: [Request::METHOD_GET])]
    public function restore(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $entityManager->getFilters()->disable('softDeleteable');

        $category = $categoryRepository->findOneById($id);

        if (!$category) {
            throw $this->createNotFoundException('Пост не найден.');
        }

        $category->setDeletedAt(null);

        $entityManager->flush();

        return $this->redirectToRoute('ui_admin_category_trash', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/category/truncate/{id}', name: 'ui_admin_category_truncate', methods: [Request::METHOD_DELETE])]
    public function truncate(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        if ($this->isCsrfTokenValid('truncate' . $id, $request->getPayload()->getString('_token'))) {
            $entityManager->getFilters()->disable('softDeleteable');

            $category = $categoryRepository->findOneById($id);

            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ui_admin_category_trash', [], Response::HTTP_SEE_OTHER);
    }
}
