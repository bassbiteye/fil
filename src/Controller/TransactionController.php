<?php

namespace App\Controller;

use Osms\Osms;
use App\Entity\User;
use App\Entity\Tarifs;
use App\Entity\ComEtat;
use App\Entity\Expediteur;
use App\Entity\Partenaire;
use App\Entity\Transaction;
use App\Entity\Beneficiaire;
use App\Form\ExpediteurType;
use App\Form\BeneficiareType;
use App\Form\TransactionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{
    public $status;
    public $message;
    public $dateFrom;
    private $dateTo;
    public function __construct()
    {
        $this->dateFrom = 'dateFrom';
        $this->dateTo = 'dateTo';
        $this->status = 'status';
        $this->message = 'message';
    }
    /**
     *@Route("/envoi", name="envoi")
     */
    public function envoi(Request $request, EntityManagerInterface $entityentityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $expediteur = new Expediteur();
        $formEx = $this->createform(ExpediteurType::class, $expediteur);
        $values = $request->request->all();
        $formEx->submit($values);
        $errors = $validator->validate($expediteur);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $beneficiare = new Beneficiaire();
        $formB = $this->createform(BeneficiareType::class, $beneficiare);
        $values = $request->request->all();
        $formB->submit($values);
        $errors = $validator->validate($beneficiare);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $transaction = new Transaction();
     
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Tarifs::class);
        $tarif = $repo->findAll();

        $formT = $this->createform(TransactionType::class, $transaction);
        $values = $request->request->all();
        $formT->submit($values);
        $dat = new \DateTime();
        $dat = $dat->format('ym');
        $random =  random_int(100000000, 999999999);
        $transaction->setCodeSecret($random);
        $transaction->setValidate(false);
        $transaction->setDateEnvoi(new \DateTime('now'));
        $transaction->setExpediteur($expediteur);
        $transaction->setBeneficiaire($beneficiare);
        $transaction->setUser($user);
        $solde = $user->getCompte()->getSolde();
        $compte = $user->getCompte();
        $montant = $transaction->getMontantTransaction();
        if ($montant > $solde) {
            $data = [
                $this->status => 201,
                $this->message => 'Le montant est insuffisant '
            ];
            return new JsonResponse($data, 201);
        }
        for ($i = 0; $i < count($tarif); $i++) {
            if ($montant >= $tarif[$i]->getMin()&& $montant <= $tarif[$i]->getMax()) {
                $transaction->setTarifs($tarif[$i]);
                $frais= $tarif[$i]->getFrais();
        
            }
        }
        
        $comEnvoi = $frais * 20 / 100;
        $comE =  $frais * 30 / 100;
        $comPr = $frais * 30 / 100;
        $transaction->setMontantTransaction($montant);
        if($request->request->get('ok')){
            $compte->setSolde(($solde - $montant )+  $comEnvoi);
        }else{
            $montant=$montant-$frais;
            $compte->setSolde($solde - $montant+$comEnvoi);
        }

        $errors = $validator->validate($transaction);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $transaction->setComEtat($comE);
        $transaction->setComProprietaire($comPr);
        $transaction->setComEnvoie($comEnvoi);
        $entityentityManager->persist($beneficiare);
        $entityentityManager->persist($expediteur);
        $entityentityManager->persist($transaction);
        $entityentityManager->flush();
        //sms
        $config = array(

            'clientId' => 'MaIKuvEdVDAB7MbnKXHTJZl4XoviuyhF',
            'clientSecret' => 's1qgKF0IlKKxlLA2'
        );

        $osms = new Osms($config);
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();

        if (!empty($response['access_token'])) {
            $senderAddress = 'tel:+221' . $expediteur->getTelephoneE();
            $receiverAddress = 'tel:+221' . $beneficiare->getTelephoneb();
            $this->message = 'bienvenue sur fatfat tranfert '
                . $expediteur->getNomE() . ' ' . $expediteur->getPrenomE() . ' vous as enoyé '
                . $transaction->getMontantTransaction() . ',le code retrait est ' . $transaction->getCodeSecret() .
                ' disponible dans toutes les agences fatfat';
            $senderName = $expediteur->getNomE();

            $osms->sendSMS($senderAddress, $receiverAddress, $this->message, $senderName);
        } else {
            // error
            echo $response['error'];
        }

        $data = [
            $this->status => 201,
            $this->message => 'Le transaction a ete fait avec succes ,le code est  ' . $random
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/frais",name="fr",methods={"POST"})
     */
    public function tarif(Request $request, SerializerInterface $serializer)
    {

        $transaction = new Transaction();
        $formT = $this->createform(TransactionType::class, $transaction);
        $values = $request->request->all();
        $formT->submit($values);
        $repo = $this->getDoctrine()->getRepository(Tarifs::class);
        $tarif = $repo->findAll();
        for ($i = 0; $i < count($tarif); $i++) {
            if (
                $transaction->getMontantTransaction() >= $tarif[$i]->getMin()
                && $transaction->getMontantTransaction() <= $tarif[$i]->getMax()
            ) {
                $data  = $serializer->serialize($tarif[$i], 'json', ['groups' => ['frais']]);
                return new Response($data, 200, []);
            }
        }
    }
    /**
     * @Route("/verif", name="verifier", methods={"POST"})
     */
    public function verif(Request $request, SerializerInterface $serializer)
    {
        $transaction = new Transaction();
        $formTr = $this->createform(TransactionType::class, $transaction);
        $values = $request->request->all();
        $formTr->submit($values);
        try {
        $repo = $this->getDoctrine()->getRepository(Transaction::class);
        $code = $repo->findOneBy(['codeSecret' =>  $transaction->getCodeSecret()]);
        if (!$code) {
            $data = [
                $this->status => 500,
                $this->message => 'le code n\'est pas valide'
            ];
            return new JsonResponse($data, 500);
        } 
        if ($code->getValidate() == true) {
            $exception = [
                $this->status => 500,
                $this->message => 'l\'argent a ete retiré'
            ];
            return new JsonResponse($exception, 500);
        }  
     } catch (ParseException $exception) {
            $exception = [
                $this->status => 500,
                $this->message => 'Vous devez renseigner  tous  les champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $data  = $serializer->serialize($code, 'json', ['groups' => ['code']]);
        return new Response($data, 200, []);
    }
    /**
     * @Route("/retrait", name="retrait")
     */
    public function retrait(Request $request, EntityManagerInterface $entityentityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {


        $transaction = new Transaction();
        $user = $this->getUser();
        $formTr = $this->createform(TransactionType::class, $transaction);
        $values = $request->request->all();
        $formTr->submit($values);

        try {

            $repo = $this->getDoctrine()->getRepository(Transaction::class);
            $transaction = $repo->findOneBy(['codeSecret' =>  $transaction->getCodeSecret()]);
            if (!$transaction) {
                $exception = [
                    $this->status => 500,
                    $this->message => 'le code n\'est pas valide'
                ];
                return new JsonResponse($exception, 500);
            }

            if ($transaction->getValidate() == true) {
                $exception = [
                    $this->status => 500,
                    $this->message => 'l\'argent a ete retiré'
                ];
                return new JsonResponse($exception, 500);
            }
            $transaction->setValidate(true);
            $transaction->setDateRetrait(new \DateTime('now'));
            $transaction->setUserRetrait($user);
            $transaction->setCni($values["cni"]);

            $solde = $user->getCompte()->getSolde();
            $compte = $user->getCompte();


            $com =  $transaction->getTarifs()->getFrais();
            $com = $com * 20 / 100;

            $compte->setSolde($solde + $transaction->getMontantTransaction() + $com);
            // if ($transaction->getMontantTransaction() > $solde) {
            //     $data = [
            //         $this->status => 201,
            //         $this->message => 'Le montant est insuffisant '
            //     ];
            //     return new JsonResponse($data, 201);
            // }
            $errors = $validator->validate($transaction);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                $this->status => 500,
                $this->message => 'Vous devez renseigner  tous  les champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $entityentityManager->persist($transaction);
        $entityentityManager->flush();
        $data = [
            $this->status => 201,
            'transations' => $transaction,
            $this->message => 'Le retrait a ete fait avec succes '
        ];
        return new JsonResponse($data, 201);
    }

    public function transDate(TransactionRepository $repo, $debut, $fin)
    {

        $transactions = $repo->findDate($debut, $fin);

        if ($debut > $fin) {
            return $this->json([
                $this->message => 'la date de début  ne doit etre superieure à la date de fin !'
            ]);
        } elseif ($fin > new \DateTime('now')) {
            return $this->json([
                $this->message => 'la date de début  ne doit etre superieure à la date d\'Aujourd\'hui !'
            ]);
        }

        return $transactions;
    }

    /**
     *  @Route("/partenaire", name="partenaires", methods={"GET"})
     */
    public function part(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $partenaire = $repo->find($user->getPartenaire());

        $data      = $serializer->serialize($partenaire, 'json', ['groups' => ['lister']]);
        return new Response($data, 200, []);
    }
    /**
     *  @Route("/use", name="user", methods={"GET"})
     */
    public function user(SerializerInterface $serializer)
    {
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($user);

        $data      = $serializer->serialize($user, 'json', ['groups' => ['users']]);
        return new Response($data, 200, []);
    }
    /**
     * @Route("/verif", name="verif", methods={"POST"})
     */
    public function verifcode(Request $request, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
        $repo = $this->getDoctrine()->getRepository(Transaction::class);
        $compte = $repo->findOneBy(['codeSecret' => $values->codeSecret]);
        $data  = $serializer->serialize($compte, 'json', ['groups' => ['compte']]);
        return new Response($data, 200, []);
    }
    /**
     * @Route("/detailEnvoi", name="detailEnv",methods={"POST"})
     */
    public function detailEnvi(Request $request, EntityManagerInterface $entityentityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {

        $user = $this->getUser();
        $values = json_decode($request->getContent());
        if (!$values) {
            $values = $request->request->all();
        }
        $debut = new \DateTime($values[$this->dateFrom]);
        $fin = new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail = $repo1->getByDate($debut, $fin, $user);
            if ($detail == []) {
                return $this->json([
                    $this->message => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                $this->status => 500,
                $this->message => 'Vous devez renseigner tous les  champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $data      = $serializer->serialize($detail, 'json', ['groups' => ['trans']]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/detailRetrait", name="detailRetrai",methods={"POST"})
     */
    public function detailRetra(Request $request, EntityManagerInterface $entityentityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {

        $user = $this->getUser();
        $values = json_decode($request->getContent());
        if (!$values) {
            $values = $request->request->all();
        }
        $debut = new \DateTime($values[$this->dateFrom]);
        $fin = new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail = $repo1->finByDateR($debut, $fin, $user);
            if ($detail == []) {
                return $this->json([
                    $this->message => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                $this->status => 500,
                $this->message => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $data      = $serializer->serialize($detail, 'json', ['groups' => ['trans']]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/detailEnvoiP", name="detailEnvP",methods={"POST"})
     */
    public function detailEnviP(Request $request, EntityManagerInterface $entityentityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {

        $user = $this->getUser();
        $values = json_decode($request->getContent());
        if (!$values) {
            $values = $request->request->all();
        }
        $debut = new \DateTime($values[$this->dateFrom]);
        $fin = new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail = $repo1->findEnP($debut, $fin, $user->getPartenaire());
            if ($detail == []) {
                return $this->json([
                    $this->message => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                $this->status => 500,
                $this->message => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $data      = $serializer->serialize($detail, 'json', ['groups' => ['trans']]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/detailRetraitP", name="detailRetraiP",methods={"POST"})
     */
    public function detailRetraP(Request $request, EntityManagerInterface $entityentityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {

        $user = $this->getUser();
        $values = json_decode($request->getContent());
        if (!$values) {
            $values = $request->request->all();
        }
        $debut = new \DateTime($values[$this->dateFrom]);
        $fin = new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail = $repo1->findRetP($debut, $fin, $user->getPartenaire());
            if ($detail == []) {
                return $this->json([
                    $this->message => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                $this->status => 500,
                $this->message => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $data      = $serializer->serialize($detail, 'json', ['groups' => ['trans']]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
}
