<?php

// Chromatix TM 04/04/2017
// Onboarding menu for Slackemon Go

function slackemon_get_onboarding_menu() {

  $message = [
    'attachments' => [
      [
        'text' => (
          ':pika2: *Yay! Welcome, new trainer!*' . "\n\n" .
          'Pokémon can appear at _any_ time of day or night - and you\'ll need to be quick to catch them! ' .
          'Don\'t worry though - you won\'t be bothered by Pokémon during your Slack \'do not disturb\' ' .
          'hours, or while you have a :toggl: timer running.'
        ),
      ], [
        'title' => 'Ooh, what\'s that rustling in the bushes?!',
        'thumb_url' => get_cached_image_url( INBOUND_URL . '/_images/slackemon-tree2.gif' ),
        'actions' => [
          [
            'name' => 'onboarding',
            'text' => 'Find my first \'mon!',
            'type' => 'button',
            'value' => 'catch',
            'style' => 'primary',
          ],
        ],
      ],
    ],
  ];

  return $message;

} // Function slackemon_get_onboarding_menu

// The end!
