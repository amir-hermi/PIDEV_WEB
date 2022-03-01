<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UtilisateurApiController extends AbstractController
{
   /**
    * @Route("user/signup", name="app_register")
    */
   public function signupAction(Request $request , UserPasswordEncoderInterface $passwordEncoder){
       $email = $request->query->get("email");
       $username = $request->query->get("username");
       $password = $request->query->get("password");
       $roles = $request->get("roles");
       $tel = $request->query->get("tel");

       if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
           return new Response("email invalid");
       }
       $utilisateur = new Utilisateur();
       $utilisateur->setUsername($username);
       $utilisateur->setEmail($email);
       $utilisateur->setPassword(
           $passwordEncoder->encodePassword(
               $utilisateur,
               $password
           )
       );
       //$utilisateur->sIsVerified(true);
       $utilisateur->setTel($tel);
       $utilisateur->setRoles(array($roles));

       try {
           $em = $this->getDoctrine()->getManager();
           $em->persist($utilisateur);
           $em->flush();
           return  new JsonResponse("Account is created",200);

       }catch (\Exception $ex){
           return new Response("execption".$ex->getMessage());
       }
   }

    /**
     * @Route("user/signin", name="app_logina")
     */
    public function signinAction(Request $request){
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $username = $request->query->get("username");

        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['username'=>$username]);

        if($utilisateur){
            if(password_verify($password,$utilisateur->getPassword())){
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($utilisateur);
                return new JsonResponse($formatted);

            }
            else{
                return new Response("password not found");
            }

        }
        else{
            return new Response("user not found");
        }


    }

    /**
     * @Route("user/edituser", name="app_gestion_profile")
     */
    public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $id = $request->get("id");

        $email = $request->query->get("email");
        $username = $request->query->get("username");
        $password = $request->query->get("password");
        $tel = $request->query->get("tel");
        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

      $utilisateur->setUsername($username);
        $utilisateur->setPassword(
            $passwordEncoder->encodePassword(
                $utilisateur,
                $password
            )
        );
        $utilisateur->setEmail($email);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($utilisateur);
            $em->flush();
            return  new JsonResponse("Account is updated",200);

        }catch (\Exception $ex){
            return new Response("fail to update".$ex->getMessage());
        }
    }

    /**
     * @Route("user/listuser", name="app_gestion_profile")
     */
    public function list(Request $request, SerializerInterface $serializer){



        $em = $this->getDoctrine()->getManager();
        $utilisateur = $em->getRepository(Utilisateur::class)->findAll();

        //$utilisateur->setUsername($username);
        //$utilisateur->setPassword(
            //$passwordEncoder->encodePassword(
             //   $utilisateur,
               // $password
           // )
        //);
        $dataJson=$serializer->serialize($utilisateur,'json',['groups'=>'utilisateur']);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return  new JsonResponse(json_decode($dataJson),200);

        }catch (\Exception $ex){
            return new Response("fail to update".$ex->getMessage());
        }
    }

    public function getAllProducts(ProduitRepository $produitRepository , SerializerInterface $serializer)
    {
        $p = $produitRepository->findAll();
        $dataJson=$serializer->serialize($p,'json',['groups'=>'produit']);
        // dd($dataJson);
        return new JsonResponse(json_decode($dataJson) );

    }
}
