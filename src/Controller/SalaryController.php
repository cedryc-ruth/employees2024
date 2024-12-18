<?php

namespace App\Controller;

use App\Entity\Salary;
use App\Form\SalaryType;
use App\Repository\SalaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/salary')]
final class SalaryController extends AbstractController
{
    #[Route(name: 'app_salary_index', methods: ['GET'])]
    public function index(SalaryRepository $salaryRepository): Response
    {
        return $this->render('salary/index.html.twig', [
            'salaries' => $salaryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_salary_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salary = new Salary();
        $form = $this->createForm(SalaryType::class, $salary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($salary);
            $entityManager->flush();

            return $this->redirectToRoute('app_salary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('salary/new.html.twig', [
            'salary' => $salary,
            'form' => $form,
        ]);
    }

    #[Route('/{emp_no}', name: 'app_salary_show', methods: ['GET'])]
    public function show(Salary $salary): Response
    {
        return $this->render('salary/show.html.twig', [
            'salary' => $salary,
        ]);
    }

    #[Route('/{emp_no}/edit', name: 'app_salary_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salary $salary, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalaryType::class, $salary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_salary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('salary/edit.html.twig', [
            'salary' => $salary,
            'form' => $form,
        ]);
    }

    #[Route('/{emp_no}', name: 'app_salary_delete', methods: ['POST'])]
    public function delete(Request $request, Salary $salary, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salary->getEmp_no(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($salary);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_salary_index', [], Response::HTTP_SEE_OTHER);
    }
}
