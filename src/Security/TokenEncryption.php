<?php

declare( strict_types = 1 );

namespace ProfessionalWiki\ExternalContent\Security;

use RuntimeException;

/**
 * Handles encryption and decryption of GitHub access tokens using AES-256-GCM
 */
class TokenEncryption {

	private const CIPHER_METHOD = 'aes-256-gcm';
	private const IV_LENGTH = 12; // 96 bits for GCM

	private string $encryptionKey;

	/**
	 * @param string $encryptionKey Base64-encoded 256-bit encryption key
	 */
	public function __construct( string $encryptionKey ) {
		if ( empty( $encryptionKey ) ) {
			throw new RuntimeException( 'Encryption key cannot be empty' );
		}

		$decodedKey = base64_decode( $encryptionKey, true );
		if ( $decodedKey === false || strlen( $decodedKey ) !== 32 ) {
			throw new RuntimeException( 'Invalid encryption key format. Must be base64-encoded 256-bit key' );
		}

		$this->encryptionKey = $decodedKey;
	}

	/**
	 * Encrypts a token using AES-256-GCM
	 *
	 * @param string $plainToken The token to encrypt
	 * @return string Base64-encoded encrypted data with format: iv.tag.ciphertext
	 */
	public function encrypt( string $plainToken ): string {
		if ( empty( $plainToken ) ) {
			throw new RuntimeException( 'Token to encrypt cannot be empty' );
		}

		// Generate a random IV (Initialization Vector)
		$iv = random_bytes( self::IV_LENGTH );

		$tag = '';
		$ciphertext = openssl_encrypt(
			$plainToken,
			self::CIPHER_METHOD,
			$this->encryptionKey,
			OPENSSL_RAW_DATA,
			$iv,
			$tag,
			'', // Additional authenticated data (empty)
			16  // Tag length in bytes
		);

		if ( $ciphertext === false ) {
			throw new RuntimeException( 'Encryption failed: ' . openssl_error_string() );
		}

		// Combine IV, tag, and ciphertext
		$encrypted = base64_encode( $iv . $tag . $ciphertext );

		return $encrypted;
	}

	/**
	 * Decrypts a token encrypted with encrypt()
	 *
	 * @param string $encryptedToken Base64-encoded encrypted data
	 * @return string The decrypted token
	 */
	public function decrypt( string $encryptedToken ): string {
		if ( empty( $encryptedToken ) ) {
			throw new RuntimeException( 'Encrypted token cannot be empty' );
		}

		$decoded = base64_decode( $encryptedToken, true );
		if ( $decoded === false ) {
			throw new RuntimeException( 'Invalid encrypted token format' );
		}

		// Extract IV (12 bytes), tag (16 bytes), and ciphertext
		if ( strlen( $decoded ) < self::IV_LENGTH + 16 ) {
			throw new RuntimeException( 'Encrypted token is too short' );
		}

		$iv = substr( $decoded, 0, self::IV_LENGTH );
		$tag = substr( $decoded, self::IV_LENGTH, 16 );
		$ciphertext = substr( $decoded, self::IV_LENGTH + 16 );

		$plaintext = openssl_decrypt(
			$ciphertext,
			self::CIPHER_METHOD,
			$this->encryptionKey,
			OPENSSL_RAW_DATA,
			$iv,
			$tag
		);

		if ( $plaintext === false ) {
			throw new RuntimeException( 'Decryption failed: ' . openssl_error_string() );
		}

		return $plaintext;
	}

	/**
	 * Generates a new encryption key suitable for use with this class
	 *
	 * @return string Base64-encoded 256-bit encryption key
	 */
	public static function generateKey(): string {
		return base64_encode( random_bytes( 32 ) );
	}
}
