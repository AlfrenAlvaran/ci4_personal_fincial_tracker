<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f9; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9; padding:30px 0;">
        <tr>
            <td align="center">

                <!-- CARD -->
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:#198754; padding:20px; text-align:center; color:#ffffff;">
                            <h2 style="margin:0;">Financial Tracker</h2>
                            <p style="margin:5px 0 0; font-size:14px;">Secure Password Reset</p>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:30px; color:#333333;">

                            <h3 style="margin-top:0;">Hello <?= esc($name) ?>,</h3>

                            <p style="font-size:15px; line-height:1.6;">
                                We received a request to reset your password. If you did not make this request, you can safely ignore this email.
                            </p>

                            <p style="font-size:15px; line-height:1.6;">
                                To reset your password, click the button below:
                            </p>

                            <!-- BUTTON -->
                            <p style="text-align:center; margin:30px 0;">
                                <a href="<?= esc($link) ?>"
                                   style="background:#198754; color:#ffffff; padding:12px 25px; text-decoration:none; border-radius:6px; font-weight:bold; display:inline-block;">
                                    Reset Password
                                </a>
                            </p>

                            <p style="font-size:13px; color:#666; line-height:1.5;">
                                This link will expire in <strong>1 hour</strong> for your security.
                            </p>

                            <hr style="border:none; border-top:1px solid #eee; margin:25px 0;">

                            <p style="font-size:12px; color:#999;">
                                If the button doesn’t work, copy and paste this link into your browser:
                            </p>

                            <p style="font-size:12px; word-break:break-all; color:#198754;">
                                <?= esc($link) ?>
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#f8f9fa; padding:15px; text-align:center; font-size:12px; color:#888;">
                            © <?= date('Y') ?> Financial Tracker. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>