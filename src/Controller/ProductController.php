<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductEditType;
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
    public function new(Product $product = null,Request $request)
    {
        if ($product == null ) {
            $product = new Product();
        }
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProductType::class, $product);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

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
                $product->setPhoto($nomFicher);
            }

            $manager->persist($product);
            $manager->flush();
            $this->addFlash("success","Product ajoutée");
        }

        return $this->render('product/product_new.html.twig',[
            'formProduct' => $form->createView(),
            'editTitle' => $product->getId() != null,
            'editMode' => $product->getId() != null,
        ]);
    }

    /**
     * @Route("/product/edit/{id}",name="product_edit")
     */
    public function edit(Product $product = null,Request $request)
    {
        if($product != null){
        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->persist($product);
            $pdo->flush();
            
            $this->addFlash("success","Produit mise à jour");
        }

        return $this->render('product/product_edit.html.twig',[
            "product" => $product,
            "form_edit" => $form->createView(),
        ]);
        }
        else{
            $this->addFlash("danger","Produit introuvable");
        }
    }

    /**
     * @Route("/product/{id}",name="product_detail")
     */
    public function detail($id,ProductRepository $repo)
    {
        $product = $repo->find($id);

        return $this->render('product/product_detail.html.twig',[
            'product' => $product,
        ]);
    }

    /**
     * @Route("/delete/{id}",name="product_delete")
     */
    public function delete(Product $product = null)
    {
        if($product != null){
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($product);
            $manager->flush();

            $this->addFlash("success","Product remove");
        }
        else {
            $this->addFlash("danger","Product not found");
        }
        return $this->redirectToRoute('product');
    }
}
