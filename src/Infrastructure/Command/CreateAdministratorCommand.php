<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Infrastructure\Repository\Security\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:administrator:create')]
class CreateAdministratorCommand extends Command
{
    public function __construct(private readonly UserRepository $userRepository, private readonly ManagerPortalRoleRepository $roleRepository, private readonly ValidatorInterface $validator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates new administrator account.')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('roles', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Roles');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $selectedRoles = $input->getArgument('roles');

        $user = new Administrator();
        $user->setEmail($email);
        $user->setPlainPassword($password);

        $roles = $this->roleRepository->findAll();
        foreach ($roles as $role) {
            if (in_array($role->getName(), $selectedRoles, true)) {
                $user->addManagerPortalRole($role);
            }
        }

        $this->userRepository->save($user);
        (new SymfonyStyle($input, $output))->success('User has been created!');

        return self::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $roles = $this->roleRepository->findAll();
        $roleNames = array_map(static fn (ManagerPortalRole $role) => $role->getName(), $roles);
        $default = array_search('ROLE__ADMIN', $roleNames, true);

        $questions = [
            'email' => (new Question('Please enter email address: '))->setValidator(function ($value) {
                if (is_null($value)) {
                    throw new Exception('Email cannot be empty.');
                }

                if (count($this->validator->validate($value, [new Email()])) > 0) {
                    throw new Exception('Incorrect email address, please provide valid one.');
                }

                if (null !== $this->userRepository->findOneBy(['email' => $value])) {
                    throw new Exception('User with given email address already exists.');
                }

                return $value;
            }),
            'password' => (new Question('Please enter password: '))->setHidden(true)->setValidator(function ($value) {
                if (is_null($value) || strlen($value) < 5) {
                    throw new Exception('Password too short, at least 5 characters required.');
                }

                return $value;
            }),
            'roles' => (new ChoiceQuestion('Please select roles:', $roleNames, $default))->setMultiselect(true),
        ];

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        foreach ($questions as $name => $question) {
            $answer = $input->getArgument($name);
            while (is_null($answer) || (is_array($answer) && 0 === count($answer))) {
                $answer = $helper->ask($input, $output, $question);
                $input->setArgument($name, $answer);
            }
        }
    }
}
