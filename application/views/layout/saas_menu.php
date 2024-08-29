<?php if (is_superadmin_loggedin()) { ?>
<li class="nav-parent <?php if ($main_menu == 'saas' || $main_menu == 'saas_setting' || $main_menu == 'custom_domain') echo 'nav-expanded nav-active';?>">
    <a>
        <i class="fas fa-sitemap"></i><span><?=translate('school_subscription')?></span>
    </a>
    <ul class="nav nav-children">
        <li class="<?php if ($sub_page == 'saas/school') echo 'nav-active';?>">
            <a href="<?=base_url('saas/school')?>">
                <span><i class="fas fa-caret-right" aria-hidden="true"></i><?=translate('subscription')?></span>
            </a>
        </li>
        <li class="<?php if ($sub_page == 'saas/pending_request' || $sub_page == 'saas/school_approved') echo 'nav-active';?>">
            <a href="<?=base_url('saas/pending_request')?>">
                <span><i class="fas fa-caret-right" aria-hidden="true"></i><?=translate('pending_request')?></span>
            </a>
        </li>
        <li class="<?php if ($sub_page == 'custom_domain/list' || $sub_page == 'custom_domain/dns_instruction') echo 'nav-active';?>">
            <a href="<?=base_url('custom_domain/list')?>">
                <span><i class="fas fa-caret-right" aria-hidden="true"></i><?=translate('custom_domain')?></span>
            </a>
        </li>
        <li class="<?php if ($sub_page == 'saas/package' || $sub_page == 'saas/package_edit') echo 'nav-active';?>">
            <a href="<?=base_url('saas/package')?>">
                <span><i class="fas fa-caret-right" aria-hidden="true"></i><?=translate('plan')?></span>
            </a>
        </li>
        <li class="<?php if ($main_menu == 'saas_setting') echo 'nav-active';?>">
            <a href="<?=base_url('saas/settings_general')?>">
                <span><i class="fas fa-caret-right" aria-hidden="true"></i><?=translate('settings')?></span>
            </a>
        </li>
        <li class="<?php if ($main_menu == 'saas') echo 'nav-active';?>">
            <a href="<?=base_url('saas/transactions')?>">
                <span><i class="fas fa-caret-right" aria-hidden="true"></i><?=translate('transactions')?></span>
            </a>
        </li>
    </ul>
</li>
<?php }
if (is_admin_loggedin()):
    ?>
<!-- School Subscription (SaaS)  -->
<li class="<?php if ($main_menu == 'subscription') echo 'nav-active';?>">
    <a href="<?=base_url('subscription/index')?>">
        <i class="icons icon-directions"></i><span><?=translate('subscription')?></span>
    </a>
</li>
<?php endif; ?>
<?php if (moduleIsEnabled('custom_domain') && !is_superadmin_loggedin()) { 
    if (get_permission('domain_request', 'is_view')) {?>
<!-- Custom Domain -->
<li class="<?php if ($main_menu == 'domain_request') echo 'nav-active';?>">
    <a href="<?=base_url('custom_domain/mylist')?>">
        <i class="fab fa-wikipedia-w"></i><span><?=translate('custom_domain')?></span>
    </a>
</li>
<?php } } ?>