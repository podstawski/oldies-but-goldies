<?php
return array(
	'lang:en' => 'english',
	'lang:pl' => 'polski',

	'application_title' => 'School Agent',
	'welcome_text' => 'Automatically generate and assign class levels in Google Apps',
	'welcome_text_1' => 'Easily <b>transfer passing classes</b> to a higher level',
	'welcome_text_2' => 'Easily <b>create gmail accounts</b> for teachers and students',
	'welcome_text_3' => 'Easily <b>delete accounts</b> of graduated students',
	'welcome_text_4' => 'Easily <b>manage school</b> groups and accounts',
	'welcome_text_dashboard' => 'School Agent allows you to easily manage accounts of students, teachers and school groups.',

	'copyright_text_prefix' => 'Copyright &copy; 2012 ',
	'copyright_text_link' => 'Gammanet',
	'copyright_text_suffix' => '',

	'auth_login_text' => 'To access the service, you must log in.',
	'auth_login_button' => 'Log in using Google',
	'auth_not_logged_in_text' => 'Not logged in',
	'auth_logout_button' => 'Log out',

	'spreadsheet_folder_name' => 'School Agent',
	'spreadsheet_header_first_name' => 'First name',
	'spreadsheet_header_last_name' => 'Last name',
	'spreadsheet_header_password' => 'Password',
	'spreadsheet_header_email' => 'E-mail',
	'spreadsheet_header_ownership' => 'Ownership',
	'spreadsheet_header_membership' => 'Membership',

	'export_debug_start' => 'Starting export&hellip;',
	'export_debug_getting_spreadsheet_id' => 'Getting spreadsheet ID of "%s"',
	'export_debug_collecting_removable_worksheets' => 'Collecting worksheets to remove',
	'export_debug_collecting_groups' => 'Collecting groups',
	'export_debug_collecting_users' => 'Collecting users',
	'export_debug_retrieving_groups' => 'Retrieving detailed information about groups',
	'export_debug_retrieving_users' => 'Retrieving detailed information about users',
	'export_debug_mongling_users' => 'Working with user information',
	'export_debug_creating_worksheet' => 'Creating worksheet',
	'export_debug_setting_header' => 'Setting up header',
	'export_debug_inserting_row' => 'Inserting row for user "%s"',
	'export_debug_deleting_old_spreadsheet' => 'Deleting old spreadsheet',
	'export_debug_uploading_file' => 'Uploading XLS document',
	'export_no_spreadsheet_title_specified_error' => 'No spreadsheet name specified.',
	'export_empty_domain_error' => 'The domain has no users nor groups, nothing to export.',
	'export_document_creation_error' => 'Failed to create document "%s". Try signing in to Google Drive.',
	'export_worksheet_creation_error' => 'Failed to create worksheet "%s"',
	'export_folder_creation_error' => 'Failed to create folder "%s"',
	'export_folder_move_error' => 'Failed to move document to folder "%s"',
	'export_worksheet_header_error' => 'Failed to set up header for worksheet "%s"',
	'export_worksheet_insert_row_error' => 'Failed to insert row for user "%s"',
	'export_worksheet_removal_error' => 'Failed to remove redundant worksheets',
	'export_group_all' => 'all* (read only)',
	'export_finished_errors_prefix' => 'Document placed with errors. Click ',
	'export_finished_errors_link' => 'here',
	'export_finished_errors_suffix' => ' to view document',
	'export_finished_success_prefix' => 'Document placed successfully. Click ',
	'export_finished_success_link' => 'here',
	'export_finished_success_suffix' => ' to view document',
	'export_progress_text' => 'Saving data to spreadsheet&hellip;',
	'export_button' => 'apps to spreadsheet',
	'export_button_cancel' => 'Cancel',
	'export_popup_button' => 'Export',
	'export_spreadsheet_title_label' => 'New spreadsheet name:',
	'export_popup_text' => '<p><strong>School Agent</strong> exports each group into one sheet with all groupmembers (users) inside. First name, Last name, E-mail, Password and Ownership are exported for each user account. Users without groups are in group &bdquo;all&rdquo;. Column Password is always empty, you can use it for new user password during importing spreadsheet. Column Ownership includes groups names of which particular user is owner of.</p><p>Export takes several minutes, please be patient.</p>',
	'export_popup_title' => 'Place all groups and users into spreadsheet&hellip;',
	'export_about_text' => 'Export accounts of students, teachers, groups and classes to a sheet. The sheet with data will be saved in your documents.',

	'import_no_spreadsheet_specified_error' => 'No spreadsheet specified.',
	'import_wrong_spreadsheet_error' => 'No spreadsheet with ID %s',
	'import_worksheet_invalid_chars_error' => 'Worksheet name &bdquo;%s&rdquo; contains invalid characters.',
	'import_group_name_invalid_chars_error' => 'Group &bdquo;%s&rdquo; has invalid characters',
	'import_user_email_invalid_chars_error' => 'User %s %s has invalid characters in e-mail &bdquo;%s&rdquo;',
	'import_cannot_guess_email_error' => 'No first or last name specified for user at row %d in worksheet %s',
	'import_no_group_name_specified_error' => 'No group name specified (?)',
	'import_user_ownership_conflict_error' => 'User %s %s owns non-existing group &bdquo;%s&rdquo;',
	'import_user_password_too_short_error' => 'User %s %s has too short password.',
	'import_no_data_specified_error' => 'No data specified (?)',
	'import_no_direction_specified_error' => 'No action direction specified (?)',
	'import_wrong_direction_error' => 'Wrong migration direction specified (?)',
	'import_missing_column_error' => 'Worksheet "%s" has no column "%s"',
	'import_no_action_error' => 'Nothing to do.',
	'import_no_spreadsheets_found_error' => 'Empty spreadsheet list. Cannot import.',
	'import_many_users_removal_cancel_button' => 'Cancel',
	'import_many_users_removal_continue_button' => 'Continue',
	'import_many_users_removal_notice' => 'According to preliminary analysis, more than 50% users are marked for removal.<br>In most cases this means you might upload wrong document.',
	'import_finished_success_prefix' => 'Migration finished successfully. Click ',
	'import_finished_success_link' => 'here',
	'import_finished_success_suffix' => ' to show the report',
	'import_finished_errors_prefix' => 'Migration finished with errors. Click ',
	'import_finished_errors_link' => 'here',
	'import_finished_errors_suffix' => ' to show the report',
	'import_no_migration_specified_error' => 'No action specified (?)',
	'import_wrong_migration_error' => 'No action with ID %s',
	'import_label_members' => 'Members',
	'import_label_owners' => 'Owners',
	'import_label_member_of' => 'Member of',
	'import_label_owner_of' => 'Owned groups',
	'import_label_password' => 'Password',
	'import_confirm_text_1' => 'I am aware deleting a user will also cause deletion of e-mails, documents and calendar events',
	'import_confirm_text_2' => 'I am aware deleting a group will cause additional deletion of subscriptions',
	'import_confirm_alert' => 'You must agree in order to continue',
	'import_button_cancel' => 'Cancel',
	'import_button_continue' => 'Continue',
	'import_phase_1_progress_text' => 'Analyzing data&hellip;',
	'import_undo_progress_text' => 'Undoing changes&hellip;',
	'import_button' => 'spreadsheet to apps',
	'import_popup_button' => 'Analyse spreadsheet',
	'import_undo_text' => 'Undo last synchronization (%s):',
	'import_undo_info' => 'Schoolagent supports you with a special feature of undoing changes previously committed. Remember that we can only recreate deleted user account without his e-mails, calendars and docs.',
	'import_undo_button' => 'Undo',
	'import_popup_title' => 'Import spreadsheet&hellip;',
	'import_about_text' => 'Import accounts of students, teachers, groups and classes from a spreadsheet. You can add new accounts and groups into the spreadsheet. You can also undo your changes.',

	'import_accordion_users-to-create' => 'Users to create',
	'import_accordion_users-to-remove' => 'Users to remove',
	'import_accordion_users-to-recreate' => 'Users to recreate',
	'import_accordion_users-to-update' => 'Users to update',
	'import_accordion_users-to-do-nothing' => 'Users no action required',
	'import_accordion_groups-to-create' => 'Groups to create',
	'import_accordion_groups-to-remove' => 'Groups to remove',
	'import_accordion_groups-to-recreate' => 'Groups to recreate',
	'import_accordion_groups-to-update' => 'Groups to update',
	'import_accordion_groups-to-do-nothing' => 'Groups no action required',

	'report_nothing_done' => 'No action steps were taken',
	'report_status_success' => 'Success',
	'report_status_failure' => 'Failure',
	'report_header_type' => 'Type',
	'report_header_result' => 'Result',
	'report_header_details' => 'Details',
	'report_button_back' => '&crarr; Back to dashboard',
	'report_button_details' => 'Show / Hide',

	'google_error_text' => 'Google API seems to be down.',
	'google_error_button_reclaim' => 'Reclaim token',

	'misc_table_ordinal' => '#',
	'misc_progress_text' => 'School Agent progress&hellip;',
	'misc_wrong_process' => 'Wrong process (?)',
	'misc_date_format' => 'Y-m-d',
	'misc_date_format_minute' => 'Y-m-d H:i',
	'misc_date_format_seconds' => 'Y-m-d H:i:s',
	'paginator_previous' => 'Previous page',
	'paginator_first' => 'First page',
	'paginator_page_x' => 'Page %s',
	'paginator_next' => 'Next page',
	'paginator_last' => 'Last page',

	'protection_no_email_specified_error' => 'No e-mail address specified',
	'protection_no_flag_specified_error' => 'No protection flag specified',

	'spreadsheet-update' => 'Spreadsheet update',
	'group-remove' => 'Group removal',
	'group-create' => 'Group creation',
	'group-update' => 'Group update',
	'user-remove' => 'User removal',
	'user-create' => 'User creation',
	'user-update' => 'User update',
	'group-member-remove' => 'Membership removal',
	'group-owner-remove' => 'Ownership removal',
	'group-member-add' => 'Membership add',
	'group-owner-add' => 'Ownership add',

	'provisioning_api_error' => 'To use this service, please ensure that:
	<ol>
        <li>You are a domain administrator</li>
        <li>
            Provisioning API is enabled. To do so:
            <ul>
                <li>Login to Gmail with your account</li>
                <li>Click icon with gearwheel in upper right corner</li>
                <li>Click &bdquo;Manage this domain&rdquo;</li>
                <li>Click &bdquo;Domain settings&rdquo; on blue toolbar</li>
                <li>Click &bdquo;User settings&rdquo;</li>
                <li>Check &bdquo;Enable Provisioning API&rdquo;</li>
            </ul>
        </li>
	</ol>',
);
