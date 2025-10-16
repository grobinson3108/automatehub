<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Page Content -->
<div class="bg-image" style="background-image: url('<?php echo $one->assets_folder; ?>/media/photos/photo28@2x.jpg');">
  <div class="row g-0 bg-primary-dark-op">
    <!-- Meta Info Section -->
    <div class="hero-static col-lg-4 d-none d-lg-flex flex-column justify-content-center">
      <div class="p-4 p-xl-5 flex-grow-1 d-flex align-items-center">
        <div class="w-100">
          <a class="link-fx fw-semibold fs-2 text-white" href="index.php">
            OneUI
          </a>
          <p class="text-white-75 me-xl-8 mt-2">
            Welcome to your amazing app. Feel free to login and start managing your projects and clients.
          </p>
        </div>
      </div>
      <div class="p-4 p-xl-5 d-xl-flex justify-content-between align-items-center fs-sm">
        <p class="fw-medium text-white-50 mb-0">
          <strong><?php echo $one->name . ' ' . $one->version; ?></strong> &copy; <span data-toggle="year-copy"></span>
        </p>
        <ul class="list list-inline mb-0 py-2">
          <li class="list-inline-item">
            <a class="text-white-75 fw-medium" href="javascript:void(0)">Legal</a>
          </li>
          <li class="list-inline-item">
            <a class="text-white-75 fw-medium" href="javascript:void(0)">Contact</a>
          </li>
          <li class="list-inline-item">
            <a class="text-white-75 fw-medium" href="javascript:void(0)">Terms</a>
          </li>
        </ul>
      </div>
    </div>
    <!-- END Meta Info Section -->

    <!-- Main Section -->
    <div class="hero-static col-lg-8 d-flex flex-column align-items-center bg-body-extra-light text-center">
      <div class="p-3 w-100 d-lg-none">
        <a class="link-fx fw-semibold fs-3 text-dark" href="index.php">
          OneUI
        </a>
      </div>
      <div class="p-4 w-100 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="col-md-8 col-xl-6">
          <!-- Header -->
          <div class="mb-5">
            <p class="mb-3">
              <i class="fa fa-2x fa-circle-notch text-primary-light"></i>
            </p>
            <h1 class="fw-bold mb-2">
              Two Factor Authentication
            </h1>
            <p class="fw-medium text-muted">
              Please confirm your account by entering the authorization code sent to your mobile number *******5974.
            </p>
          </div>
          <!-- END Header -->

          <!-- Two Factor Form -->
          <form id="form-2fa" action="be_pages_auth_all.php" method="POST">
            <div class="d-flex items-center justify-content-center gap-1 gap-sm-2 mb-4">
              <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num1" name="num1" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num2" name="num2" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num3" name="num3" maxlength="1" style="width: 38px;">
              <span class="d-flex align-items-center">-</span>
              <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num4" name="num4" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num5" name="num5" maxlength="1" style="width: 38px;">
              <input type="text" class="form-control form-control-alt form-control-lg text-center px-0" id="num6" name="num6" maxlength="1" style="width: 38px;">
            </div>
            <div class="mb-4">
              <button type="submit" class="btn btn-lg btn-alt-primary">
                Submit
                <i class="fa fa-fw fa-arrow-right ms-1 opacity-50"></i>
              </button>
            </div>
            <p class="fs-sm pt-4 text-muted mb-0">
              Haven't received it? <a href="javascript:void(0)">Resend a new code</a>
            </p>
          </form>
          <!-- END Two Factor Form -->
        </div>
      </div>
      <div class="px-4 py-3 w-100 d-lg-none d-flex flex-column flex-sm-row justify-content-between fs-sm text-center text-sm-start">
        <p class="fw-medium text-black-50 py-2 mb-0">
          <strong><?php echo $one->name . ' ' . $one->version; ?></strong> &copy; <span data-toggle="year-copy"></span>
        </p>
        <ul class="list list-inline py-2 mb-0">
          <li class="list-inline-item">
            <a class="text-muted fw-medium" href="javascript:void(0)">Legal</a>
          </li>
          <li class="list-inline-item">
            <a class="text-muted fw-medium" href="javascript:void(0)">Contact</a>
          </li>
          <li class="list-inline-item">
            <a class="text-muted fw-medium" href="javascript:void(0)">Terms</a>
          </li>
        </ul>
      </div>
    </div>
    <!-- END Main Section -->
  </div>
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

<!-- Page JS Code -->
<?php $one->get_js('js/pages/op_auth_two_factor.min.js'); ?>

<?php require 'inc/_global/views/footer_end.php'; ?>