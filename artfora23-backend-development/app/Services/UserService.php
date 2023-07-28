<?php

namespace App\Services;

use App\Facades\TokenGenerator;
use App\Jobs\SendMailJob;
use App\Mails\ForgotPasswordMail;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Artel\Support\Services\EntityService;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

/**
 * @property UserRepository $repository
 * @property PasswordResetRepository $passwordResetRepository
 *
 * @mixin UserRepository
 */
class UserService extends EntityService
{
    protected PasswordResetRepository $passwordResetRepository;

    public function __construct()
    {
        $this->setRepository(UserRepository::class);

        $this->passwordResetRepository = app(PasswordResetRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('role_id')
            ->filterByQuery(['username', 'email'])
            ->getSearchResults();
    }

    public function create($data)
    {
        $data['role_id'] = Arr::get($data, 'role_id', Role::USER);
        $data['password'] = Hash::make($data['password']);
        $data['email_verification_token'] = TokenGenerator::getRandom();
        $data['email_verification_token_sent_at'] = Carbon::now();

        return $this->repository
            ->force()
            ->updateOrCreate([ 'email' => $data['email'] ], $data);
    }

    public function update($where, $data)
    { 
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
            return $this->repository->update($where, $data);
       

    }

    public function verifyEmail($code): User
    {
        $user = $this->repository
            ->withTrashed()
            ->findBy('email_verification_token', $code);

        $this->repository->force()->update(['email_verification_token' => $code], [
            'email_verification_token' => null,
            'email_verification_token_sent_at' => null,
            'email_verified_at' => Carbon::now(),
            'deleted_at' => null
        ]);

        return $user;
    }

    public function forgotPassword($email)
    {
        $user = $this->repository->findBy('email', $email);

        if (empty($user)) {
            return;
        }

        $record = $this->passwordResetRepository->create([
            'email' => $email,
            'token' => TokenGenerator::getRandom(64),
            'created_at' => Carbon::now()
        ]);

        Mail::queue(new ForgotPasswordMail($email, [
            'user' => $user,
            'hash' => $record['token']
        ]));
    }

    public function restorePassword($restoreToken, $password)
    {
        $record = $this->passwordResetRepository->findBy('token', $restoreToken);

        $this->update(
            ['email' => $record['email']],
            ['password' => $password]
        );

        $this->passwordResetRepository->delete(['token' => $restoreToken]);
    }

    public function enable2FA($userId, $type)
    {
        $data = [ '2fa_type' => $type ];

        if ($type === User::SMS_2FA_TYPE) {
            $data['phone_verified_at'] = Carbon::now();
        }

        $this->update($userId, $data);
    }


    
}
