<?php
<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject ?? 'بدون موضوع' }}</title>
<style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .header { background-color: #007bff; color: #fff; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
    .content { padding: 20px; }
    .footer { text-align: center; font-size: 12px; color: #777; padding-top: 10px; border-top: 1px solid #eee; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>پیام از Noxon Label</h2>
    </div>
    <div class="content">
        <p>{{ $body }}</p>
    </div>
    <div class="footer">
        <p>ارسال شده در {{ now()->format('Y-m-d H:i') }} | <a href="https://www.noxonlabel.shop">Noxon Label</a></p>
    </div>
</div>
</body>
</html>
