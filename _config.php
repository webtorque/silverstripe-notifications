<?php
var_dump(Director::isTest());
die();
if (Director::isTest()) {
    Member::add_extension('MemberNotificationExtension');
}
