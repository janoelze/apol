 
<?php $__env->startSection('title', 'Page Title'); ?>
 
<?php $__env->startSection('content'); ?>
    <?php if($subreddit_id ?? false): ?>
        <div class="tab-title">/r/<?php echo e($subreddit_id); ?></div>
    <?php else: ?>
        <div class="tab-title">Home</div>
    <?php endif; ?>
    <div class="listing">
        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($entry['kind'] == 'Listing' && $entry['data']['children'][0]['kind'] == 't3'): ?>
                <div class="list-t3">
                    <?php $__currentLoopData = $entry['data']['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $is_last = $key == count($entry['data']['children']) - 1 && !$is_comments_page;
                        ?>
                        <?php echo $__env->make('partials.post', ['data' => $value['data'], 'is_last' => $is_last], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <?php if($entry['kind'] == 'Listing' && $entry['data']['children'][0]['kind'] == 't1'): ?>
                <div class="list-t1">
                    <?php $__currentLoopData = $entry['data']['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.comment', ['data' => $value['data']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="progress-indicator">Loading…</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/views/page.blade.php ENDPATH**/ ?>