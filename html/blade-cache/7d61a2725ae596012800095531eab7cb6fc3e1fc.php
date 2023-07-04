<!-- Stored in resources/views/layouts/app.blade.php -->
 
<html>
    <head>
        <title>Apol - <?php echo $__env->yieldContent('title'); ?></title>
        <meta name="viewport" content="width=device-width, viewport-fit=cover,initial-scale=1.0,user-scalable=no" />
        <script src="https://unpkg.com/htmx.org@1.9.2"></script>
        <style>
            <?php echo Helpers::embed('apol.css'); ?>

        </style>
    </head>
    <body>
        <div class="container">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <?php echo $__env->make('partials.tab-bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </body>
</html><?php /**PATH /var/www/html/views/layouts/app.blade.php ENDPATH**/ ?>