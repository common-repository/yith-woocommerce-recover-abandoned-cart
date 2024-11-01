<?php

$settings = array(

	'general' => array(

		'section_general_settings' => array(
			'name' => __( 'General settings', 'yith-woocommerce-recover-abandoned-cart' ),
			'type' => 'title',
			'id'   => 'ywrac_section_general',
		),

		'enabled'                  => array(
			'name'    => __( 'Enable Recover Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc'    => '',
			'id'      => 'ywrac_enabled',
			'type'    => 'checkbox',
			'default' => 'yes',
		),

		'cut_off_time'             => array(
			'name' => __( 'Cart abandoned cut-off time', 'yith-woocommerce-recover-abandoned-cart' ),
			'desc' => __( 'Minutes that have to pass to consider a cart abandoned', 'yith-woocommerce-recover-abandoned-cart' ),
			'id'   => 'ywrac_cut_off_time',
			'type' => 'text',
			'std'  => '60',
		),

		'section_end_form'         => array(
			'type' => 'sectionend',
			'id'   => 'ywrac_section_general_end_form',
		),
	),

);

return apply_filters( 'yith_ywrac_panel_settings_options', $settings );
