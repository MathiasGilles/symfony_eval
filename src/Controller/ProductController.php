<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function new(Product $produit = null,Request $request)
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
                    $this->addFlash('danger', "Impossible d'uploader le fichier");
                    return $this->redirectToRoute('product');
                }

                $produit->setPhoto($nomFicher);
            }
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash("success", "Produit ajoutée");
        }

        return $this->render('product/product_new.html.twig',[
            'formProduct' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produt/{id}",name="product_detail")
     */
    public function detail($id,ProductRepository $repo)
    {
        $product = $repo->find($id);

        return $this->render('product/product_detail.html.twig',[
            'product' => $product,
        ]);
    }

    /**
     * @Route("/produit/delete/{id}",name="product_delete")
     */
    public function delete(Product $product = null)
    {
        if($product != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($product);
            $manager->flush();

            $this->addFlash("success","Produit supprimée");
        }
        else {
            $this->addFlash("danger", "Produit introuvable");
        }
        return $this->redirectToRoute('product');
    }
}
