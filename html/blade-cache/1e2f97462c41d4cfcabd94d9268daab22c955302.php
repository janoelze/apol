<div class="tab-bar">
  <?php $__currentLoopData = Helpers::get_tab_bar_items(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a hx-boost href="<?php echo e($item['href']); ?>" class="<?php echo e($item['class']); ?>">
      <?php echo Helpers::embed($item['icon']); ?>

      <span><?php echo e($item['label']); ?></span>
    </a>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH /var/www/html/views/partials/tab-bar.blade.php ENDPATH**/ ?>