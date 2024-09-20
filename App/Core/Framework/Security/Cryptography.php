<?php

namespace App\Core\Framework\Security;

use App\Core\Application\Configuration;
use App\Core\Exceptions\CryptographyException;
use App\Core\Server\Environment;

/**
 * The Cryptography class provides methods for encrypting and decrypting data using OpenSSL.
 */
final class Cryptography
{

	protected $isAvailable = false;
	protected const CIPHER_METHOD = 'aes-256-gcm';
	protected const TAG_LENGTH = 16;

	/**
	 * Constructs a new Cryptography instance.
	 * Checks if the OpenSSL extension is loaded and sets the availability flag accordingly.
	 */
	public function __construct()
	{
		if (extension_loaded('openssl')) {
			$this->isAvailable = true;
		}
	}

	/**
	 * Returns the availability of the OpenSSL extension.
	 *
	 * @return bool True if the OpenSSL extension is available, false otherwise.
	 */
	public function getAvailable()
	{
		return $this->isAvailable;
	}

	/**
	 * Encrypts the given data using the AES-256-GCM cipher.
	 *
	 * @param string $data The data to encrypt.
	 * @return string The encrypted data.
	 * @throws CryptographyException If the OpenSSL extension is not available or the data is invalid.
	 */
	public function encrypt($data)
	{
		$this->validateAvailability();
		$this->validateData($data);

		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));
		$encrypted = openssl_encrypt($data, self::CIPHER_METHOD, $this->getEncryptionKey(), OPENSSL_RAW_DATA, $iv, $tag);

		return base64_encode($iv . $tag . $encrypted);
	}

	/**
	 * Decrypts the given data using the AES-256-GCM cipher.
	 *
	 * @param string $data The data to decrypt.
	 * @return string The decrypted data.
	 * @throws CryptographyException If the OpenSSL extension is not available or the data is invalid.
	 */
	public function decrypt($data)
	{
		$this->validateAvailability();
		$this->validateData($data);

		$c = base64_decode($data);
		$ivlen = openssl_cipher_iv_length(self::CIPHER_METHOD);
		$iv = substr($c, 0, $ivlen);
		$tag = substr($c, $ivlen, self::TAG_LENGTH);
		$ciphertext_raw = substr($c, $ivlen + self::TAG_LENGTH);

		return openssl_decrypt($ciphertext_raw, self::CIPHER_METHOD, $this->getEncryptionKey(), OPENSSL_RAW_DATA, $iv, $tag);
	}

	/**
	 * Generates or retrieves the encryption key.
	 *
	 * @return string The encryption key.
	 * @throws CryptographyException If the OpenSSL extension is not available or the encryption key is not defined.
	 */
	protected function getEncryptionKey()
	{
		$this->validateAvailability();

		$envKey = Environment::getInstance()->getEnvironmentVariable(Configuration::ENV_CRYPTOGRAPHY_KEY_NAME);
		if ($envKey !== false) {
			return $envKey;
		} else {
			throw new CryptographyException('Encryption key not defined.');
		}
	}

	/**
	 * Validates the availability of the OpenSSL extension.
	 *
	 * @throws CryptographyException If the OpenSSL extension is not available.
	 */
	protected function validateAvailability()
	{
		if (!$this->isAvailable) {
			throw new CryptographyException('OpenSSL extension not available.');
		}
	}

	/**
	 * Validates the data to be encrypted or decrypted.
	 *
	 * @param string $data The data to validate.
	 * @throws CryptographyException If the data is not a string.
	 */
	protected function validateData($data)
	{
		if (!is_string($data)) {
			throw new CryptographyException('Invalid data. Data must be a string.');
		}
	}
}