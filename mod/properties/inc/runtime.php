<?php

/**
 * See docs/AUTHORS and docs/COPYRIGHT for relevant info.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 *
 * @version $Id$
 * @author Matthew McNaney <mcnaney at gmail dot com>
 * @package
 * @license http://opensource.org/licenses/gpl-3.0.html
 */
if (!PHPWS_Settings::get('properties', 'roommate_only')) {
    if (PHPWS_Core::atHome()) {
        PHPWS_Core::initModClass('properties', 'User.php');
        $user = new Properties\User;
        $user->searchPanel();
        $user->propertyListing();
    }

    if (isset($_SESSION['Contact_User'])) {
        $_SESSION['Contact_User']->loginMenu();
    } else {
        $form = new PHPWS_Form('contact-login');
        $form->addHidden('module', 'properties');
        $form->addHidden('cop', 'login');
        $form->addText('c_username');
        $form->setPlaceHolder('c_username', 'Username');
        $form->setSize('c_username', 10);
        $form->setClass('c_username', 'form-control');

        $form->addPassword('c_password');
        $form->setPlaceHolder('c_password', 'Password');
        $form->setSize('c_password', 10);
        $form->setClass('c_password', 'form-control');
        $form->addSubmit('submit', 'Log in to Manager Account');
        $form->setClass('submit', 'btn btn-success');
        $tpl = $form->getTemplate();
        $content = PHPWS_Template::process($tpl, 'properties', 'clogin.tpl');
        Layout::add($content, 'properties', 'contact_login');
    }
}

purgeProperties();

function purgeProperties()
{
    $last_purge = \PHPWS_Settings::get('properties', 'last_purge') + 86400;
    $current_time = time();
    if ($last_purge < $current_time) {
        \PHPWS_Settings::set('properties', 'last_purge', $current_time);
        \PHPWS_Settings::save('properties');
        $db = new PHPWS_DB('properties');
        $db->addWhere('timeout', time(), '<');
        $db->addValue('active', 0);
        $db->update();
        $db = new PHPWS_DB('prop_roommate');
        $db->addWhere('timeout', time(), '<');
        $db->delete();
    }
}

?>