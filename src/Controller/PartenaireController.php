<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Partenaire;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Exception\ParseException;

use App\Entity\Operation;
use App\Entity\Compte;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Component\Config\Definition\Exception\Exception;

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
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PartenaireController.php',
        ]);
    }
    /**
     *  @Route("/partenaire", name="liste", methods={"GET"})
     */
    public function show(PartenaireRepository $partenaireRepository, SerializerInterface $serializer)
    {
        $partenaire = $partenaireRepository->findAll();

        $data      = $serializer->serialize($partenaire, 'json', ['groups' => ['lister']]);
        return new Response($data, 200, []);
    }
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/addP", name="add", methods={"POST"})
     * isGranted("ROLE_SUPER")
     */
    public function ajoutP(Request $request,  EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $values = json_decode($request->getContent());

        if ($user->getId()) {
            $partenaire = new Partenaire();
            $partenaire->setRaisonSocial($values->raisonSocial);
            $partenaire->setAdresse($values->adresse);
            $partenaire->setNinea($values->ninea);
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo->find($user->getId());
            $partenaire->setCreatedBy($user);
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
                $compte->setNumCompte($values->numCompte);
                $compte->setSolde(0);
                //recuperer l'entité partenaire dans compte
                $repo = $this->getDoctrine()->getRepository(Partenaire::class);
                $part = $repo->find($partenaire->getId());
                $compte->setPartenaire($part);
                $entityManager->persist($compte);
                $entityManager->flush();

                $user = new User();
                $user->setUsername($values->username);
                $user->setNom($values->nom);
                $user->setPrenom($values->prenom);
                $user->setEtat($values->etat);
                $user->setTelephone($values->telephone);
                $user->setPhoto($values->photo);
                $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
                $user->setRoles($values->roles);
                //recuperer l'entité partenaire dans user
                $repo = $this->getDoctrine()->getRepository(Partenaire::class);
                $part = $repo->find($partenaire->getId());
                $user->setPartenaire($part);
                //recuperer l'entité partenaire du compte

                $repo = $this->getDoctrine()->getRepository(Compte::class);
                $compte = $repo->find($compte->getId());
                $user->setCompte($compte);

                $entityManager->persist($user);
                $entityManager->flush();
            }
        }


        $data = [
            'status' => 201,
            'message' => 'Le partenaire a été créé par ' . $user->getNom() . ' ' . $user->getPrenom()
        ];

        return new JsonResponse($data, 201);

        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner les tous  champs'
        ];
        return new JsonResponse($data, 500);
    }
    /**
     * @Route("/bloquer/{id}", name="par", methods={"PUT"})
     * isGranted("ROLE_SUPER")
     */
    public function update(Request $request, SerializerInterface $serializer, Partenaire $partenaire, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $partenaireUpdate = $entityManager->getRepository(Partenaire::class)->find($partenaire->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $partenaireUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($partenaireUpdate);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'Le partenaire a bien été modifié'
        ];
        return new JsonResponse($data);
    }
    /**
     * @Route("/depot", name="upda", methods={"POST"})
     *
     */
    public function depot(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $values = json_decode($request->getContent());
        try {
            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repo->findOneBy(['numCompte' => $values->numCompte]);
            $solde = $compte->getSolde();
            $compte->setSolde($values->solde + $solde);
            $entityManager->persist($compte);
            $entityManager->flush();
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 500);
            // printf('Unable to parse the YAML string: %s', $exception->getMessage());
        }


        if (isset($compte)) {
            $operation = new Operation();
            $operation->setMontantdepose($values->solde);
            $operation->setMonatantAvantDepot($solde);
            $operation->setDateDepot(new \DateTime('now'));


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
     * @Route("/addCompte", name="compte", methods={"POST"})
     *
     */
    public function addCompte(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    { 
        $values = json_decode($request->getContent());
        $user = $this->getUser();
        try {
            $random =  random_int(1, 100000);
            $repo = $this->getDoctrine()->getRepository(Partenaire::class);
            $partenaire = $repo->findOneBy(['ninea' => $values->ninea]);
            if($partenaire){
            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repo->findOneBy(['numCompte' =>$random ]);
            if($compte){
            $exception = [
                'status' => 200,
                'message' => 'compte existe'
            ];
            return new JsonResponse($exception, 200);
            }
        }else{
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
            'status' => 200,
            'message' => 'Le nouveau compte a bien été ajouté ' . 'par ' . $user->getNom() . ' ' . $user->getPrenom()
        ];
        return new JsonResponse($data);
    }
}
