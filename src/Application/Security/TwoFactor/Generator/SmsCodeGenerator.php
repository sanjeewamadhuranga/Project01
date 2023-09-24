<?php

declare(strict_types=1);

namespace App\Application\Security\TwoFactor\Generator;

use App\Application\Security\TwoFactor\SmsCodeDto;
use App\Domain\Document\Security\Administrator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SmsCodeGenerator
{
    public function __construct(
        private readonly TexterInterface $texter,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function generateAndSend(Administrator $user): void
    {
        $this->texter->send($this->generateMessage($user));
    }

    public function validateCode(string $code): bool
    {
        $smsCode = $this->requestStack->getSession()->get('sms_code');

        if (!$smsCode instanceof SmsCodeDto) {
            return false;
        }

        return $smsCode->isCodeValid($code);
    }

    private function generateMessage(Administrator $user): MessageInterface
    {
        if (null === $user->getPhoneNumber()) {
            throw new InvalidArgumentException(sprintf('User %s do not have phone number.', $user->getUsername()));
        }

        $code = new SmsCodeDto();
        $this->requestStack->getSession()->set('sms_code', $code);

        return new SmsMessage(
            $user->getPhoneNumber(),
            $this->translator->trans('security.2fa.smsTemplate', ['%code%' => $code->code])
        );
    }
}
