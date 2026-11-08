<?php if (is_logged_in()): ?>
        </div><!-- /.content-area -->
    </div><!-- /.main-content -->
</div><!-- /.wrapper -->
<?php else: ?>
</div><!-- /.auth-wrapper -->
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo asset('js/script.js'); ?>"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
