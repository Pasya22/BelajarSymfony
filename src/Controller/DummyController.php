<?php

namespace App\Controller;

use App\Entity\Dummy;
use App\Repository\DummyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/dummy', name: 'api_dummy_')]
class DummyController extends AbstractController
{
    private $entityManager;
    private $dummyRepository;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, DummyRepository $dummyRepository, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->dummyRepository = $dummyRepository;
        $this->validator = $validator;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $dummies = $this->dummyRepository->findAll();
        return $this->json($dummies);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $dummy = $this->dummyRepository->find($id);

        if (!$dummy) {
            return $this->json(['error' => 'Entity not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($dummy);
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Mendapatkan data dari form-data
        $name = $request->request->get('name');
        $hobby = $request->request->get('hobby');
        $description = $request->request->get('description'); 
    
        // Pengecekan manual apakah field kosong
        $errorsMessage = [];
        if (empty($name)) {
            $errorsMessage[] = 'Nama Tidak Boleh Kosong';
        }
        if (empty($hobby)) {
            $errorsMessage[] = 'Hobby Tidak Boleh Kosong';
        }
        if (empty($description)) {
            $errorsMessage[] = 'Description Tidak Boleh Kosong';
        }
    
        // Jika ada pesan error, kembalikan response dengan status HTTP_BAD_REQUEST
        if (!empty($errorsMessage)) {
            return $this->json(['errors' => implode(', ', $errorsMessage)], Response::HTTP_BAD_REQUEST);
        }
    
        // Membuat entitas Dummy
        $dummy = new Dummy();
        $dummy->setName($name);
        $dummy->setHobby($hobby);
        $dummy->setDescription($description);
        $dummy->setCreatedAt(new \DateTime());
    
        // Validasi entitas
        $errors = $this->validator->validate($dummy);
    
        // Jika ada error validasi, kirim response dengan error message
        if (count($errors) > 0) {
            $validationErrors = [];
            foreach ($errors as $error) {
                $validationErrors[] = $error->getMessage();
            }
            return $this->json(['errors' => implode(', ', $validationErrors)], Response::HTTP_BAD_REQUEST);
        }
    
        // Jika validasi berhasil, simpan entitas ke database
        $this->entityManager->persist($dummy);
        $this->entityManager->flush();
    
        return $this->json(['success'=> 'Berhasil Ditambahkan', 'data' => $dummy], Response::HTTP_CREATED);
    }
    

    #[Route('/{id}/edit', name: 'edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        // $data = json_decode($request->getContent(), true);

        $dummy = $this->dummyRepository->find($id);

        if (!$dummy) {
            return $this->json(['errors' => 'Data tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        $name = $request->request->get('name');
        $hobby = $request->request->get('hobby');
        $description = $request->request->get('description');


        $dummy->setName($name ?? $dummy->getName());
        $dummy->setHobby($hobby ?? $dummy->getHobby());
        $dummy->setDescription($description ?? $dummy->getDescription());
        $dummy->setUpdatedAt(new \DateTime()); // Assuming you have a setUpdatedAt method for timestamps

        // Validate entity
        if (empty($name) || empty($hobby) || empty($description)) {
            $errorMessages = [];
    
            if (empty($name)) {
                $errorMessages[] = 'Name tidak boleh kosong';
            }
            if (empty($hobby)) {
                $errorMessages[] = 'Hobby tidak boleh kosong';
            }
            if (empty($description)) {
                $errorMessages[] = 'Description tidak boleh kosong';
            }
    
            return $this->json(['errors' => implode(', ', $errorMessages)], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->flush();
    
        return $this->json($dummy, Response::HTTP_OK);
    }
 

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $dummy = $this->dummyRepository->find($id);

        if (!$dummy) {
            return $this->json(['error' => 'Entity not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($dummy);
        $this->entityManager->flush();

        return $this->json(['status' => 'deleted']);
    }
}
