<?php

namespace App\Controller;
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
use App\Entity\ComProprietaire;
use App\Repository\UserRepository;
use App\Repository\TarifsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{
    public $dateFrom;
    private $dateTo;
    public function __construct()
    {
        $this->dateFrom='dateFrom';
        $this->dateTo='dateTo';
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
        $comETAT = new ComEtat();
        $comPro = new ComProprietaire();
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
        for ($i = 0; $i < count($tarif); $i++) {
            if (
                $transaction->getMontantTransaction() >= $tarif[$i]->getMin()
                && $transaction->getMontantTransaction() <= $tarif[$i]->getMax()
            ) {
                $transaction->setMontantTransaction($transaction->getMontantTransaction());
                $transaction->setTarifs($tarif[$i]);
                $comEnvoi = $tarif[$i]->getFrais() * 10 / 100;
                $comE = $tarif[$i]->getFrais() * 30 / 100;
                $comPr = $tarif[$i]->getFrais() * 40 / 100;
                $compte->setSolde($solde - $transaction->getMontantTransaction() +  $comEnvoi);
            }
        }
        if ($transaction->getMontantTransaction() > $solde) {
            $data = [
                'statuss' => 500,
                'messge' => 'Le montant est insuffisant '
            ];
            return new JsonResponse($data, 500);
        }
        $errors = $validator->validate($transaction);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $comETAT->setCommission($comE);
        $comETAT->setDateCom(new \DateTime('now'));
        $comETAT->setTransaction($transaction);
        $comPro->setCommission($comPr);
        $comPro->setDateCom(new \DateTime('now'));
        $comPro->setTransaction($transaction);
        $entityentityManager->persist($beneficiare);
        $entityentityManager->persist($expediteur);
        $entityentityManager->persist($comETAT);
        $entityentityManager->persist($comPro);
        $entityentityManager->persist($transaction);
        $entityentityManager->flush();
        $data = [
            'statuss1' => 201,
            'message1' => 'Le transaction a ete fait avec succes ,le code est'.$random 
        ];
        return new JsonResponse($data, 201);
    }
    /**
     * @Route("/verif", name="verifier", methods={"POST"})
     */
    public function verif(Request $request, SerializerInterface $serializer)
    {
        $values = json_decode($request->getContent());
            $repo = $this->getDoctrine()->getRepository(Transaction::class);
            $code = $repo->findOneBy(['codeSecret' =>$values->codeSecret]);
            if (!$code) {
                $data = [
                    'status' => 500,
                    'message' => 'le code n\'est pas valide'
                ];
                return new JsonResponse($data, 500);
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
                    'statu' => 500,
                    'messag' => 'le code n\'est pas valide'
                ];
                return new JsonResponse($exception, 500);
            }

            if ($transaction->getValidate() == true) {
                $exception = [
                    'status' => 500,
                    'message' => 'l\'argent a ete retiré'
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

            $errors = $validator->validate($transaction);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner  tous  les champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $entityentityManager->persist($transaction);
        $entityentityManager->flush();
        $data = [
            'statuss' => 201,
            'transations' =>$transaction,
            'messge' => 'Le retrait a ete fait avec succes ' 
        ];
        return new JsonResponse($data, 201);
    }
    
    public function transDate(TransactionRepository $repo, $debut, $fin)
    {

        $transactions = $repo->findDate($debut, $fin);
       
        if ($debut > $fin) {
            return $this->json([
                'message' => 'la date de début  ne doit etre superieure à la date de fin !'
            ]);
        } elseif ($fin > new \DateTime('now')) {
            return $this->json([
                'message' => 'la date de début  ne doit etre superieure à la date d\'Aujourd\'hui !'
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
    public function user( SerializerInterface $serializer)
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
        if(!$values){
            $values=$request->request->all();
        }
        $debut= new \DateTime($values[$this->dateFrom]);
        $fin= new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail =$repo1->getByDate($debut,$fin,$user);
            if ($detail == []) {
                return $this->json([
                    'message' => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner les tous  champs'
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
        if(!$values){
            $values=$request->request->all();
        }
        $debut= new \DateTime($values[$this->dateFrom]);
        $fin= new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail =$repo1->finByDateR($debut,$fin,$user);
            if ($detail == []) {
                return $this->json([
                    'message' => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner les tous  champs'
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
        if(!$values){
            $values=$request->request->all();
        }
        $debut= new \DateTime($values[$this->dateFrom]);
        $fin= new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail =$repo1->findEnP($debut,$fin,$user->getPartenaire());
            if ($detail == []) {
                return $this->json([
                    'message' => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner les tous  champs'
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
        if(!$values){
            $values=$request->request->all();
        }
        $debut= new \DateTime($values[$this->dateFrom]);
        $fin= new \DateTime($values[$this->dateTo]);


        try {
            $repo1 = $this->getDoctrine()->getRepository(Transaction::class);
            $detail =$repo1->findRetP($debut,$fin,$user->getPartenaire());
            if ($detail == []) {
                return $this->json([
                    'message' => 'aucune transaction pour cette intervale!'
                ]);
            }
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 500);
        }
        $data      = $serializer->serialize($detail, 'json', ['groups' => ['trans']]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
 
   

}
