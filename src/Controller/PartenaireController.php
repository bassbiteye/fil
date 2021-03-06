<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Operation;
use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\OperationRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class PartenaireController extends AbstractController

{
    /**
     * @Route("/partenaire", name="partenaire")
     */
    public function index()
    {
        return $this->json([
            'messag' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PartenaireController.php',
        ]);
    }
    /**
     * @Route("/contrat", name="contrat",methods={"GET"})
     */
    public function contrat(PartenaireRepository $userRepository, SerializerInterface $serializer)
    {
        $users = $userRepository->findOneBySomeField(1);
        $data = $serializer->serialize($users, 'json', ['groups' => ['contrat']]);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     *  @Route("/liste", name="liste", methods={"GET"})
      *@IsGranted("ROLE_SUPER")
     */
    public function show(PartenaireRepository $partenaireRepository,EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {        $user = $this->getUser();
        $partenaire = $entityManager->getRepository(Partenaire::class)->findBy((['createdBy' => $user]));

        $data      = $serializer->serialize($partenaire, 'json', ['groups' => ['listeP']]);
        return new Response($data, 200, []);
    }
    /**
     *  @Route("/listeSysteme", name="systeme", methods={"GET"})
      *@IsGranted("ROLE_SUPER")
     */
    public function systeme(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {        $user = $this->getUser();
        $partenaire = $entityManager->getRepository(User::class)->getUserSystem();

        $data      = $serializer->serialize($partenaire, 'json', ['groups' => ['lister']]);
        return new Response($data, 200, []);
    }
    /**
     *  @Route("/history", name="histor", methods={"GET"})
     */
    public function historique(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {            $user = $this->getUser();

        $operation = $entityManager->getRepository(Operation::class)->findBy(["caissier"=> $user]);

        $data      = $serializer->serialize($operation, 'json', ['groups' => ['liste']]);
        return new Response($data, 200, []);
    }
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/addP", name="add", methods={"POST"})
     *  @IsGranted("ROLE_SUPER")
     */
    public function ajoutP(Request $request,  EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($user->getId());
        $partenaire->setCreatedBy($user);
        $partenaire->setEtat('actif');
        $values = $request->request->all();
        $form->submit($values);
        $errors = $validator->validate($partenaire);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($partenaire);
        $entityManager->flush();
        if ($partenaire) {
            $compte = new Compte();
            //recuperer l'entité partenaire dans compte
            $repo = $this->getDoctrine()->getRepository(Partenaire::class);
            $part = $repo->find($partenaire->getId());
            $dat = new \DateTime();
            $dat = $dat->format('ym');
            $random = $dat . random_int(100000, 999999);
            $compte->setNumCompte($random);
            $compte->setSolde(0);
            $compte->setPartenaire($part);
            $errors = $validator->validate($compte);

            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($compte);
            $entityManager->flush();
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $values = $request->request->all();
        $form->submit($values);

        $user->setPassword($passwordEncoder->encodePassword($user,  $form->get('password')->getData()));
        $user->setEtat("actif");
            $user->setRoles(["ROLE_ADMIN"]);

      
        $file = $request->files->all()['imageName'];
        $user->setImageFile($file);
        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $part = $repo->find($partenaire->getId());
        $user->setPartenaire($part);
        $repo = $this->getDoctrine()->getRepository(Compte::class);
        $compte = $repo->find($compte->getId());
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
            'statuss' => 201,
            'messages' => 'Le partenaire a été créé par ' . $user->getNom() . ' ' . $user->getPrenom()
        ];

        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/bloquer/{id}", name="par", methods={"POST"})
     * isGranted("ROLE_SUPER")
     */
    public function update(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $partenaire = $entityManager->getRepository(Partenaire::class)->find($user->getId());
      
        if ($partenaire->getEtat() == 'actif') {
            $partenaire->setEtat('bloquer');
        } else if ($partenaire->getEtat() == 'bloquer') {
            $partenaire->setEtat('actif');
        }
        $entityManager->persist($partenaire);
        $entityManager->flush();
        $data = [
            'status' => 201,
            'messages' => 'le partenaire est en mode  '.$partenaire->getEtat()
        ];
        return new JsonResponse($data, 201);
    }

    /**
     * @Route("/depot", name="upda", methods={"POST"})
     */
    public function depot(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $values = json_decode($request->getContent());
        try {

            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repo->findOneBy(['numCompte' => $values->numCompte]);
            if (!$compte) {
                $exception = [
                    'status' => 200,
                    'message' => 'le compte n\'existe pas'
                ];
                return new JsonResponse($exception, 200);
            }
            $solde = $compte->getSolde();
            if ($values->solde < 75000) {
                $exception = [
                    'status' => 200,
                    'message' => 'le montant doit etre supérieur a 75000 f'
                ];
                return new JsonResponse($exception, 200);
            }
            $compte->setSolde($values->solde + $solde);
            $entityManager->persist($compte);
            $entityManager->flush();
        } catch (ParseException $exception) {
            $exception = [
                'status' => 200,
                'message' => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 200);
        }


        if (isset($compte)) {
            $operation = new Operation();
            $operation->setMontantdepose($values->solde);
            $operation->setMonatantAvantDepot($solde);
            $operation->setDateDepot(new \DateTime('now'));

            $repo = $this->getDoctrine()->getRepository(User::class);
            $user1 = $repo->find($user);
            $operation->setCaissier($user1);
            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $Comp = $repo->find($compte);
            $operation->setCompte($Comp);
            $entityManager->persist($operation);
            $entityManager->flush();

            $errors = $validator->validate($operation);

            $errors = $validator->validate($Comp);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->flush();
            $data = [
                'status' => 200,
                'message' => 'Le depot a éte fait avec succes ' . 'par ' . $user->getNom() . ' ' . $user->getPrenom()
            ];
            return new JsonResponse($data);
        }
    }
   /**
     * @Route("/findCompte", name="findCompte", methods={"POST"})
     */
    public function findCompte(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repo->findOneBy(['numCompte' => $values->numCompte]);
            $data  = $serializer->serialize($compte, 'json', ['groups' => ['compte']]);
            return new Response($data, 200, []);
  
}

  /**
     * @Route("/findPar", name="findPartenaire", methods={"POST"})
     */
    public function findPartenaire(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
    
            $repo = $this->getDoctrine()->getRepository(Partenaire::class);
            $partenaire = $repo->findOneBy(['ninea' => $values->ninea]);
            $data  = $serializer->serialize($partenaire, 'json', ['groups' => ['listeP']]);
            return new Response($data, 200, []);
 }


          

    /**
     * @Route("/addCompte", name="ajoutcompte", methods={"POST"})
     * isGranted("ROLE_SUPER")
     */
    public function ajouCompte(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        $user = $this->getUser();
        try {

            $repo = $this->getDoctrine()->getRepository(Partenaire::class);
            $partenaire = $repo->findOneBy(['ninea' => $values->ninea]);
            if ($partenaire) {
                $repo = $this->getDoctrine()->getRepository(Compte::class);
            } else {
                $exception = [
                    'status' => 500,
                    'message' => 'ce partenaire  n\'existe pas'
                ];
                return new JsonResponse($exception, 500);
            }
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'ce Partenaire n\'existe pas'
            ];
            return new JsonResponse($exception, 500);
        }


        $dat = new \DateTime();
        $dat = $dat->format('ym');
        $random = $dat . random_int(100000, 999999);


        $compte = new Compte();
        $compte->setNumCompte($random);
        $compte->setSolde(0);
        $compte->setPartenaire($partenaire);


        $entityManager->persist($compte);
        $entityManager->flush();


        $errors = $validator->validate($compte);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }



        $entityManager->flush();
        $data = [
            'num' => $compte->getNumCompte(),
            'status' => 200,
            'message' => 'Le nouveau compte est '.$random,
            
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/updateP/{id}", name="updatep", methods={"PUT"})
     */
    public function modifP(Request $request, SerializerInterface $serializer, Partenaire $partenaire, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $partUpdate = $entityManager->getRepository(Partenaire::class)->find($partenaire->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $partUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($partUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'Le partenaire a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }
      /**
     * @Route("/userid/{id}", name="getu", methods={"GET"})
     */
    public function users(Request $request, SerializerInterface $serializer, User $user, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($user->getId());
        $data      = $serializer->serialize($user, 'json', ['groups' => ['users']]);
        return new Response($data, 200, []);
    }
      /**
     * @Route("/findallCompte", name="findallCompte", methods={"GET"})
     */
    public function findallCompte(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {        $user = $this->getUser();

            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repo->getUserPart($user->getPartenaire());
            $data  = $serializer->serialize($compte, 'json', ['groups' => ['listeP']]);
            return new Response($data, 200, []);
  
}
}
