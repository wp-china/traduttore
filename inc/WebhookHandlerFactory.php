<?php
/**
 * WebhookHandlerFactory class.
 *
 * @since 3.0.0
 *
 * @package Required\Traduttore
 */

namespace Required\Traduttore;

use Required\Traduttore\WebhookHandler\{
	Bitbucket, GitHub, GitLab, SourceForge
};
use WP_REST_Request;

/**
 * WebhookHandlerFactory class.
 *
 * @since 3.0.0
 */
class WebhookHandlerFactory {
	/**
	 * Returns a new webhook handler instance for a given project based on the request.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WebhookHandler Webhook handler instance.
	 */
	public function get_handler( WP_REST_Request $request ): ?WebhookHandler {
		$handler = null;

		if ( $request->get_header( 'x-github-event' ) ) {
			$handler = new GitHub( $request );
		} elseif ( $request->get_header( 'x-gitlab-event' ) ) {
			$handler = new GitLab( $request );
		} elseif ( $request->get_header( 'x-event-key' ) ) {
			$handler = new Bitbucket( $request );
		} elseif ( $request->get_header( 'x-allura-signature' ) ) {
			$handler = new SourceForge( $request );
		}

		/**
		 * Filters the determined incoming webhook handler.
		 *
		 * @param WebhookHandler|null $handler Webhook handler instance.
		 * @param WP_REST_Request The current request object.
		 */
		return apply_filters( 'traduttore.webhook_handler', $handler, $request );
	}
}
