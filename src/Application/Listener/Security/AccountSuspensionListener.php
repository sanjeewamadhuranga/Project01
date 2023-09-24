<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Domain\Document\Security\Administrator;
use App\Domain\Event\User\AccountSuspendedEvent;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Infrastructure\Repository\Security\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;

#[AsEventListener]
class AccountSuspensionListener
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserRepository $userRepository,
        private readonly ManagerPortalRoleRepository $roleRepository
    ) {
    }

    public function __invoke(AccountSuspendedEvent $event): void
    {
        $user = $event->getUser();

        $keyPersonelRole = $this->roleRepository->findOneBy(['name' => 'ROLE__ADMIN']);
        if (is_null($keyPersonelRole)) {
            return;
        }

        $keyPersonel = iterator_to_array($this->userRepository->getUsersByRole($keyPersonelRole));
        if (0 === count($keyPersonel)) {
            return;
        }

        $message = (new TemplatedEmail())
            ->bcc(...array_map(static fn (Administrator $user) => $user->getEmail(), $keyPersonel))
            ->subject('User account suspension')
            ->htmlTemplate('mail/account_suspension.html.twig')
            ->context(['user' => $user])
        ;

        $this->mailer->send($message);
    }
}
