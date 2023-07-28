<?php

namespace App\Services;

use App\Facades\TokenGenerator;
use App\Mails\TwoFactorAuthenticationMail;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Artel\Support\Services\EntityService;
use App\Repositories\TwoFactorAuthEmailRepository;
use Illuminate\Support\Facades\Mail;

/**
 * @mixin TwoFactorAuthEmailRepository
 * @property TwoFactorAuthEmailRepository $repository
 */
class TwoFactorAuthEmailService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(TwoFactorAuthEmailRepository::class);
    }

    public function search($filters)
    {
        return $this
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->filterBy('email')
            ->filterBy('code')
            ->filterByActuality()
            ->getSearchResults();
    }

    public function send($email)
    {
        $code = TokenGenerator::getCode();

        $this->repository->create([
            'email' => $email,
            'code' => $code
        ]);

        Mail::queue(new TwoFactorAuthenticationMail($email, [ 'code' => $code ]));
    }

    public function check(string $email, string $code): bool
    {
        $records = $this->search([
            'email' => $email,
            'code' => $code,
            'only_actual' => true
        ]);

        return (bool)$records->total();
    }
}
