<script src="<?php echo $js ?>jquery-1.12.1.min.js"></script>
<script src="<?php echo $js ?>jquery-ui.min.js"></script>
<script src="<?php echo $js ?>bootstrap.min.js"></script>
<script src="<?php echo $js ?>jquery.selectBoxIt.min.js"></script>
<script src="<?php echo $js ?>front.js"></script>
<script src="<?=$js?>jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        // validate signup form on keyup and submit
        $("#form_validate").validate({
            rules: {
                user: {
                    required: true,
                    minlength: 2
                },
                pass: {
                    required: true,
                    minlength: 5
                }
            },
            messages: {
                user: {
                    required: "Please enter a username",
                    minlength: "Your username must consist of at least 2 characters"
                },
                pass: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                }
            }
        });
    });
</script>

</body>
</html>