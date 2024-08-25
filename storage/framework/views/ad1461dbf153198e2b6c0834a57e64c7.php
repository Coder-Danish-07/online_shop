<?php if(Session::has('error')): ?>
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-ban"></i> Error!</h5> <?php echo e(Session::get('error')); ?>

</div>
<?php endif; ?>

<?php if(Session::has('success')): ?>
<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-check"></i> Success!</h5> <?php echo e(Session::get('success')); ?>

</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\online_shop\resources\views/admin/message.blade.php ENDPATH**/ ?>