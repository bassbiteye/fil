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
    /**
     * @Route("/liste", name="list",methods={"GET"})
     */


    public function index(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $users = $userRepository->findAll();
        $data = $serializer->serialize($users, 'json', ['groups' => ['lister']]);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }


    /**
     * @Route("/register", name="register", methods={"POST"})
      *@IsGranted("ROLE_ADMIN")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $status = 'statu';
        $message = 'messages';
        $userr = $this->getUser();
     
       
          
        $user = new User();
        
        $partenaire = new Partenaire();
        $compte = new Compte();
       
        $form = $this->createForm(UserType::class, $user);
        $values = $request->request->all();
        $form->submit($values);

        $user->setPassword($passwordEncoder->encodePassword($user,$form->get('password')->getData()));
        $user->setEtat("actif");
        $file = $request->files->all()['imageName'];
        $user->setImageFile($file);
        $user->setRoles(["ROLE_ADMIN"]);
        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $part = $repo->find(1);
        $user->setPartenaire($part);
        $repo = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $repo->find(1);
        $user->setCompte($compte);
        $errors = $validator->validate($user);

                if (count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                }
        $entityManager->persist($user);
        $entityManager->flush();
        
        $data = [
            $status => 500,
            $message => 'l\'utilisateur a été créée avec succes'
        ];
        return new JsonResponse($data, 500);
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
                'message' => 'Username incorrect'
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
                'message' => 'ACCÈS REFUSÉ vous ne pouvez pas connecter !'
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
        $useretat = $entityManager->getRepository(User::class)->find($user->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $useretat->$setter($value);
            }
        }
        $errors = $validator->validate($useretat);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'statu' => 200,
            'message' => 'Le utilisateur a bien été modifié'
        ];
        return new JsonResponse($data);
    }

    function verifyInput($var)
    {
        $var = trim($var);
        $var = stripcslashes($var);
        $var = htmlspecialchars($var);

        return $var;
    }
}
