<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verify Email</title>
</head>

<body style="
    margin:0;
    padding:0;
    background:#f5f7fb;
    font-family:Arial,Helvetica,sans-serif;
">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:40px 20px;">

                <table width="600" cellpadding="0" cellspacing="0" style="
        background:#ffffff;
        border-radius:12px;
        overflow:hidden;
        box-shadow:0 2px 10px rgba(0,0,0,.08);
    ">

                    <tr>
                        <td style="
            background:#0d6efd;
            color:#ffffff;
            padding:30px;
            text-align:center;
        ">
                            <h1 style="margin:0;">
                                Expense Tracker
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:40px;">
                            <h2>
                                Verify Your Email Address
                            </h2>

                            <p>
                                Hello
                                <strong>
                                    <?= esc($firstName) ?>
                                </strong>,
                            </p>

                            <p>
                                Thank you for creating an account.
                                Please verify your email address
                                to activate your account.
                            </p>

                            <p style="text-align:center;margin:40px 0;">
                                <a href="<?= esc($verificationUrl) ?>" style="
                    background:#0d6efd;
                    color:#ffffff;
                    padding:14px 30px;
                    text-decoration:none;
                    border-radius:6px;
                    display:inline-block;
                    font-weight:bold;
                ">
                                    Verify Email
                                </a>
                            </p>

                            <p>
                                If the button above does not work,
                                copy and paste this link into
                                your browser:
                            </p>

                            <p>
                                <a href="<?= esc($verificationUrl) ?>">
                                    <?= esc($verificationUrl) ?>
                                </a>
                            </p>

                            <p>
                                If you did not create this account,
                                you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="
            background:#f8f9fa;
            text-align:center;
            padding:20px;
            color:#6c757d;
            font-size:14px;
        ">
                            © <?= date('Y') ?>
                            Expense Tracker.
                            All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>