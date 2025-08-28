<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan Pernikahan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f1f1;
        }
        .couple-names {
            font-size: 28px;
            font-weight: bold;
            color: #d63384;
            margin: 10px 0;
        }
        .wedding-details {
            background: linear-gradient(135deg, #ffeef8 0%, #f3e8ff 100%);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .detail-item {
            margin: 10px 0;
            padding: 8px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #6b46c1;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #d63384 0%, #6b46c1 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 10px;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #d63384; margin: 0;">💕 Undangan Pernikahan 💕</h1>
            <div class="couple-names">
                {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
            </div>
        </div>

        <div class="greeting">
            @if($recipientName)
                Kepada Yth. <strong>{{ $recipientName }}</strong>,
            @else
                Kepada Yth. Bapak/Ibu/Saudara/i,
            @endif
        </div>

        <p style="margin-bottom: 20px;">Assalamu'alaikum Wr. Wb.</p>
        
        <p>Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami:</p>

        <div class="wedding-details">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="margin-bottom: 15px;">
                    <strong style="color: #d63384; font-size: 18px;">{{ $invitation->bride_name }}</strong><br>
                    <small style="color: #666;">Putri dari Bapak {{ $invitation->bride_father ?? '...' }} & Ibu {{ $invitation->bride_mother ?? '...' }}</small>
                </div>
                
                <div style="margin: 20px 0; font-size: 24px; color: #6b46c1;">💕</div>
                
                <div style="margin-bottom: 15px;">
                    <strong style="color: #d63384; font-size: 18px;">{{ $invitation->groom_name }}</strong><br>
                    <small style="color: #666;">Putra dari Bapak {{ $invitation->groom_father ?? '...' }} & Ibu {{ $invitation->groom_mother ?? '...' }}</small>
                </div>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">📅 Hari/Tanggal:</span>
                {{ date('l, d F Y', strtotime($invitation->wedding_date)) }}
            </div>
            
            @if($invitation->wedding_time)
            <div class="detail-item">
                <span class="detail-label">🕐 Waktu:</span>
                {{ $invitation->wedding_time }}
            </div>
            @endif
            
            <div class="detail-item">
                <span class="detail-label">📍 Tempat:</span>
                {{ $invitation->venue ?? $invitation->location }}
            </div>

            @if($invitation->additional_notes)
            <div class="detail-item">
                <span class="detail-label">📝 Catatan:</span>
                {{ $invitation->additional_notes }}
            </div>
            @endif
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <p style="margin-bottom: 15px;">Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kedua mempelai.</p>
            
            <p style="margin-bottom: 20px;">Atas kehadiran dan doa restunya, kami ucapkan terima kasih.</p>
            
            <p style="margin-bottom: 20px; font-style: italic;">Wassalamu'alaikum Wr. Wb.</p>
            
            <div style="font-weight: bold; color: #d63384; font-size: 18px;">
                {{ $invitation->groom_name }} & {{ $invitation->bride_name }}
            </div>
        </div>

        <div class="footer">
            <p>Undangan ini dibuat dengan ❤️ menggunakan UndanginAja</p>
            <p style="font-size: 12px; color: #999;">
                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
