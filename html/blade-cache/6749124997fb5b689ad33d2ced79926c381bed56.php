<?php if($is_last): ?>
  <a class="thing t3" href="<?php echo e(Helpers::get_base_url()); ?><?php echo e($data['permalink']); ?>" hx-get="<?php echo Helpers::get_next_page($data); ?>" hx-select=".list-t3" hx-trigger="revealed" hx-swap="afterend" hx-indicator=".progress-indicator">
<?php else: ?>
  <a class="thing t3" href="<?php echo e(Helpers::get_base_url()); ?><?php echo e($data['permalink']); ?>">
<?php endif; ?>
  <div class="title"><?php echo e($data['title']); ?></div>
  <?php if($data['url'] ?? false): ?>
    <?php if($picture = Helpers::get_embeddable_picture($data)): ?>
        <div class="image" href="<?php echo e($data['url']); ?>">
          <img src="<?php echo e($picture['src']); ?>" />
        </div>
      <?php else: ?>
        <div class="url">
          <div class="url-icon"><?php echo Helpers::embed('./img/link.svg'); ?></div>
          <div class="url-text">
            <div>
              <span><?php echo e(Helpers::get_host($data['url'])); ?></span><span><?php echo e(Helpers::get_path($data['url'])); ?></span>
            </div>
          </div>
          <div class="url-icon"><?php echo Helpers::embed('./img/chevron-right.svg'); ?></div>
        </div>
      <?php endif; ?>
  <?php endif; ?>
  <div class="meta">
    <div><?php echo Helpers::embed('./img/arrow-up.svg'); ?> <?php echo e(Helpers::formatk($data['ups'])); ?></div>
    <div><?php echo Helpers::embed('./img/message-circle.svg'); ?> <?php echo e(Helpers::formatk($data['num_comments'])); ?></div>
    <div><?php echo Helpers::embed('./img/clock.svg'); ?> <?php echo e(Helpers::relative_time($data['created_utc'])); ?></div>
  </div>
</a><?php /**PATH /var/www/html/views/partials/post.blade.php ENDPATH**/ ?>