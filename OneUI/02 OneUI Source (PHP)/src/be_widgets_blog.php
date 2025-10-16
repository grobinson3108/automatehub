<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/backend/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Blog
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Perfect cards for your blog posts.
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="javascript:void(0)">Widgets</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Blog
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- Cover Link Stories -->
  <h2 class="content-heading">Cover Link Stories</h2>
  <div class="row items-push">
    <div class="col-md-6 col-xl-4">
      <!-- Story -->
      <a class="block block-rounded bg-image h-100 mb-0" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo9.jpg');" href="javascript:void(0)">
        <div class="block-content bg-black-50">
          <div class="mb-5 mb-sm-7 d-sm-flex justify-content-sm-between align-items-sm-center">
            <p>
              <span class="badge bg-primary fw-bold p-2 text-uppercase">
                Travel
              </span>
            </p>
            <p class="fs-sm">
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-eye text-white-50"></i> 400
              </span>
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-heart text-white-50"></i> 89
              </span>
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-comments text-white-50"></i> 44
              </span>
            </p>
          </div>
          <p class="fs-lg fw-bold text-white mb-0">
            Exploring the forest in the mist
          </p>
          <p class="fw-medium text-white-75">
            <?php echo $one->get_name(); ?> &middot; 12 min
          </p>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-4">
      <!-- Story -->
      <a class="block block-rounded bg-image h-100 mb-0" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo13.jpg');" href="javascript:void(0)">
        <div class="block-content bg-black-50">
          <div class="mb-5 mb-sm-7 d-sm-flex justify-content-sm-between align-items-sm-center">
            <p>
              <span class="badge bg-primary fw-bold p-2 text-uppercase">
                Inspiration
              </span>
            </p>
            <p class="fs-sm">
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-eye text-white-50"></i> 433
              </span>
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-heart text-white-50"></i> 78
              </span>
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-comments text-white-50"></i> 43
              </span>
            </p>
          </div>
          <p class="fs-lg fw-bold text-white mb-0">
            Building successful web apps
          </p>
          <p class="fw-medium text-white-75">
            <?php echo $one->get_name(); ?> &middot; 16 min
          </p>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-4">
      <!-- Story -->
      <a class="block block-rounded bg-image h-100 mb-0" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo22.jpg');" href="javascript:void(0)">
        <div class="block-content bg-black-50">
          <div class="mb-5 mb-sm-7 d-sm-flex justify-content-sm-between align-items-sm-center">
            <p>
              <span class="badge bg-primary fw-bold p-2 text-uppercase">
                Coding
              </span>
            </p>
            <p class="fs-sm">
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-eye text-white-50"></i> 789
              </span>
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-heart text-white-50"></i> 22
              </span>
              <span class="text-white fw-semibold me-1">
                <i class="fa fa-fw fa-comments text-white-50"></i> 14
              </span>
            </p>
          </div>
          <p class="fs-lg fw-bold text-white mb-0">
            How to build your next project
          </p>
          <p class="fw-medium text-white-75">
            <?php echo $one->get_name(); ?> &middot; 32 min
          </p>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-lg-4">
      <!-- Story -->
      <a class="block block-rounded block-link-pop h-100 mb-0 overflow-hidden" href="javascript:void(0)">
        <?php $one->get_photo(25, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="fw-bold mb-1">Get things done</h4>
          <p class="fs-sm fw-medium mb-2">
            <span class="text-primary"><?php $one->get_name(); ?></span> on March 30, 2024 · <span class="text-muted">9 min</span>
          </p>
          <p class="fs-sm">
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-lg-4">
      <!-- Story -->
      <a class="block block-rounded block-link-pop h-100 mb-0 overflow-hidden" href="javascript:void(0)">
        <?php $one->get_photo(12, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="fw-bold mb-1">10 Inspiring adventures</h4>
          <p class="fs-sm fw-medium mb-2">
            <span class="text-primary"><?php $one->get_name(); ?></span> on March 24, 2024 · <span class="text-muted">8 min</span>
          </p>
          <p class="fs-sm">
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-lg-4">
      <!-- Story -->
      <a class="block block-rounded block-link-pop h-100 mb-0 overflow-hidden" href="javascript:void(0)">
        <?php $one->get_photo(23, true, 'img-fluid'); ?>
        <div class="block-content">
          <h4 class="fw-bold mb-1">Work &amp; Travel</h4>
          <p class="fs-sm fw-medium mb-2">
            <span class="text-primary"><?php $one->get_name(); ?></span> on March 21, 2024 · <span class="text-muted">14 min</span>
          </p>
          <p class="fs-sm">
            Anam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus justo amet....
          </p>
        </div>
      </a>
      <!-- END Story -->
    </div>
  </div>
  <!-- END Cover Link Stories -->

  <!-- Stories with multiple links -->
  <h2 class="content-heading">Stories with multiple links</h2>
  <div class="row">
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <div class="block block-rounded">
        <div class="block-content pb-8 text-end bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo10.jpg');">
          <span class="badge bg-primary fw-bold p-2 text-uppercase">
            Travel
          </span>
        </div>
        <div class="block-content">
          <a class="fw-semibold text-dark" href="javascript:void(0)">
            Southeast Asia and beyond
          </a>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 12 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 643
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 45
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 12
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <div class="block block-rounded">
        <div class="block-content pb-8 text-end bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo29.jpg');">
          <span class="badge bg-warning fw-bold p-2 text-uppercase">
            Tutorials
          </span>
        </div>
        <div class="block-content">
          <a class="fw-semibold text-dark" href="javascript:void(0)">
            Learn to Code
          </a>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 9 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 456
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 67
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 56
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <div class="block block-rounded">
        <div class="block-content pb-8 text-end bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo4.jpg');">
          <span class="badge bg-danger fw-bold p-2 text-uppercase">
            Inspiration
          </span>
        </div>
        <div class="block-content">
          <a class="fw-semibold text-dark" href="javascript:void(0)">
            How to move forward
          </a>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 16 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 789
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 78
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 23
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <div class="block block-rounded">
        <div class="block-content pb-8 text-end bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo31.jpg');">
          <span class="badge bg-success fw-bold p-2 text-uppercase">
            Coding
          </span>
        </div>
        <div class="block-content">
          <a class="fw-semibold text-dark" href="javascript:void(0)">
            Build your next project
          </a>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 15 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 643
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 45
              </a>
            </div>
            <div class="col-4">
              <a class="text-muted fw-semibold" href="javascript:void(0)">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 14
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- END Story -->
    </div>
  </div>
  <!-- END Stories with multiple links -->

  <!-- Link Stories -->
  <h2 class="content-heading">Link Stories</h2>
  <div class="row">
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <a class="block block-rounded" href="javascript:void(0)">
        <div class="block-content pb-8 bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo9.jpg');">
          <span class="badge bg-primary fw-bold p-2 text-uppercase">
            Travel
          </span>
        </div>
        <div class="block-content text-center">
          <h4 class="fs-base fw-semibold text-dark mb-0">
            Exploring the forest in the mist
          </h4>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 12 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 344
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 56
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 35
              </span>
            </div>
          </div>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
        <div class="block-content pb-8 bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo22.jpg');">
          <span class="badge bg-warning fw-bold p-2 text-uppercase">
            Tutorials
          </span>
        </div>
        <div class="block-content text-center">
          <h4 class="fs-base fw-semibold text-dark mb-0">
            Learn to Code
          </h4>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 9 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 600
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 87
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 17
              </span>
            </div>
          </div>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <a class="block block-rounded block-link-pop" href="javascript:void(0)">
        <div class="block-content pb-8 bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo5.jpg');">
          <span class="badge bg-danger fw-bold p-2 text-uppercase">
            Inspiration
          </span>
        </div>
        <div class="block-content text-center">
          <h4 class="fs-base fw-semibold text-dark mb-0">
            How to move forward
          </h4>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 16 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 890
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 56
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 14
              </span>
            </div>
          </div>
        </div>
      </a>
      <!-- END Story -->
    </div>
    <div class="col-md-6 col-xl-3">
      <!-- Story -->
      <a class="block block-rounded block-link-rotate" href="javascript:void(0)">
        <div class="block-content pb-8 bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo24.jpg');">
          <span class="badge bg-success fw-bold p-2 text-uppercase">
            Coding
          </span>
        </div>
        <div class="block-content text-center">
          <h4 class="fs-base fw-semibold text-dark mb-0">
            Build your next project
          </h4>
          <p class="fs-sm fw-medium text-muted">
            <?php echo $one->get_name(); ?> &middot; 15 min
          </p>
        </div>
        <div class="block-content block-content-full bg-body-light">
          <div class="row g-0 fs-sm text-center">
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> 467
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-heart opacity-50 me-1"></i> 34
              </span>
            </div>
            <div class="col-4">
              <span class="text-muted fw-semibold">
                <i class="fa fa-fw fa-comments opacity-50 me-1"></i> 12
              </span>
            </div>
          </div>
        </div>
      </a>
      <!-- END Story -->
    </div>
  </div>
  <!-- END Link Stories -->

  <!-- Authors -->
  <h2 class="content-heading">Authors</h2>
  <div class="row items-push">
    <div class="col-md-6 col-xl-4">
      <!-- Author #1 -->
      <div class="block block-rounded h-100 mb-0">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between bg-primary-dark">
          <div class="me-3">
            <p class="text-white fw-bold mb-0"><?php $one->get_name('male'); ?></p>
            <p class="fs-sm fw-medium text-white-75 mb-0">
              54k Followers
            </p>
          </div>
          <?php $one->get_avatar(0, 'male', 48, true); ?>
        </div>
        <div class="block-content">
          <div class="list-group push">
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Exploring alternative marketing</h5>
              <small>March 20, 2024</small>
            </a>
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Learn Laravel in 5 simple steps</h5>
              <small>March 15, 2024</small>
            </a>
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Inspiring Results</h5>
              <small>February 23, 2024</small>
            </a>
          </div>
        </div>
        <div class="block-content border-top">
          <div class="d-flex justify-content-between push">
            <a class="btn btn-sm btn-alt-secondary" href="javascript:void(0)">
              <i class="fa fa-fw fa-pencil-alt opacity-50 me-1"></i> 78 Stories
            </a>
            <a class="btn btn-sm btn-alt-secondary" href="javascript:void(0)">
              More <i class="fa fa-fw fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- END Author #1 -->
    </div>
    <div class="col-md-6 col-xl-4">
      <!-- Author #2 -->
      <div class="block block-rounded h-100 mb-0">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between bg-primary">
          <div class="me-3">
            <p class="fw-bold text-white mb-0"><?php $one->get_name('male'); ?></p>
            <p class="fs-sm fw-medium text-white-75 mb-0">
              54k Followers
            </p>
          </div>
          <?php $one->get_avatar(0, 'male', 48, true); ?>
        </div>
        <div class="block-content">
          <div class="list-group push">
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Exploring alternative marketing</h5>
              <small>March 20, 2024</small>
            </a>
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Learn Laravel in 5 simple steps</h5>
              <small>March 15, 2024</small>
            </a>
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Inspiring Results</h5>
              <small>February 23, 2024</small>
            </a>
          </div>
        </div>
        <div class="block-content border-top">
          <div class="d-flex justify-content-between push">
            <a class="btn btn-sm btn-alt-secondary" href="javascript:void(0)">
              <i class="fa fa-fw fa-pencil-alt opacity-50 me-1"></i> 78 Stories
            </a>
            <a class="btn btn-sm btn-alt-secondary" href="javascript:void(0)">
              More <i class="fa fa-fw fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- END Author #2 -->
    </div>
    <div class="col-xl-4">
      <!-- Author #3 -->
      <div class="block block-rounded h-100 mb-0 overflow-hidden">
        <div class="block-content bg-image p-0" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo16.jpg');">
          <div class="block-content block-content-full d-flex align-items-center justify-content-between bg-primary-dark-op">
            <div class="me-3">
              <p class="fw-bold text-white mb-0"><?php $one->get_name('male'); ?></p>
              <p class="fs-sm fw-medium text-white-75 mb-0">
                54k Followers
              </p>
            </div>
            <?php $one->get_avatar(0, 'male', 48, true); ?>
          </div>
        </div>
        <div class="block-content">
          <div class="list-group push">
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Exploring alternative marketing</h5>
              <small>March 20, 2024</small>
            </a>
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Learn Laravel in 5 simple steps</h5>
              <small>March 15, 2024</small>
            </a>
            <a class="list-group-item list-group-item-action" href="javascript:void(0)">
              <h5 class="fs-base mb-1">Inspiring Results</h5>
              <small>February 23, 2024</small>
            </a>
          </div>
        </div>
        <div class="block-content border-top">
          <div class="d-flex justify-content-between push">
            <a class="btn btn-sm btn-alt-secondary" href="javascript:void(0)">
              <i class="fa fa-fw fa-pencil-alt opacity-50 me-1"></i> 78 Stories
            </a>
            <a class="btn btn-sm btn-alt-secondary" href="javascript:void(0)">
              More <i class="fa fa-fw fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- END Author #3 -->
    </div>
  </div>
  <!-- Authors -->
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>
<?php require 'inc/_global/views/footer_end.php'; ?>
