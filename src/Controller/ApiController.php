<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiController extends AbstractController{

    public function apitest(Request $request){
        return $this->render("apitest.html.twig");
    }

    public function apilogin(Request $request){
        if($request->isXmlHttpRequest()){
            $client = HttpClient::create();
            $response = $client->request("POST", "https://webapi.gordon.com.pl/client-service/authenticate", array("json" => array(
                "Login" => $request->request->get('Login'),
                "Password" => $request->request->get('Password'))
            ));
            $array = $response->toArray();
            if($array['error']!=="null"){
                $request->getSession()->set('api_token', $array['token']);
                return new Response("Zalogowano");
            }
            else{
                return new Response($array['error']);
            }
        }
        else{
            return new Response("unauthorized", 404);
        }
    }

    public function apiindexinfo(Request $request){
        if($request->isXmlHttpRequest()){
            $client = HttpClient::create();
            $response = $client->request("POST", "https://webapi.gordon.com.pl/client-service/articles", array( 
                'auth_bearer' => $request->getSession()->get('api_token'),
                'json' => array(strtoupper($request->request->get('index')))));
            return new Response($response->getContent());            
        }
        else{
            return new Response("unauthorized", 404);
        }
    }
}