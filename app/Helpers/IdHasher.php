<?php

	namespace App\Helpers;

	use Hashids\Hashids;

	class IdHasher
	{
		protected static function getHasher()
		{
			return new Hashids(env('APP_KEY'), 10);
		}

		public static function encode($id)
		{
			return static::getHasher()->encode($id);
		}

		public static function decode($hash)
		{
			$decoded = static::getHasher()->decode($hash);
			return !empty($decoded) ? $decoded[0] : null;
		}
	}
