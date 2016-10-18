<?php

class Prompt_Subscribing {
	const SUBSCRIBE_ACTION = 'prompt_subscribe';

	/** @var array Subscribable types - could be extended into a registration system  */
	protected static $subscribables = array(
		'Prompt_Site' => null,
		'Prompt_Post' => 'WP_Post',
		'Prompt_User' => 'WP_User',
	);

	/**
	 * Instantiate a subscribable object.
	 *
	 * @param null|WP_Post|WP_user|int|string $object Optional object to pass to the constructor.
	 * @return Prompt_Interface_Subscribable|null
	 */
	public static function make_subscribable( $object = null ) {

		if ( is_string( $object ) ) {
			return self::make_subscribable_from_slug( $object );
		}
		
		if (
			is_a( $object, 'WP_Post' ) and
			in_array( $object->post_type, Prompt_Core::$options->get( 'site_subscription_post_types' ) )
		) {
			return new Prompt_Post( $object );
		}

		$subscribables = array_diff_key( self::$subscribables, array( 'Prompt_Post' => true ) );
		foreach ( $subscribables as $subscribable_type => $init_object_type ) {
			if ( is_a( $object, $init_object_type ) )
				return new $subscribable_type( $object );
		}

		return apply_filters(
			'prompt/subscribing/make_subscribable',
			new Prompt_Site(),
			$object
		);
	}

	/**
	 * Instantiate a subscribable object given its slug.
	 * 
	 * @since 2.0.0
	 * @param string $slug
	 * @return Prompt_Interface_Subscribable|null
	 */
	public static function make_subscribable_from_slug( $slug ) {

		$parts = explode( '/', $slug );

		$id = null;
		
		if ( is_numeric( $parts[count($parts)-1] ) ) {
			$id = intval( array_pop( $parts ) );
		}

		$class = 'Prompt_' . ucwords( $parts[0], '_' );

		if ( class_exists( $class ) ) {
			return new $class( $id );
		}

		return apply_filters(
			'prompt/subscribing/make_subscribable',
			null,
			$slug
		);
	}

	/**
	 * Get a text identifier (slug) for a list that can be used later to remake it.
	 *
	 * @since 2.0.0
	 * @param Prompt_Interface_Subscribable $list
	 * @return string
	 */
	public static function get_subscribable_slug( Prompt_Interface_Subscribable $list ) {

		$list_class = get_class( $list );

		if ( strpos( $list_class, 'Prompt_' ) == 0 ) {
			return strtolower( str_replace( 'Prompt_', '', $list_class ) ) . '/' . $list->id();
		}

		return apply_filters( 'prompt/subscribing/get_subscribable_slug', '', $list );
	}

	/**
	 * Get registered subscribable types in fixed order.
	 * @return array
	 */
	public static function get_subscribable_classes() {
		return apply_filters( 'prompt/subscribing/get_subscribable_classes', array_keys( self::$subscribables ) );
	}

	/**
	 * Get the lists enabled for new subscribers to choose from.
	 *
	 * @since 2.0.0
	 *
	 * @return Prompt_Interface_Subscribable[]
	 */
	public static function get_signup_lists() {
		$lists = array();

		if ( Prompt_Core::$options->get( 'enable_post_delivery' ) ) {
			$lists[] = new Prompt_Site();
		}

		$lists = apply_filters( 'prompt/subscribing/get_signup_lists', $lists );

		// Always have something to sign up for
		if ( empty( $lists ) ) {
			$lists[] = new Prompt_Site();
		}

		return $lists;
	}

}