<?php

namespace App\Manager\TelegramBot;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class ConfirmationCodeManager
{
	private \Redis $redis;
	public function __construct(private readonly CacheInterface $cache, \Redis $redis)
	{
		$this->redis = $redis;
	}

	protected function buildKey(string $type, string $id): string
	{
		return sprintf('confirm_code_%s_%s', $type, $id);
	}

	public function generateCode(string $type, string $id, $ttl = 300): string
	{
		$code = (string) random_int(100000, 999999);
		$key = $this->buildKey($type, $id);

		try {
			$this->cache->deleteItem($key);

			$item = $this->cache->getItem($key);
			$item->set($code);
			$item->expiresAfter($ttl); // time after that the key will be deleted from the Redis Cache

			$this->cache->save($item);
		} catch (\Psr\Cache\InvalidArgumentException $e) {
			throw new \RuntimeException('Помилка при збереженні в кеш Redis', 0, $e);
		}

		try {
			$this->redis->setex($key, $ttl, $code);
		} catch (\Throwable $e) {

		}

		return $code;
	}

	public function getCode(string $type, string $id): ?string
	{
		$key = $this->buildKey($type, $id);

		try {
			$item = $this->cache->getItem($key);
			return $item->isHit() ? $item->get() : null;
		} catch (\Psr\Cache\InvalidArgumentException $e) {
			return null;
		}
	}


	public function isValidCode(string $type, string $id, string $inputCode): bool
	{
		$storedCode = $this->getCode($type, $id);

		return $storedCode !== null && $storedCode === $inputCode;
	}

	public function clearCode(string $type, string $id): void
	{
		$key = $this->buildKey($type, $id);

		try {
			$this->cache->deleteItem($key);
		} catch (\Throwable $e) {

		}

		try {
			$this->redis->del($key);
		} catch (\Throwable $e) {

		}
	}

	public function findChatByCode(string $type, string $code): ?string
	{
		$pattern = sprintf("confirm_code_%s_*", $type);

		error_log("findChatByCode called with type=$type and code=$code");
		error_log("pattern=$pattern");

		foreach ($this->redis->keys($pattern) as $key) {
			if ($this->redis->get($key) === $code) {
				return explode('_', $key)[3] ?? null;
			}
		}

		error_log("No match found for code=$code");

		return null;
	}

	public function getCodeTTL(string $type, string $id): int
	{
		$key = $this->buildKey($type, $id);
		$pattern = '*' . $key;
		$keys = $this->redis->keys($pattern); // знайде всі підходящі ключі (може бути 1 або кілька)

		if (!empty($keys)) {
			// беремо перший знайдений ключ
			$ttl = $this->redis->ttl($keys[0]);
			return $ttl > 0 ? $ttl : 0;
		}

		return 0;
	}
}