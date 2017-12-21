<?php
/**
 * Bendera
 *
 * Copyright 2010 by Shaun McCormick <shaun+bendera@modx.com>
 *
 * Bendera is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Bendera is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Bendera; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package bendera
 */
/**
 * Default English Lexicon Entries for Bendera
 *
 * @package bendera
 * @subpackage lexicon
 */
$_lang['bendera']                     = 'Bendera';
$_lang['bendera.menu_desc']           = 'Manage all your advertisements.';
$_lang['bendera.items']               = 'Advertisements';
$_lang['bendera.item_create']         = 'New  Advertisement';
$_lang['bendera.item_err_ae']         = 'An advertisement already exists with this title.';
$_lang['bendera.item_err_nf']         = 'Advertisement not found.';
$_lang['bendera.item_err_ns']         = 'Advertisement not selected.';
$_lang['bendera.item_err_remove']     = 'Something went wrong while deleting this advertisement.';
$_lang['bendera.item_err_save']       = 'Something went wrong while saving this advertisement.';
$_lang['bendera.item_remove']         = 'Delete advertisement';
$_lang['bendera.item_remove_confirm'] = 'Are you sure you want to delete this advertisement?';
$_lang['bendera.item_update']         = 'Update advertisement';
$_lang['bendera.intro_msg']           = 'Manage your advertisements.';

$_lang['bendera.active']             = 'Active?';
$_lang['bendera.kindad']             = 'Type advertisement';
$_lang['bendera.title']              = 'Title';
$_lang['bendera.description']        = 'Description';
$_lang['bendera.image']              = 'Image';
$_lang['bendera.html']               = 'HTML content';
$_lang['bendera.resource']           = 'Place advertisement in categories:';
$_lang['bendera.startdate']          = 'Start date';
$_lang['bendera.enddate']            = 'End date';
$_lang['bendera.link_internal']      = 'Internal link';
$_lang['bendera.link_external']      = 'External link';
$_lang['bendera.link_external-help'] = 'When an external link is filled in it will overrule the internal link.';

/* System setting */
$_lang['setting_bendera.user_name']             = 'Your name';
$_lang['setting_bendera.user_name_desc']        = 'Is used for the Sterc Extra\'s newsletter subscription. (optional)';
$_lang['setting_bendera.user_email']            = 'Your emailaddress';
$_lang['setting_bendera.user_email_desc']       = 'Is used for the Sterc Extra\'s newsletter subscription. (optional)';
$_lang['setting_bendera.exclude_contexts']      = 'Contexts to exclude from Bendera';
$_lang['setting_bendera.exclude_contexts_desc'] = 'Provide a comma delimited list of context keys to exclude from Bendera.';
$_lang['setting_bendera.allowed_types']         = 'List of allowed bendera types';
$_lang['setting_bendera.allowed_types_desc']    = 'Provide a comma delimited list of allowed bendera types. Options are: html, button, image, affiliate.';
$_lang['setting_bendera.chunk_config']          = 'Chunk options config';
$_lang['setting_bendera.chunk_config_desc']     = 'Provide a JSON array containing ID and names of available chunks. Example: [{"id": 12, "name": "Filters"}]';
$_lang['setting_bendera.use_categories']        = 'Use categories?';
$_lang['setting_bendera.use_categories_desc']   = 'Enable/Disable the use of categories based on templates.';
$_lang['setting_bendera.use_dates']             = 'Use dates?';
$_lang['setting_bendera.use_dates_desc']        = 'Enable/Disable the use of start and end dates for displaying bendera items.';

/* Types. */
$_lang['bendera.type.html']       = 'HTML';
$_lang['bendera.type.banner']     = 'Banner';
$_lang['bendera.type.button']     = 'Button';
$_lang['bendera.type.chunk']      = 'Block';
$_lang['bendera.type.image']      = 'Image';
$_lang['bendera.type.affiliate']  = 'Affiliate';
