<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartRepository $repo)
    {
        $cart = $repo->findAll();

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/cart/add",name="cart_new")
     */
    public function add(Cart $cart = null,Request $request)
    {
        if ($cart == null ) {
            $cart = new Cart;
        }
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(CartType::class, $cart);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cart->setCreatedAt(new \DateTime)
                 ->setState(true);
            $product->setCart();
            $manager->persist($cart);
            $manager->flush();
            $this->addFlash("success","Article ajouté au panier");
        }

        return $this->render('cart/cart_add.html.twig',[
            'formCart' => $form->createView(),
        ]);
    }
}
