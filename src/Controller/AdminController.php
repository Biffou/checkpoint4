<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/", name="admin_index")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index(): Response
    {
        return $this->render("admin/index.html.twig");
    }

    /** USERS */

    /**
     * @Route("/users/", name="admin_user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexUsers(UserRepository $userRepository, Request $request): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/{id}", name="admin_user_show", methods={"GET"})
     * @param User $user
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showUser(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editUser(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $emailForm = $this->createForm(EditEmailType::class, $user);
        $emailForm->handleRequest($request);

        $passForm = $this->createForm(EditPasswordType::class, $user);
        $passForm->handleRequest($request);

        if ($passForm->isSubmitted() && $passForm->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $passForm->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Votre mot de passe a été modifié avec succès");

            return $this->redirectToRoute('admin_user_index');
        }

        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Votre email a été modifié avec succès");

            return $this->redirectToRoute('admin_user_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // Set the pictureFile property to null to avoid serialization error
            $user->setPictureFile(null);

            $this->addFlash('success', "Votre profil a été modifié avec succès");

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'emailForm' => $emailForm->createView(),
            'passForm' => $passForm->createView(),
            'form' => $form->createView(),
        ]);
    }

    /** Categories */

    /**
     * @Route("/category/", name="admin_category_index", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/{id}", name="admin_category_show", methods={"GET"})
     * @param Category $category
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showCategory(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/category/{id}/edit", name="admin_category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editCategory(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}", name="admin_category_delete", methods={"DELETE"})
     * @param Request $request
     * @param Category $category
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteCategory(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_category_index');
    }
}

