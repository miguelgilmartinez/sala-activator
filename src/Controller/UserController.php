<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\RegistrationFormType;

#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController {

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response {
        return $this->render('user/index.html.twig', [
                    'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $req,
            UserPasswordHasherInterface $userPassHasher,
            EntityManagerInterface $entityMngr): Response {
        $user = new User();
        try {
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($req);
            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                        $userPassHasher->hashPassword($user,
                                $form->get('plainPassword')->getData()));
                $entityMngr->persist($user);
                $entityMngr->flush();
                $this->addFlash("success", "Usuario creado");
                // Redirigir al login después del registro
                return $this->redirectToRoute('app_user_index');
            }
            return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
            ]);
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            $this->addFlash("danger", "Usuario ya existe");
            return $this->redirectToRoute('app_register');
            //     return  new Response(); //$this->json(['error' => 'El usuario ya existe en el sistema'],
//                            Response::HTTP_CONFLICT
//                    );
        }
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response {
        return $this->render('user/show.html.twig', [
                    'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $u, EntityManagerInterface $em,
            UserPasswordHasherInterface $passwordHasher): Response {
        $form = $this->createForm(UserType::class, $u, ['is_edit' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPass = $form->get('plainPassword')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($u, $plainPass);
                $u->setPassword($hashedPassword);
            }
            $em->flush();
            $this->addFlash('success', 'Usuario actualizado correctamente.');
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user/edit.html.twig', ['user' => $u, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // No permitir eliminar el propio usuario
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'No puedes eliminar tu propio usuario.');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Usuario eliminado correctamente.');
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle', name: 'app_user_toggle', methods: ['POST'])]
    public function toggleActive(Request $request, User $user, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('toggle' . $user->getId(), $request->request->get('_token'))) {
            // No permitir desactivar el propio usuario
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'No puedes desactivar tu propio usuario.');
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            $user->setActive(!$user->isActive());
            $entityManager->flush();

            $status = $user->isActive() ? 'activado' : 'desactivado';
            $this->addFlash('success', "Usuario {$status} correctamente.");
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
