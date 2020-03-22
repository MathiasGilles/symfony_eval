<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/{_locale}")
 */

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(ProductRepository $repo)
    {
        $products = $repo->findAll();
            return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
    /**
     * @Route("/product/new",name="product_new")
     */
    public function new(Product $produit = null,Request $request,TranslatorInterface $translator)
    {
        if ($produit == null) {
            $produit = new Product();
        }   
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form ->isValid()) {

            $fichier = $form->get('photo')->getData();
           
            if ($fichier) {
             
                $nomFicher = uniqid() . '.' . $fichier->guessExtension();
                
                try {
                   
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFicher
                    );
                } catch (FileExeption $e) {
                    $this->addFlash( "danger", "Impossible d'uploader le fichier");
                    return $this->redirectToRoute('product');
                }

                $produit->setPhoto($nomFicher);
            }
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash("success", $translator->trans('added'));
        }

        return $this->render('product/product_new.html.twig',[
            'formProduct' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produt/{id}",name="product_detail")
     */
    public function detail($id,ProductRepository $repo,Cart $cart = null,Request $request,TranslatorInterface $translator)
    {
        $product = $repo->find($id);

        if ($cart == null ) {
            $cart = new Cart;
        }
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(CartType::class, $cart);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cart->setAddedAt(new \DateTime)
                 ->setState(true);
            $product->setCart($cart);
            $manager->persist($cart);
            $manager->flush();
            $this->addFlash("success",$translator->trans('added'));
        }

        return $this->render('product/product_detail.html.twig',[
            'product' => $product,
            'formCart' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/delete/{id}",name="product_delete")
     */
    public function delete(Product $product = null,TranslatorInterface $translator)
    {
        if($product != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($product);
            $manager->flush();

            $this->addFlash("success",$translator->trans('deleted'));
        }
        else {
            $this->addFlash("danger",$translator->trans('notFound'));
        }
        return $this->redirectToRoute('product');
    }
}
