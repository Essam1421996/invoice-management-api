<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function view(User $user, Contract $contract)
    {
        return (int) $user->tenant_id === (int) $contract->tenant_id;
    }
}
