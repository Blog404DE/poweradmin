<?php

/*  Poweradmin, a friendly web-based admin tool for PowerDNS.
 *  See <https://www.poweradmin.org> for more details.
 *
 *  Copyright 2007-2009  Rejo Zenger <rejo@zenger.nl>
 *  Copyright 2010-2022  Poweradmin Development Team
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

/** Print paging menu
 *
 * @param int $amount Total number of items
 * @param int $rowamount Per page number of items
 * @param int $id Page specific ID (Zone ID, Template ID, etc)
 *
 * @return null
 */
function show_pages($amount, $rowamount, $id = '') {
    if ($amount > $rowamount) {
        $num = 8;
        $poutput = '';
        $lastpage = ceil($amount / $rowamount);
        $startpage = 1;

        if (!isset($_GET["start"]))
            $_GET["start"] = 1;
        $start = $_GET["start"];

        if ($lastpage > $num & $start > ($num / 2)) {
            $startpage = ($start - ($num / 2));
        }

        $poutput .= "<nav><ul class=\"pagination\">";

        if ($lastpage > $num & $start > 1) {
            $poutput .= '<li class="page-item"><a class="page-link" href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=' . ($start - 1);
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= _('Previous');
            $poutput .= '</a></li>';
        }

        if ($start != 1) {
            $poutput .= '<li class="page-item"><a class="page-link" href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=1';
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= '1';
            $poutput .= '</a></li>';
            if ($startpage > 2)
                $poutput .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">..</a></li>';
        }

        for ($i = $startpage; $i <= min(($startpage + $num), $lastpage); $i++) {
            if ($start == $i) {
                $poutput .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } elseif ($i != $lastpage & $i != 1) {
                $poutput .= '<li class="page-item"><a class="page-link" href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
                $poutput .= '?start=' . $i;
                if ($id != '')
                    $poutput .= '&id=' . $id;
                $poutput .= '">';
                $poutput .= $i;
                $poutput .= '</a></li>';
            }
        }

        if ($start != $lastpage) {
            if (min(($startpage + $num), $lastpage) < ($lastpage - 1))
                $poutput .= '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">..</a></li>';
            $poutput .= '<li class="page-item"><a class="page-link" href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=' . $lastpage;
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= $lastpage;
            $poutput .= '</a></li>';
        }

        if ($lastpage > $num & $start < $lastpage) {
            $poutput .= '<li class="page-item"><a class="page-link" href=" ' . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES);
            $poutput .= '?start=' . ($start + 1);
            if ($id != '')
                $poutput .= '&id=' . $id;
            $poutput .= '">';
            $poutput .= _('Next');
            $poutput .= '</a></li>';
        }

        $poutput .= "</ul></nav>";

        echo $poutput;
    }
}

/** Print alphanumeric paging menu
 *
 * Display the alphabetic option: [0-9] [a] [b] .. [z]
 *
 * @param string $letterstart Starting letter/number or 'all'
 * @param int $userid Current user ID
 *
 * @return null
 */
function show_letters($letterstart, $userid) {
    global $db;

    $char_range = array_merge(range('a', 'z'), array('_'));

    $query = "SELECT
			DISTINCT ".dbfunc_substr()."(domains.name, 1, 1) AS letter
			FROM domains
			LEFT JOIN zones ON domains.id = zones.domain_id
			WHERE zones.owner = " . $userid;
    $db->setLimit(36);

    $available_chars = array();
    $digits_available = 0;

    $response = $db->query($query);

    while ($row = $response->fetch()) {
        if (preg_match("/[0-9]/", $row['letter'])) {
            $digits_available = 1;
        } elseif (in_array($row['letter'], $char_range)) {
            array_push($available_chars, $row['letter']);
        }
    }

    echo _('Show zones beginning with') . ":<br>";

    if ($letterstart == "1") {
        echo "<span class=\"text-mutted\">[ 0-9 ]</span> ";
    } elseif ($digits_available) {
        echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=1\">[ 0-9 ]</a> ";
    } else {
        echo "[ <span class=\"text-muted\">0-9</span> ] ";
    }

    foreach ($char_range as $letter) {
        if ($letter == $letterstart) {
            echo "<span class=\"text-muted\">[ " . $letter . " ]</span> ";
        } elseif (in_array($letter, $available_chars)) {
            echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=" . $letter . "\">[ " . $letter . " ]</a> ";
        } else {
            echo "[ <span class=\"text-muted\">" . $letter . "</span> ] ";
        }
    }

    if ($letterstart == 'all') {
        echo "<span class=\"text-muted\">[ Show all ]</span>";
    } else {
        echo "<a href=\"" . htmlentities($_SERVER["PHP_SELF"], ENT_QUOTES) . "?letter=all\">[ Show all ]</a> ";
    }
}

/** Check if current user allowed to view any zone content
 *
 * @param int $userid Current user ID
 *
 * @return int 1 if user has permission to view other users zones content, 0 otherwise
 */
function zone_content_view_others($userid) {
    global $db;

    $query = "SELECT
		DISTINCT u.id
		FROM 	users u,
		        perm_templ pt,
		        perm_templ_items pti,
		        (SELECT id FROM perm_items WHERE name
			    IN ('zone_content_view_others', 'user_is_ueberuser')) pit
                WHERE u.id = " . $userid . "
                AND u.perm_templ = pt.id
                AND pti.templ_id = pt.id
                AND pti.perm_id  = pit.id";

    $result = $db->queryOne($query);

    return ($result ? 1 : 0);
}
