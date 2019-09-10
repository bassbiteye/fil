<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Profile;
use App\Form\ProfileType;

use App\Entity\Partenaire;
use App\Repository\CompteRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{

    /**
     *  @Route("/listeSysteme", name="systeme", methods={"GET"})
     */
    public function systeme(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {              

        $user = $entityManager->getRepository(User::class)->getUserSystem();
        $data      = $serializer->serialize($user, 'json', ['groups' => ['lister']]);
        return new Response($data, 200, []);
    }
    /**
     *  @Route("/listeUpart", name="userPart", methods={"GET"})
      *@IsGranted("ROLE_ADMIN")
     */
    public function userPart(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {       
        $user1 = $this->getUser();
        $user = $entityManager->getRepository(User::class)->getUserPart($user1->getPartenaire());
        $data      = $serializer->serialize($user, 'json', ['groups' => ['users']]);
        return new Response($data, 200, []);
    }
   /**
     *@Route("/info", name="info", methods={"GET"})
     */
    public function infoU(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {       
        $user1 = $this->getUser();
        $user = $entityManager->getRepository(User::class)->getInfo($user1->getCompte(),$user1->getId());
        $data      = $serializer->serialize($user, 'json', ['groups' => ['users']]);
        return new Response($data, 200, []);
    }
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    /**
     * @Route("/profile", name="list",methods={"GET"})
     */
    public function index(ProfileRepository $profileRepository, SerializerInterface $serializer)
    {
        $users = $profileRepository->findAll();
        $data = $serializer->serialize($users, 'json', ['groups' => ['profile']]);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/compte", name="compte",methods={"GET"})
     */
    public function compt(UserRepository $compteRepository, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $compte = $compteRepository->find($user);
       $data = $serializer->serialize($compte, 'json', ['groups' => ['affect']]);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $id = $repo->find($user);
        $part = $id;
        $userr = new User();
        $form = $this->createForm(UserType::class, $userr);
        $profile = new Profile();
        $form1 = $this->createForm(ProfileType::class,$profile);
        $values = $request->request->all();
        $form->submit($values);
        $form1->submit($values);
        $userr->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
        $userr->setEtat("actif");
        $file = $request->files->all()['imageName'];
        $userr->setImageFile($file);
        if($profile->getLibelle()=='admin'){
            $userr->setRoles(["ROLE_ADMIN"]);
        }
        if($profile->getLibelle()=='super'){
            $userr->setRoles(["ROLE_SUPER"]);
        }
        if($profile->getLibelle()=='user'){
            $userr->setRoles(["ROLE_USERS"]);
        }
        if($profile->getLibelle()=='caissier'){
            $userr->setRoles(["ROLE_CAISSIER"]);
        }
        $userr->setPartenaire($part);
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
            'status' => 201,
            'messages' => 'l\'utilisateur a été créée avec succes'
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
                'code'=>'ko',
                'message' => 'Le nom d\'utilisateur n\'existe pas'
            ]);
        }

        $isValid = $this->passwordEncoder
            ->isPasswordValid($user, $password);
        if (!$isValid) {
            return $this->json([
                'code'=>'ko',
                'message' => 'Le  Mot de passe  est incorect'
            ]);
        }
        if ($user->getEtat() == "bloquer") {
            return $this->json([
                'code'=>'ko',
                'message' => 'ACCÈS REFUSÉ vous ne pouvez pas connecter, vous etes bloqués !'
            ]);
        }
        if ( $user->getPartenaire() && $user->getPartenaire()->getEtat() == "bloquer") {
            return $this->json([
                'code'=>'ko',
                'message' => 'ACCÈS REFUSÉ vous ne pouvez pas connecter,votre  partenaire a été bloqué !'
                
            ]);
        }
        $token = $JWTEncoder->encode([
            'username' => $user->getUsername(),
            'roles'=>$user->getRoles(),
            'nom' =>$user->getNom(),
            'imageName' =>$user->getImageName(),
            'prenom'=>$user->getPrenom(),
            'exp' => time() + 8600 // 1 day expiration
        ]);

        return $this->json([
            'token' => $token
        ]);
    }

    /**
     * @Route("/etat/{id}", name="u", methods={"PUT"})
     */
    public function update(User $user, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($user->getId());
        if ($user->getUsername() == 'admin') {
            return $this->json([
                'messages' => 'cet utilisateur ne pas etre bloqué!'
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
            'messages' => 'l\'utilisateur est  '.$user->getEtat() 
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/affecter/{id}", name="aff", methods={"POST"})
     */
    public function affecter(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        $user = $entityManager->getRepository(User::class)->find($user->getId());
        $compte = $entityManager->getRepository(Compte::class)->findOneBy(['numCompte' =>$values->numCompte]);
       if(!$compte){
        return $this->json([
            'messages' => 'compte introuvable!'
        ]);
       }
        $user->setCompte($compte);
        $entityManager->persist($user);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'messages' => 'le compte a ete affecté avec sucess  '.$user->getEtat() 
        ];
        return new JsonResponse($data, 201);
    }  
    /**
     * @Route("/updateUser/{id}", name="updateu", methods={"PUT"})
     */
    public function modifU(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $UserUpdate = $entityManager->getRepository(User::class)->find($user->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $UserUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($UserUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'L\'utilisateur a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }
  

}
