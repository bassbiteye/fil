<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Operation;
use App\Entity\Partenaire;
use App\Form\PartenaireType;
use PhpParser\Node\Stmt\Catch_;
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
     *  @Route("/liste", name="liste", methods={"GET"})
     */
    public function show(PartenaireRepository $partenaireRepository, SerializerInterface $serializer)
    {
        $partenaire = $partenaireRepository->findAll();

        $data      = $serializer->serialize($partenaire, 'json', ['groups' => ['lister']]);
        return new Response($data, 200, []);
    }
    /**
     *  @Route("/history", name="histor", methods={"GET"})
     */
    public function historique(OperationRepository $operationRepository, SerializerInterface $serializer)
    {
        $operation = $operationRepository->findAll();

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
     * isGranted("ROLE_SUPER")
     */
    public function ajoutP(Request $request,  EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    { 
        $user = $this->getUser();
      
            $partenaire = new Partenaire();
            $form = $this->createForm(PartenaireType::class, $partenaire);
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo->find($user->getId());
            $partenaire->setCreatedBy($user);
             $values=$request->request->all();
             $form->submit($values);
                $entityManager->persist($partenaire);
                $entityManager->flush();
            
         
            $errors = $validator->validate($partenaire);

            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
           

            if ($partenaire) {
                $compte = new Compte();
                 //recuperer l'entité partenaire dans compte
                 $repo = $this->getDoctrine()->getRepository(Partenaire::class);
                 $part = $repo->find($partenaire->getId());
                $random =$partenaire->getId() .random_int(100000,999999);
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
                $values=$request->request->all();
                $form->submit($values);
               
                    $user->setPassword($passwordEncoder->encodePassword($user,  $form->get('password')->getData()));
                    $user->setEtat("actif");
                    $user->setRoles(["ROLE_ADMIN"]);
                    $file=$request->files->all()['imageName'];
                    $user->setImageFile($file);
                    $repo = $this->getDoctrine()->getRepository(Partenaire::class);
                    $part = $repo->find($partenaire->getId());
                    $user->setPartenaire($part);
                    $repo = $this->getDoctrine()->getRepository(Compte::class);
                    $compte = $repo->find($compte->getId());
                    $user->setCompte($compte);
                  $entityManager->persist($user);
                $entityManager->flush();
                
              

             
             $data = [
                'statuss' => 201,
                'messge' => 'Le partenaire a été créé par ' . $user->getNom() . ' ' . $user->getPrenom()
            ];
    
            return new JsonResponse($data, 201);
    
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
            if($values->solde<75000){
                $exception = [
                    'status' => 500,
                    'message' => 'le montant doit etre supérieur a 75000 f'
                ];
                return new JsonResponse($exception, 500);
            }
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

            $repo = $this->getDoctrine()->getRepository(Partenaire::class);
            $partenaire = $repo->findOneBy(['ninea' => $values->ninea]);
            if($partenaire){
            $repo = $this->getDoctrine()->getRepository(Compte::class);
         
            // if($compte){
            // $exception = [
            //     'status' => 200,
            //     'message' => 'compte existe'
            // ];
            // return new JsonResponse($exception, 200);
            // }
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
      

        $random =$partenaire->getId() .random_int(100000,999999);

    
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
