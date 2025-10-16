<?php require 'inc/_global/config.php'; ?>
<?php require 'inc/_global/views/head_start.php'; ?>
<?php require 'inc/_global/views/head_end.php'; ?>
<?php require 'inc/_global/views/page_start.php'; ?>

<!-- Page Content -->
<div class="hero-static d-flex align-items-center">
  <div class="w-100">
    <!-- Two Factor Section -->
    <div class="bg-body-extra-light">
      <div class="content content-full">
        <div class="row g-0 justify-content-center text-center">
          <div class="col-md-8 col-lg-6 col-xl-4 py-4 px-4 px-lg-5">
            <!-- Header -->
            <div class="text-center">
              <p class="mb-2">
                <i class="fa fa-2x fa-circle-notch text-primary"></i>
              </p>
              <h1 class="h4 mb-1">
                Two Factor Authentication
              </h1>
              <p class="fw-medium text-muted mb-3">
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
                <button type="submit" class="btn btn-alt-primary px-4">
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
      </div>
    </div>
    <!-- END Two Factor Section -->

    <!-- Footer -->
    <div class="fs-sm text-center text-muted py-3">
      <strong><?php echo $one->name . ' ' . $one->version; ?></strong> &copy; <span data-toggle="year-copy"></span>
    </div>
    <!-- END Footer -->
  </div>
</div>
<!-- END Page Content -->

<?php require 'inc/_global/views/page_end.php'; ?>
<?php require 'inc/_global/views/footer_start.php'; ?>

<!-- Page JS Code -->
<?php $one->get_js('js/pages/op_auth_two_factor.min.js'); ?>

<?php require 'inc/_global/views/footer_end.php'; ?>
