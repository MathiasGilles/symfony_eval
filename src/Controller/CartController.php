<?php

namespace App\Controller;


use App\Repository\CartRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(CartRepository $repo)
    {
        $cart = $repo->findAll();
            return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }
    
}
