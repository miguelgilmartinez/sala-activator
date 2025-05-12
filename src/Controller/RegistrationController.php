<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Miguel Gil Martínez <miguel.gil.martinez@juntadeandalucia.es>
 */
class RegistrationController extends AbstractController {

    #[Route('/register2', name: 'app_register2')]
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
                return $this->redirectToRoute('app_login');
            }
            return $this->render('registration/register.html.twig', [
                        'registrationForm' => $form->createView(),
            ]);
        }
        catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            $this->addFlash("danger", "Usuario ya existe");
            return $this->redirectToRoute('app_register');
            //     return  new Response(); //$this->json(['error' => 'El usuario ya existe en el sistema'],
//                            Response::HTTP_CONFLICT
//                    );
        }
    }
}
