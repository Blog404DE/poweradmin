<?php

/*  Poweradmin, a friendly web-based admin tool for PowerDNS.
 *  See <https://www.poweradmin.org> for more details.
 *
 *  Copyright 2007-2010 Rejo Zenger <rejo@zenger.nl>
 *  Copyright 2010-2023 Poweradmin Development Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Web interface footer
 *
 * @package     Poweradmin
 * @copyright   2007-2010 Rejo Zenger <rejo@zenger.nl>
 * @copyright   2010-2023 Poweradmin Development Team
 * @license     https://opensource.org/licenses/GPL-3.0 GPL
 */

use Poweradmin\AppFactory;
use Poweradmin\Infrastructure\Web\ThemeManager;
use Poweradmin\Version;

global $db;
global $db_debug;

if (is_object($db)) {
    $db->disconnect();
}

$app = AppFactory::create();

$iface_style = $app->config('iface_style');
$themeManager = new ThemeManager($iface_style);
$selected_theme = $themeManager->getSelectedTheme();

$display_stats = $app->config('display_stats');

$app->render('footer.html', [
    'version' => isset($_SESSION["userid"]) ? Version::VERSION : false,
    'custom_footer' => file_exists('templates/custom/footer.html'),
    'display_stats' => $display_stats ? $app->displayStats() : false,
    'db_queries' => $db_debug ? $db->getQueries() : false,
    'show_theme_switcher' => in_array($selected_theme, ['ignite', 'spark']),
    'iface_style' => $selected_theme,
]);

