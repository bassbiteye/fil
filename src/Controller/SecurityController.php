<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Partenaire;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/admin", name="security")
     */
    public function acceuil()
    {
        return $this->json([
            'messag' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SecurityController.php',
        ]);
    }

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    // /**
    //  * @Route("/liste", name="list",methods={"GET"})
    //  */


    // public function index(UserRepository $userRepository, SerializerInterface $serializer)
    // {
    //     $users = $userRepository->findAll();
    //     $data = $serializer->serialize($users, 'json', ['groups' => ['lister']]);

    //     return new Response($data, 200, [
    //         'Content-Type' => 'application/json'
    //     ]);
    // }



    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $status = 'statu';
        $message = 'messages';
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $id = $repo->find($user);
        $part = $id;
        $repo = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $repo->find(1);
        $c = $compte;
        $userr = new User();
        $form = $this->createForm(UserType::class, $userr);

        $values = $request->request->all();
        $form->submit($values);
        $userr->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
        $userr->setEtat("actif");
        $file = $request->files->all()['imageName'];
        $userr->setImageFile($file);
        $userr->setRoles(["ROLE_ADMIN"]);

        $userr->setPartenaire($part);

        $userr->setCompte($c);
        $errors = $validator->validate($userr);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($userr);
        $entityManager->flush();

        $data = [
            $status => 201,
            $message => 'l\'utilisateur a été créée avec succes'
        ];
        return new JsonResponse($data, 201);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param JWTEncoderInterface $JWTEncoder
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function login(Request $request, JWTEncoderInterface  $JWTEncoder)
    {

        $values = json_decode($request->getContent());
        $username   = $values->username; // json-string
        $password   = $values->password; // json-string

        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(['username' => $username]);
        if (!$user) {
            return $this->json([
                'message' => 'nom d\'utilisateur n\'existe pas'
            ]);
        }

        $isValid = $this->passwordEncoder
            ->isPasswordValid($user, $password);
        if (!$isValid) {
            return $this->json([
                'message' => 'Mot de passe incorect'
            ]);
        }
        if ($user->getEtat() == "bloquer") {
            return $this->json([
                'message' => 'ACCÈS REFUSÉ vous ne pouvez pas connecter, vous etes bloqués !'
            ]);
        }
        if ($user->getPartenaire()->getEtat() == "bloquer") {
            return $this->json([
                'message' => 'ACCÈS REFUSÉ vous ne pouvez pas connecter,votre  partenaire a été bloqué !'
            ]);
        }
        $token = $JWTEncoder->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 86400 // 1 day expiration
        ]);

        return $this->json([
            'token' => $token
        ]);
    }

    /**
     * @Route("/etat/{id}", name="user", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($user->getId());
        if ($user->getUsername() == 'admin') {
            return $this->json([
                'message' => 'cet utilisateur ne pas etre bloqué!'
            ]);
        }
        if ($user->getEtat() == 'actif') {
            $user->setEtat('bloquer');
        } else if ($user->getEtat() == 'bloquer') {
            $user->setEtat('actif');
        }
        $entityManager->persist($user);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'msg' => 'l\'utilisateur est en mode '.$user->getEtat() 
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/affecter/{id}", name="user", methods={"PUT"})
     */
    public function affecter(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($user->getId());
        if ($user->getUsername() == 'admin') {
            return $this->json([
                'message' => 'cet utilisateur ne pas etre bloqué!'
            ]);
        }
        if ($user->getEtat() == 'actif') {
            $user->setEtat('bloquer');
        } else if ($user->getEtat() == 'bloquer') {
            $user->setEtat('actif');
        }
        $entityManager->persist($user);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'msg' => 'l\'utilisateur est en mode '.$user->getEtat() 
        ];
        return new JsonResponse($data, 201);
    }  
}
