<x-mail::message>
Hello {{ $emailData['name'] }},
<br><br>

Terimakasih telah mendaftar Webinar Nasional {{ $emailData['year'] }}, yang dilaksanakan oleh Education Fair.
<br><br>
Berikut link Grup Webinar Nasional untuk informasi lebih lanjut.
<br>
Pastikan kamu masuk kedalam Grup Webinar Nasional ya!

<x-mail::button :url="$emailData['webinar_group']">
    Grup Webinar Nasional
</x-mail::button>

<x-mail::button :url="$emailData['link_zoom']">
    Link Zoom Webinar Nasional
</x-mail::button>

Salam Hangat,<br> <br><br>
{{ config('app.name') }}
</x-mail::message>
