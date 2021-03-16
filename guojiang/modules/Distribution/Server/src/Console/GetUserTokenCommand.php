<?php

namespace GuoJiangClub\Distribution\Server\Console;

use Illuminate\Console\Command;
use GuoJiangClub\Component\User\Repository\UserRepository;

class GetUserTokenCommand extends Command
{
	protected $signature = 'user:token {user}';

	protected $description = 'get user token';

	public function handle(UserRepository $userRepository)
	{
		$user_id = $this->argument('user');
		if (!$user_id) {
			return false;
		}

		$user = $userRepository->find($user_id);

		$this->info('Bearer ' . $user->createToken($user_id)->accessToken);
	}
}