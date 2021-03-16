<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

class BankAccountTransformer extends BaseTransformer
{
	public static $excludeable = [
		'deleted_at',
	];

	public function transformData($model)
	{

		if (!is_null($model->bank_card_number)) {
			$model->bank_card_number = $this->getTransferBankCardNumber($model->bank_card_number);
		}

		$bankAccount = array_except($model->toArray(), self::$excludeable);

		return $bankAccount;
	}

	public function getTransferBankCardNumber($bankCardNumber)
	{

		$match = str_split($bankCardNumber, 4);
		for ($i = 0; $i < count($match) - 1; $i++) {
			$match[$i] = '****';
		}

		return implode(' ', $match);
	}
}