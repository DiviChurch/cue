<?php
/**
 * Customizer provider.
 *
 * @package   Cue
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @since     2.0.0
 */

/**
 * Customizer provider class.
 *
 * @package Cue
 * @since   2.0.0
 */
class Cue_Provider_Customize extends Cue_AbstractProvider {
	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 */
	public function register_hooks() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
	}

	/**
	 * Add a Customizer section for selecting playlists for registered players.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public function customize_register( $wp_customize ) {
		$description = '';
		$players     = get_cue_players();
		$themes      = get_cue_themes();

		$wp_customize->add_section( 'cue', array(
			'title'    => __( 'Cue Players', 'cue' ),
			'priority' => 115,
		) );

		if ( ! empty( $players ) ) {
			$this->register_player_controls( $players );
		}

		if ( count( $themes ) > 1 ) {
			$wp_customize->add_setting( 'cue_default_theme', array(
				'capability'        => 'edit_theme_options',
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_key',
				'type'              => 'option',
			) );

			$wp_customize->add_control( 'cue_default_theme', array(
				'choices'     => get_cue_themes(),
				'description' => esc_html__( 'Choose a default theme to use for players.', 'cue' ),
				'label'       => esc_html__( 'Default Theme', 'cue' ),
				'priority'    => 100,
				'section'     => 'cue',
				'settings'    => 'cue_default_theme',
				'type'        => 'select',
			) );
		}
	}

	/**
	 * Register controls to select a playlist for each player.
	 *
	 * @since 2.1.0
	 *
	 * @param array $players Array of players.
	 */
	protected function register_player_controls( $players ) {
		$playlists = get_posts( array(
			'post_type'      => 'cue_playlist',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'asc',
		) );

		if ( empty( $playlists ) ) {
			$playlists = array();

			$description = sprintf(
				__( '<a href="%s">Create a playlist</a> for this player.', 'cue' ),
				admin_url( 'post-new.php?post_type=cue_playlist' )
			);
		} else {
			// Create an array: ID => post_title
			$playlists = array_combine( wp_list_pluck( $playlists, 'ID' ), wp_list_pluck( $playlists, 'post_title' ) );
		}

		$playlists = array( 0 => '' ) + $playlists;

		foreach ( $players as $id => $player ) {
			$id = sanitize_key( $id );

			$wp_customize->add_setting( 'cue_players[' . $id . ']', array(
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'cue_player_' . $id, array(
				'choices'     => $playlists,
				'description' => $description,
				'label'       => $player['name'],
				'section'     => 'cue',
				'settings'    => 'cue_players[' . $id . ']',
				'type'        => 'select',
			) );
		}
	}
}
