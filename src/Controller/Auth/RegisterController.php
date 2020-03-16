<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RegisterController
 * @package App\Controller\Auth
 */
class RegisterController extends AbstractController
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RegisterController constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em
    ) {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     * @Route("/register", name="app_register", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request, ValidatorInterface $validator)
    {
        $constraint = $this->getValidationRules();

        $data = $request->request->all();
        $errors = $validator->validate($data, $constraint);

        if ($errors->count() > 0) {
            return $this->json(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $password = $this->encoder->encodePassword($user, $request->get('password'));
        $user->setEmail($request->get('email'))
            ->setPassword($password)
            ->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'User has been registered',
        ]);
    }

    /**
     * @return Assert\Collection
     * @throws \Exception
     */
    private function getValidationRules(): Assert\Collection
    {
        return new Assert\Collection([
            'first_name' => new Assert\Length(['min' => 1]),
            'last_name' => new Assert\Length(['min' => 1]),
            'password' => new Assert\Length(['min' => 6]),
            'email' => [
                new Assert\Email(),
            ],
        ]);
    }
}
