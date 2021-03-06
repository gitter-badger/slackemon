<?php

// Chromatix TM 04/04/2017
// Templating/output specific functions for Slackemon Go

function slackemon_readable_moveset( $moves, $types, $include_bullets = false, $include_pp = false ) {

  $output = '';
  $moves = slackemon_sort_battle_moves( $moves, $types );

  foreach ( $moves as $move ) {

    if ( $output ) { $output .= "\n"; }

    $move_data = slackemon_get_move_data( $move->name );

    $output .= (
      ( $include_bullets ? '• ' : '' ) .
      pokedex_readable( $move->name ) . ' ' .
      '(' .
      ( in_array( ucfirst( $move_data->type->name ), $types ) ? '*' : '' ) .
      pokedex_readable( $move_data->type->name ) .
      ( in_array( ucfirst( $move_data->type->name ), $types ) ? '*' : '' ) .
      ', x' . ( $move_data->power ? $move_data->power : 0 ) .
      ( $include_pp ? ', ' . $move->{'pp-current'} . '/' . $move->pp : '' ) .
      ')'
    );

  }

  return $output;

} // Function slackemon_readable_moveset

function slackemon_condensed_moveset( $moves, $types, $abbrev = false ) {

  $output = '';

  $moves = slackemon_sort_battle_moves( $moves, $types );

  foreach ( $moves as $move ) {
    if ( $output ) { $output .= " / "; }
    $move_data = slackemon_get_move_data( $move->name );
    $output .= (
      pokedex_readable( $move->name, true, $abbrev ) . ' ' .
      'x' . ( $move_data->power ? $move_data->power : 0 )
    );
  }

  return $output;

} // Function slackemon_condensed_moveset

function slackemon_get_gender_symbol( $gender, $space_location = 'before' ) {

  $gender_symbols = [
    'male'   => ( 'before' === $space_location ? ' ' : '' ) . '♂' . ( 'after' === $space_location ? ' ' : '' ),
    'female' => ( 'before' === $space_location ? ' ' : '' ) . '♀' . ( 'after' === $space_location ? ' ' : '' ),
    false    => '',
  ];

  return $gender_symbols[ $gender ];

} // Function slackemon_get_gender_symbol

function slackemon_get_gender_pronoun( $gender ) {

  $pronouns = [
    'male'   => 'he',
    'female' => 'she',
    false    => 'it',
  ];

  return $pronouns[ $gender ];

} // Function slackemon_get_gender_pronoun

function slackemon_appraise_ivs( $ivs, $include_emoji = true, $abbrev = false ) {

  $ivs_percentage = slackemon_get_iv_percentage( $ivs );

  if ( 100 == $ivs_percentage ) {
    $ivs_appraisal = 'Perfect IVs'   . ( $include_emoji ? ' :heart_eyes:' : '');
  } else if ( $ivs_percentage >= 80 ) {
    $ivs_appraisal = ( $abbrev ? 'Exc.' : 'Excellent' ) . ' IVs' . ( $include_emoji ? ' :tada:' : '');
  } else if ( $ivs_percentage >= 60 ) {
    $ivs_appraisal = 'Good IVs'      . ( $include_emoji ? ' :thumbsup:' : '');
  } else if ( $ivs_percentage >= 40 ) { // Emoji for this was :wavy_dash: but need something better...
    $ivs_appraisal = ( $abbrev ? 'Avg' : 'Average' ) . ' IVs' . ( $include_emoji ? ' ' : '');
  } else if ( $ivs_percentage >= 20 ) {
    $ivs_appraisal = 'Low IVs'       . ( $include_emoji ? ' :arrow_heading_down:' : '');
  } else if ( $ivs_percentage >= 0 ) {
    $ivs_appraisal = 'Poor IVs'      . ( $include_emoji ? ' :thumbsdown:' : '');
  } else {
    $ivs_appraisal = 'Unknown IVs'   . ( $include_emoji ? ' :grey_question:' : '');
  }

  return $ivs_appraisal;

} // Function slackemon_appraise_ivs

function slackemon_emojify_types( $type_string, $include_text = true, $emoji_position = 'before' ) {

  $type_string = preg_replace_callback(
    '|(\S*)|',
    function( $matches ) use ( $include_text, $emoji_position ) {
      if ( ! $matches[1] ) { return ''; }
      return (
        ( $include_text && 'after' === $emoji_position ? $matches[1] . ' ' : '' ) .
        ':type-' . strtolower( $matches[1] ) . ':' .
        ( $include_text && 'before' === $emoji_position ? ' ' . $matches[1] : '' )
      );
    },
    $type_string
  );

  return $type_string;

} // Function slackemon_emojify_types

function slackemon_get_happiness_emoji( $happiness_rate ) {

  // For reference, possible base happiness rates are 0, 35, 70, 90, 100 and 140
  // Happiness is capped at 0 and 255

  if ( ! $happiness_rate ) {
    $happiness_emoji = ':unamused:'; // 0%
  } else if ( $happiness_rate <= 10 ) { // 3%
    $happiness_emoji = ':pensive:';
  } else if ( $happiness_rate <= 30 ) { // 11%
    $happiness_emoji = ':disappointed:';
  } else if ( $happiness_rate <= 50 ) { // 19%
    $happiness_emoji = ':slightly_frowning_face:';
  } else if ( $happiness_rate <= 70 ) { // 27%
    $happiness_emoji = ':thinking_face:';
  } else if ( $happiness_rate <= 95 ) { // 37%
    $happiness_emoji = ':neutral_face:';
  } else if ( $happiness_rate <= 120 ) { // 47%
    $happiness_emoji = ':slightly_smiling_face:';
  } else if ( $happiness_rate <= 150 ) { // 58%
    $happiness_emoji = ':smiley:';
  } else if ( $happiness_rate <= 180 ) { // 70%
    $happiness_emoji = ':smile:';
  } else if ( $happiness_rate <= 230 ) { // 90%
    $happiness_emoji = ':relaxed:';
  } else if ( $happiness_rate <= 254 ) { // 99%
    $happiness_emoji = ':sunglasses:';
  } else if ( $happiness_rate == 255 ) { // 100%
    $happiness_emoji = ':heart_eyes:';
  } else {
    $happiness_emoji = ''; // This shouldn't happen    
  }

  return $happiness_emoji;

} // Function slackemon_get_happiness_emoji

function slackemon_get_nature_emoji( $nature ) {

  // TODO: Try to make sure none of the below are the same as any happiness emoji

  $emoji = [
    'adamant' => ':triumph:',
    'bashful' => ':blush:',
    'bold'    => ':smiling_imp:',
    'brave'   => ':sunglasses:',
    'calm'    => ':innocent:',
    'careful' => ':face_with_head_bandage:',
    'docile'  => ':hugging_face:',
    'gentle'  => ':smiley:',
    'hardy'   => ':sweat_smile:',
    'hasty'   => ':rage:',
    'jolly'   => ':smile:',
    'lax'     => ':sleeping:',
    'lonely'  => ':disappointed:',
    'impish'  => ':smirk:',
    'mild'    => ':slightly_smiling_face:',
    'modest'  => ':nerd_face:',
    'naive'   => ':yum:',
    'naughty' => ':stuck_out_tongue_winking_eye:',
    'quiet'   => ':zipper_mouth_face:',
    'quirky'  => ':upside_down_face:',
    'rash'    => ':laughing:',
    'relaxed' => ':relieved:',
    'sassy'   => ':face_with_rolling_eyes:',
    'serious' => ':neutral_face:',
    'timid'   => ':confused:',
  ];

  if ( isset( $emoji[ $nature ] ) ) {
    return $emoji[ $nature ];
  } else {
    return '';
  }

} // Function slackemon_get_nature_emoji

function slackemon_get_color_as_hex( $color_name ) {
  return rgb2hex( COLORS_BY_NAME[ $color_name ] );
}

function slackemon_paginate( $objects, $page_number, $items_per_page = 5 ) {

  $total_objects = count( $objects );
  $total_pages   = ceil( $total_objects / $items_per_page );

  // Default to last page if we have requested a page that no longer exists (eg. due to transferring)
  $page_number = $page_number <= $total_pages ? $page_number : $total_pages;

  $paginated = array_chunk( $objects, $items_per_page )[ $page_number - 1 ];

  return $paginated;

} // Function slackemon_paginate

function slackemon_get_pagination_attachment( $objects, $page_number, $action_name, $items_per_page = 5, $action_value_prefix = '' ) {

  $total_objects = count( $objects );
  $total_pages   = ceil( $total_objects / $items_per_page );

  if ( $total_pages > 1 ) {

    // Partial pagination mode, or all pages?
    if ( $total_pages > 5 ) {
      $pagination_actions = [
        [
          'name' => $action_name,
          'text' => ':rewind:',
          'type' => 'button',
          'value' => $action_value_prefix . '1',
          'style' => 1 == $page_number ? 'primary' : '',
        ], (
          $total_pages == $page_number ?
          [
            'name' => $action_name,
            'text' => $page_number - 2,
            'type' => 'button',
            'value' => $action_value_prefix . ( $page_number - 2 ),
          ] :
          ''
        ), (
          1 == $page_number ?
          '' : [
            'name' => $action_name,
            'text' => $page_number - 1,
            'type' => 'button',
            'value' => $action_value_prefix . ( $page_number - 1 ),
          ]
        ), [
          'name' => $action_name,
          'text' => $page_number,
          'type' => 'button',
          'value' => $action_value_prefix . $page_number,
          'style' => 'primary',
        ], (
          $total_pages == $page_number ?
          '' : [
            'name' => $action_name,
            'text' => $page_number + 1,
            'type' => 'button',
            'value' => $action_value_prefix . ( $page_number + 1 ),
          ]
        ), (
          1 == $page_number ?
          [
            'name' => $action_name,
            'text' => $page_number + 2,
            'type' => 'button',
            'value' => $action_value_prefix . ( $page_number + 2 ),
          ] : ''
        ), [
          'name' => $action_name,
          'text' => ':fast_forward:',
          'type' => 'button',
          'value' => $action_value_prefix . $total_pages,
          'style' => $total_pages == $page_number ? 'primary' : '',
        ],
      ];
    } else {
      $pagination_actions = [];
      for ( $i = 1; $i <= $total_pages; $i++ ) {
        $pagination_actions[] = [
          'name' => $action_name,
          'text' => $i,
          'type' => 'button',
          'value' => $action_value_prefix . $i,
          'style' => $i == $page_number ? 'primary' : '',
        ];
      }
    }

    $attachment = [
      'fallback' => 'Page',
      'color' => '#333333',
      'actions' => $pagination_actions,
      'footer' => (
        'Viewing ' . ( $items_per_page * ( $page_number - 1 ) + 1 ) . ' - ' .
        min( $total_objects, $items_per_page * $page_number ) .
        ' of ' . $total_objects
      ),
    ];

    return $attachment;

  } // If pagination

  return [];

}  // Function slackemon_get_pagination_attachment

// The end!
