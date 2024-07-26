<?php declare( strict_types=1 );

namespace NodaPay\Button;

/**
 * Verificator Class.
 *
 * This class implements interfaces to verify the CMS, Vendor, and Store configurations.
 * It provides methods to check PHP version compatibility, WordPress version compatibility,
 * WooCommerce activation and version compatibility, and WooCommerce currency compatibility.
 *
 * @package NodaPay
 * @since   1.0.0
 */
class Verificator {

	/**
	 * Compares if the actual version is greater than or equal to the expected version.
	 *
	 * @param string $actual The actual version to compare.
	 * @param string $expected The expected minimum version.
	 *
	 * @return bool True if the actual version is greater than or equal to the expected version, false otherwise.
	 */
	protected function min_version( string $actual, string $expected ): bool {
		return version_compare( $actual, $expected, '>=' );
	}

	/**
	 * Check if PHP version is compatible.
	 *
	 * @param string $version Minimal compatible PHP version.
	 *
	 * @return bool
	 */
	public function is_php_version_compatible( string $version ): bool {
		return $this->min_version( PHP_VERSION, $version );
	}

	/**
	 * Check if WordPress version is compatible.
	 *
	 * @param string $version Minimal compatible WordPress version.
	 *
	 * @return bool
	 */
	public function is_cms_version_compatible( string $version ): bool {
		return $this->min_version( (string) get_bloginfo( 'version' ), $version );
	}
}
