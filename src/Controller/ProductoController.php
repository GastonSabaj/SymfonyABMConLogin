<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Form\ProductoType;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Service\AuthenticationService;

use Knp\Component\Pager\PaginatorInterface;


#[Route('/producto')]
class ProductoController extends AbstractController
{
    private $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    #[Route('/', name: 'app_producto_index', methods: ['GET'])]
    public function index(Request $request, ProductoRepository $productoRepository, PaginatorInterface $paginator): Response
    {
        if (!$this->authenticationService->checkAuthentication()) {
            return $this->redirectToRoute('required_loggin');
        }

        $pagination = $paginator->paginate(
            $productoRepository->findAll(), // Query para paginar
            $request->query->getInt('page', 1), // Número de página
            1 // Cantidad de elementos por página
        );
    
        return $this->render('producto/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_producto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->authenticationService->checkAuthentication()) {
            return $this->redirectToRoute('required_loggin');
        }

        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($producto);
            $entityManager->flush();

            $this->addFlash('success', 'A new product has been created successfully!');

            return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('producto/new.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_show', methods: ['GET'])]
    public function show(Producto $producto = null): Response
    {
        if (!$this->authenticationService->checkAuthentication()) {
            return $this->redirectToRoute('required_loggin');
        }

        if (!$producto) {
            $this->addFlash('danger', 'Product not found!');
            return $this->redirectToRoute('app_producto_index');
        }

        return $this->render('producto/show.html.twig', [
            'product' => $producto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_producto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Producto $producto = null, EntityManagerInterface $entityManager): Response
    {
        if (!$this->authenticationService->checkAuthentication()) {
            return $this->redirectToRoute('required_loggin');
        }

        if (!$producto) {
            $id = $request->attributes->get('id');
            $this->addFlash('danger', sprintf('The product of id "%s" does not match with an existent product!', $id));
            return $this->redirectToRoute('app_producto_index');
        }

        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'A product has been edited successfully!');

            return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('producto/edit.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_delete', methods: ['POST'])]
    public function delete(Request $request, Producto $producto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$producto->getId(), $request->request->get('_token'))) {
            $entityManager->remove($producto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
    }
}
