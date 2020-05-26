<?php
/**
 * Webhook handler factory
 *
 * @since 3.0.0
 *
 * @package Required\Traduttore
 */

namespace Required\Traduttore;

use Required\Traduttore\WebhookHandler\{
	Bitbucket, Gitea, GitHub, GitLab
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

		/**
		 * Gitea's header contains both GitHub and Gogs events, so it should be ranked first
		 */
		if ( $request->get_header( 'x-gitea-event' ) ) {
			$handler = new Gitea( $request );
		} elseif ( $request->get_header( 'x-github-event' ) ) {
			$handler = new GitHub( $request );
		} elseif ( $request->get_header( 'x-gitlab-event' ) ) {
			$handler = new GitLab( $request );
		} elseif ( $request->get_header( 'x-event-key' ) ) {
			$handler = new Bitbucket( $request );
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
