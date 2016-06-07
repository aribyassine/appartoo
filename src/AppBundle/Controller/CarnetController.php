<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Carnet;
use AppBundle\Form\CarnetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Carnet controller.
 *
 * @Route("/")
 * @Security("has_role('ROLE_USER')")
 */
class CarnetController extends Controller
{
    /**
     * Lists all Carnet entities.
     *
     * @Route("/", name="_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $carnets = $em->getRepository('AppBundle:Carnet')->findAll();

        return $this->render('AppBundle:carnet:index.html.twig', array(
            'carnets' => $carnets,
        ));
    }

    /**
     * Creates a new Carnet entity.
     *
     * @Route("/new", name="_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $carnet = new Carnet();
        $carnet->setUser($this->getUser());
        $form = $this->createForm('AppBundle\Form\CarnetType', $carnet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($carnet);
            $em->flush();
            $this->addFlash('success', 'contact enregistrée.');

            return $this->redirectToRoute('_show', array('id' => $carnet->getId()));
        }

        return $this->render('AppBundle:carnet:new.html.twig', array(
            'carnet' => $carnet,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Carnet entity.
     *
     * @Route("/{id}", name="_show")
     * @Method("GET")
     */
    public function showAction(Carnet $carnet)
    {
        if($carnet->getUser()->getUsername() != $this->getUser()->getUsername())
            return $this->createAccessDeniedException('vous ne pouvez pas accéder à cette page');

        $deleteForm = $this->createDeleteForm($carnet);

        return $this->render('AppBundle:carnet:show.html.twig', array(
            'carnet' => $carnet,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Carnet entity.
     *
     * @Route("/{id}/edit", name="_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Carnet $carnet)
    {
        $deleteForm = $this->createDeleteForm($carnet);
        $editForm = $this->createForm('AppBundle\Form\CarnetType', $carnet);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($carnet);
            $em->flush();
            $this->addFlash('success', 'contact mis à jour.');

            return $this->redirectToRoute('_edit', array('id' => $carnet->getId()));
        }

        return $this->render('AppBundle:carnet:edit.html.twig', array(
            'carnet' => $carnet,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Carnet entity.
     *
     * @Route("/{id}", name="_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Carnet $carnet)
    {
        $form = $this->createDeleteForm($carnet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($carnet);
            $em->flush();
            $this->addFlash('success', 'contact supprimé.');
        }

        return $this->redirectToRoute('_index');
    }

    /**
     * Creates a form to delete a Carnet entity.
     *
     * @param Carnet $carnet The Carnet entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Carnet $carnet)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $carnet->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
