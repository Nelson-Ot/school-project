<!-- Main Banner Starts -->
<div class="main-banner" style="background: url(<?php echo base_url('uploads/frontend/banners/' . $page_data['banner_image']); ?>) center top;">
    <div class="container px-md-0">
        <h2><span><?php echo $page_data['page_title']; ?></span></h2>
    </div>
</div>

<div class="breadcrumb">
    <div class="container px-md-0">
        <ul class="list-unstyled list-inline">
            <li class="list-inline-item"><a href="<?php echo base_url('home') ?>">Home</a></li>
            <li class="list-inline-item active"><?php echo $page_data['page_title']; ?></li>
        </ul>
    </div>
</div>

<div class="container px-md-0 main-container">
    <h3 class="main-heading2 mt-0"><?php echo $page_data['title']; ?></h3>
    <?php echo $page_data['description']; ?>
    <div class="row">
        <?php
        $url_alias = $cms_setting['url_alias'];
        if (!empty($results)) {
            foreach ($results as $key => $value) {
        ?>
        <div class="col-sm-12 col-md-4" style="padding-top:20px">
            <div class="news-post-box">
                <div class="news-post-box-img text-center text-lg-left">
                    <img src="<?=base_url('uploads/frontend/events/' . $value['image'] )?>" alt="Event Image" class="img-fluid">
                </div>
                <div class="inner">
                    <h5>
                        <a href="<?=base_url("$url_alias/event_view/". $value['id'])?>"><?php echo $value['title'] ?></a>
                    </h5>
                    <ul class="list-unstyled list-inline post-meta">
                        <li class="list-inline-item">
                            <i class="fas fa-calendar-day"></i> <?php echo _d($value['start_date']); ?>
                        </li>
                        <li class="list-inline-item">
                            <i class="fas fa-calendar-day"></i> <?php echo _d($value['end_date']); ?>
                        </li>
                    </ul>
                    <ul class="list-unstyled list-inline post-meta">
                        <li class="list-inline-item">
                            <i class="fas fa-calendar-day"></i> Posted on - <?php echo _d($value['created_at']); ?>
                        </li>
                    </ul>
                    <p>
                        <?php echo mb_strimwidth(strip_tags($value['remark']), 0, 80, "..."); ?>
                    </p>
                    <a href="<?=base_url("$url_alias/event_view/". $value['id'])?>" class="btn btn-1">
                        <i class="fa fa-arrow-circle-right"></i>
                        Read More
                    </a>
                </div>
            </div>

        </div>
 <?php } ?>
     <div class="pagination-bx mt-2">
        <?php
            if (isset($links)) {
                echo $links;
            }
        ?>
    </div>
<?php } else { ?>
    <div class="col-md-12">
        <div class="alert alert-info">No Upcoming Events Found.</div>
    </div>
 <?php } ?>
    </div>
</div>
<!-- Main Container Ends -->