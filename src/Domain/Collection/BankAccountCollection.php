<?php

declare(strict_types=1);

namespace App\Domain\Collection;

use App\Domain\Document\Company\BankAccount;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends ArrayCollection<int, BankAccount>
 */
class BankAccountCollection extends ArrayCollection
{
    public function getDefault(): ?BankAccount
    {
        $defaultAccount = $this->filter(fn (BankAccount $account) => $account->isDefault())->first();

        if (false === $defaultAccount) {
            return null;
        }

        return $defaultAccount;
    }

    public function setDefault(int $index = 0): void
    {
        foreach ($this as $key => $account) {
            $account->setDefault($key === $index);
        }
    }
}
