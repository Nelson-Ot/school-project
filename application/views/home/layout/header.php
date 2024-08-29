
<!-- Header Starts -->
	<header class="main-header">
	<!-- Nested Container Starts -->
	<!-- Top Bar Starts -->
		<div class="top-bar d-none d-md-block">
			<div class="container px-md-0">
			<div class="row">
				<div class="col-md-6 col-sm-12"><?php echo $cms_setting['working_hours']; ?></div>
				<div class="col-md-6 col-sm-12">
					<ul class="list-unstyled list-inline">
						<li class="list-inline-item"><a href="mailto:<?php echo $cms_setting['email']; ?>">
							<i class="far fa-envelope"></i> <?php echo $cms_setting['email']; ?>
						</a></li>
						<li class="list-inline-item"><i class="fas fa-phone-volume"></i> <?php echo $cms_setting['mobile_no']; ?></li>
					<?php 
					$homeURL = base_url($cms_setting['url_alias']);
					if (!is_loggedin()) { 
				        $authenticationURL = base_url($cms_setting['url_alias'] . '/authentication');
				        $saasExisting = $this->app_lib->isExistingAddon('saas');
				        if ($saasExisting && $this->db->table_exists("custom_domain")) {
				            $getDomain = $this->home_model->getCurrentDomain();
				            if(!empty($getDomain)) {
				                $authenticationURL = base_url('authentication');
				            }
				        } ?>
						<li class="list-inline-item"><a href="<?php echo $authenticationURL; ?>"><i class="fas fa-user-lock"></i> Login</a></li>
					<?php } else { ?>
						<li class="list-inline-item"><a href="<?php echo base_url('dashboard'); ?>"><i class="fas fa-home"></i> Dashboard</a></li>
					<?php } ?>
					</ul>
				</div>
			</div>
			</div>
		</div>
	<!-- Top Bar Ends -->
	<!-- Navbar Starts -->
		<div class="stricky" id="strickyMenu" style="background-color: <?php echo $cms_setting['menu_color'] ?>;">
			<div class="container-md px-md-0">
			<nav id="nav" class="navbar navbar-expand-lg" role="navigation">
				<div class="container-md px-md-0">
				<!-- Logo Starts -->
					<a href="<?php echo $homeURL ?>" class="navbar-brand">
						<img src="<?php echo base_url('uploads/frontend/images/' . $cms_setting['logo']); ?>" alt="Logo">
					</a>
					<!-- Logo Ends -->
					<!-- Collapse Button Starts -->
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
						<span class="fa fa-bars"></span>
					</button>
					<!-- Collapse Button Ends -->
					<!-- Navbar Collapse Starts -->
					<div id="mainNav" class="navbar-collapse collapse">
						<ul class="nav navbar-nav ml-auto navbar-style3">
							<?php
							$school = $this->uri->segment(1);
							$result = $this->home_model->menuList($school);
							foreach ($result as $key => $row) {
								$active_menu 	= '';
								$submenu 		= '';
								$op_new_tab 	= '';
								$submenu_active = '';
								$currentURL 	= base_url(uri_string());
								
								if ($currentURL == $row['url']) {
									$active_menu = ' active';
								}
								if (!empty($row['submenu']) && is_array($row['submenu'])) {
									$submenu = ' dropdown';
									$arrayURL = array_column($row['submenu'],'url');
									if (in_array($currentURL, $arrayURL)) {
										$submenu_active = ' active';
									}
									$row['title'] = $row['title'] . '<span class="arrow"></span>';
								}
                                if ($row['open_new_tab']) {
                                    $op_new_tab = "target='_blank'";
                                }
								if ($cms_setting['online_admission'] == 0 && $row['alias'] == 'admission') continue;
							?>
								<li class="nav-item<?php echo $active_menu; echo $submenu; echo $submenu_active; ?>">
									<a href="<?php echo $row['url']; ?>" class="nav-link" <?php echo $op_new_tab; ?>><?php echo $row['title']; ?>  </a>
									<?php if (!empty($row['submenu'])) { ?>
									<ul class="dropdown-menu" role="menu">
										<?php foreach ($row['submenu'] as $row2) { 
											$active_menu 	= '';
											$op_new_tab 	= '';
											if ($currentURL == $row2['url']) {
												$active_menu = ' active';
											}
			                                if ($row2['open_new_tab']) {
			                                    $op_new_tab = "target='_blank'";
			                                }
											?>
										<a class="dropdown-item<?php echo $active_menu; ?>" <?php echo $op_new_tab; ?> href="<?php echo $row2['url']; ?>"><?php echo $row2['title']; ?></a>
										<?php } ?>
									</ul>
								<?php } ?>
								</li>
							<?php } ?>
							<li class="nav-item m-login">
							<?php if (!is_loggedin()) { ?>
								<a href="<?php echo $authenticationURL; ?>" class="btn d-grid btn-black mt-sm">Login</a>
							<?php } else { ?>
								<a href="<?php echo base_url('dashboard'); ?>" class="btn d-grid btn-black mt-sm">Dashboard</a>
							<?php } ?>
							</li>
						</ul>
					</div>
				<!-- Navbar Collapse Ends -->
				</div>
			</nav>
		</div>
	</div>
	<!-- Navbar Ends -->
</header>

<?php
$news_list = $this->home_model->getLatestNews($branchID);
if (!empty($news_list)) {
	$url_alias = $cms_setting['url_alias'];
	?>
<div class="latest--news">
    <div class="container">
        <div class="d-lg-flex align-items-center">
            <div class="latest-title d-flex align-items-center text-nowrap text-white text-uppercase">
                <i class="fa-solid fa-bolt"></i> Latest News
            </div>
			<div class="news-updates-list overflow-hidden mt-2 mb-2" data-marquee="true">
				<ul class="nav">
				<?php foreach ($news_list as $key => $value) { ?>
					<li><a class="text-white" href="<?php echo base_url($url_alias . '/news_view/' . $value->alias); ?>"><strong><?php echo str_pad($key + 1, 2, '0', STR_PAD_LEFT); ?>.</strong> <?php echo $value->title . " - <span class='date-text'>" .  _d($value->date); ?></span></a></li>
				<?php } ?>
				</ul>
			</div>
			<div class="current-date text-nowrap text-white">
				<span class="date-now"><i class="fa-regular fa-clock"></i> <?php echo _d(date('Y-m-d')) ?></span>
			</div>
        </div>
    </div>
</div>
<?php } ?>