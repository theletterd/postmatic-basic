<?php

/**
 * Options tab for choosing modules
 *
 * @since 2.0.0
 *
 */
class Prompt_Admin_Core_Options_Tab extends Prompt_Admin_Options_Tab {
	/** @var  Prompt_Interface_License_Status */
	protected $license_status;

	/**
	 *
	 * @since 2.0.0
	 *
	 * @param Prompt_Options $options
	 * @param array|null $overridden_options
	 * @param Prompt_Interface_License_Status $license_status
	 */
	public function __construct( $options, $overridden_options = null, Prompt_Interface_License_Status $license_status = null ) {
		$this->license_status = $license_status;
		parent::__construct( $options, $overridden_options );
	}

	/**
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Get Started', 'Postmatic' );
	}

	/**
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function slug() {
		return 'core';
	}

	/**
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function render() {

		$parts = array(
			$this->promo_html(),
			html(
				'div class="intro-text welcome"',
				html( 'h2', __( 'Welcome to Replyable', 'Postmatic' ) ),
				html(
					'p',
					__( 'Configure Replyable using the tabs above, or get help below.', 'Postmatic' )
				)
			),
		);

		$parts[] = html(
		    'ul id="replyable-utils"',
            html(
                'li id="util-account"',
                html(
                    'a class="btn-postmatic"',
                    array( 'href' => admin_url( 'options-general.php?page=postmatic-account' ) ),
                    __( 'Manage your account', 'Postmatic' )
                )
            ),
            html(
                'li id="util-contact"',
                html(
                    'a class="btn-postmatic"',
                    array( 'href' => admin_url( 'options-general.php?page=postmatic-contact' ) ),
                    __( 'Contact us', 'Postmatic' )
                )
            ),
            html(
                'li id="util-docs"',
                html(
                    'a class="btn-postmatic" target="_blank"',
                    array( 'href' => 'http://docs.replyable.com' ),
                    __( 'Read the docs', 'Postmatic' )
                )
            )
        );

		return $this->form_wrap( implode( '', $parts ) );
	}

	/**
	 * Disable overridden entry UI table entries.
	 *
	 * @since 2.0.0
	 *
	 * @param array $table_entries
	 */
	protected function override_entries( &$table_entries ) {
		foreach ( $table_entries as $index => $entry ) {
			if ( isset( $this->overridden_options[$entry['name']] ) ) {
				$table_entries[$index]['extra'] = array(
					'class' => 'overridden',
					'disabled' => 'disabled',
				);
			}
		}
	}

	/**
	 * @since 2.0.0
	 * @return string
	 */
	protected function promo_html() {
		$data = array(
			'is_pending_activation' => $this->license_status->is_pending_activation(),
			'is_trial_available' => $this->license_status->is_trial_available(),
			'is_trial_underway' => $this->license_status->is_trial_underway(),
			'is_paying' => $this->license_status->is_paying(),
			'is_key_present' => (bool) $this->options->get( 'prompt_key' ),
			'is_api_transport' => (bool) $this->options->is_api_transport(),
		);

		$template = new Prompt_Template( 'core-options-promo.php' );

		return $template->render( $data );
	}

	/**
	 * @since 2.0.6
	 * @param string $video_id
	 * @return string
	 */
	protected function video_link( $video_id ) {
		return html(
			'a',
			array(
				'class' => 'thickbox video',
				'href' => "https://www.youtube.com/embed/$video_id?autoplay=1&TB_iframe=true",
			),
			html(
				'span',
				__( 'Watch the Video', 'Postmatic' )
			)
		);
	}
}