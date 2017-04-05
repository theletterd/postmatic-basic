<?php
/**
 * @var Prompt_Interface_Subscribable[] $lists
 * @var string $invite_introduction Only present for invite emails.
 */
?>

<div class="padded">
	<?php if ( !empty( $invite_introduction ) ) : ?>
		<?php /* translators: %s is site name */ ?>
		<h3><?php printf( __( 'An invitation from %s', 'Postmatic' ), get_bloginfo( 'name' ) ); ?></h3>
		<p><?php echo $invite_introduction; ?></p>
	<?php else : ?>
		<h4>
			<strong>
				<?php
				if ( count( $lists ) < 2 ) {
					printf(
						__( "Action required: There’s one more step to confirm your subscription to %s.", 'Postmatic' ),
						$lists[0]->subscription_object_label()
					);
				} else {
					_e( "Action required: There's one more step to confirm your subscription.", 'Postmatic' );
				}
				?>
			</strong>
		</h4>
	<?php endif; ?>

	{{{notice_html}}}

	<?php if ( count( $lists ) < 2 ) : ?>
		<p>
			<span class="alert">
				<?php
				printf(
					__(
						'To confirm your subscription, please reply with the word <a href="%s"><strong>agree</strong></a>.',
						'Postmatic'
					),
					add_query_arg(
						array(
							'subject' => rawurlencode( __( 'Press send to confirm.', 'Postmatic' ) ),
							'body' => __( 'agree', 'Postmatic' ),
						),
						'mailto:{{{reply_to}}}'
					)
				);
				?>
			</span>
		</p>
	<?php else : ?>
		<p>
			<span class="alert">
				<?php
				printf(
					__(
						'Please choose a subscription option:',
						'Postmatic'
					),
					get_bloginfo( 'name' )
				);
				?>
			</span>
		</p>
		<ol>
			<?php foreach ( $lists as $list ) : ?>
				<li>
					<?php echo $list->select_reply_prompt(); ?>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</div>

<div class="padded gray">

	<p class="abuse">
		<?php
		printf(
			__(
				'If you did not initiate this subscription please ignore this email or forward it to %s.',
				'Postmatic'
			),
			Prompt_Core::is_api_transport() ? Prompt_Core::ABUSE_EMAIL : get_option( 'admin_email' )
		)
		?>
	</p>

</div>