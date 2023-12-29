<?php

/**
 * Plugin Name:       AppTailor API
 * Description:       Expose REST API endpoints for mobile application.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           0.1.1
 * Author:            Novembit
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       at-api
 *
 * @package           app-tailor
 */

require_once 'vendor/autoload.php';
require_once 'src/V1/routes.php';
require_once 'src/V1/hooks.php';
