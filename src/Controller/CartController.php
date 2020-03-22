<?php

namespace App\Controller;


use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/{_locale}")
 */

class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart")
     */
    public function index(CartRepository $repo)
    {
        $cart = $repo->findAll();
            return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/cart/delete/{id}",name="cart_delete")
     */
    public function delete(Cart $cart = null,TranslatorInterface $translator)
    {
        if($cart != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($cart);
            $manager->flush();

            $this->addFlash("success",$translator->trans('deleted'));
        }
        else {
            $this->addFlash("danger",$translator->trans('notFound'));
        }
        return $this->redirectToRoute('cart');
    }
    
}
