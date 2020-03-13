<?php

namespace App\Controller;


use App\Repository\ShoppingCartRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ShoppingCartController extends AbstractController
{
    /**
     * @Route("/shopping_cart", name="shopping_cart")
     */
    public function index(ShoppingCartRepository $repo)
    {
        $shoppingCart = $repo->findAll();

        return $this->render('shopping_cart/index.html.twig', [
            'shoppingCart' => $shoppingCart,
        ]);
    }

    /**
     * @Route("/shopping_cart/new",name="shopping_cart_new")
     * @Route("/shopping_cart/edit/{id}",name="shopping_cart_edit")
     */
    public function new()
    {
        
    }

    /**
     * @Route("/shopping_cart/delete/{id}",name="shopping_cart_delete")
     */
    public function delete()
    {

    }
}

