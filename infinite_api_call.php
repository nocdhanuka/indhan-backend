<?php
$apiUrl = "http://dhanukabdapp.in:8088/dhanukaV2/v3/selfParticipationHistory/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzNywiaXNzIjoiaHR0cDovL2RoYW51a2FiZGFwcC5pbjo4MDg4L2RoYW51a2FWMi92Mi9sb2dpbiIsImlhdCI6MTc0OTYzODAzOCwiZXhwIjoxNzQ5NjQxNjM4LCJuYmYiOjE3NDk2MzgwMzgsImp0aSI6IlRvYlp2V2dsRDJKaGlhYVgifQ.zGUvd7PcmTffbjEmZGXEoTnJ_UCFyq0djbv9Y1drPoE";

$apiUrl1 = "http://dhanukabdapp.in:8088/dhanukaV2/v3/selfParticipationHistory/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzNywiaXNzIjoiaHR0cDovL2RoYW51a2FiZGFwcC5pbjo4MDg4L2RoYW51a2FWMi92Mi9sb2dpbiIsImlhdCI6MTc0OTYzODAzOCwiZXhwIjoxNzQ5NjQxNjM4LCJuYmYiOjE3NDk2MzgwMzgsImp0aSI6IlRvYlp2V2dsRDJKaGlhYVgifQ.zGUvd7PcmTffbjEmZGXEoTnJ_UCFyq0djbv9Y1drPoE";

$apiUrl2 = "http://dhanukabdapp.in:8088/dhanukaV2/v3/selfParticipationHistory/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzNywiaXNzIjoiaHR0cDovL2RoYW51a2FiZGFwcC5pbjo4MDg4L2RoYW51a2FWMi92Mi9sb2dpbiIsImlhdCI6MTc0OTYzODAzOCwiZXhwIjoxNzQ5NjQxNjM4LCJuYmYiOjE3NDk2MzgwMzgsImp0aSI6IlRvYlp2V2dsRDJKaGlhYVgifQ.zGUvd7PcmTffbjEmZGXEoTnJ_UCFyq0djbv9Y1drPoE";

$apiUrl3 = "http://dhanukabdapp.in:8088/dhanukaV2/v3/selfParticipationHistory/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzNywiaXNzIjoiaHR0cDovL2RoYW51a2FiZGFwcC5pbjo4MDg4L2RoYW51a2FWMi92Mi9sb2dpbiIsImlhdCI6MTc0OTYzODAzOCwiZXhwIjoxNzQ5NjQxNjM4LCJuYmYiOjE3NDk2MzgwMzgsImp0aSI6IlRvYlp2V2dsRDJKaGlhYVgifQ.zGUvd7PcmTffbjEmZGXEoTnJ_UCFyq0djbv9Y1drPoE";

$apiUrl4 = "http://dhanukabdapp.in:8088/dhanukaV2/v3/selfParticipationHistory/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzNywiaXNzIjoiaHR0cDovL2RoYW51a2FiZGFwcC5pbjo4MDg4L2RoYW51a2FWMi92Mi9sb2dpbiIsImlhdCI6MTc0OTYzODAzOCwiZXhwIjoxNzQ5NjQxNjM4LCJuYmYiOjE3NDk2MzgwMzgsImp0aSI6IlRvYlp2V2dsRDJKaGlhYVgifQ.zGUvd7PcmTffbjEmZGXEoTnJ_UCFyq0djbv9Y1drPoE";

for ($i = 0; $i < 10000; $i++) {
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    $ch1 = curl_init($apiUrl1);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch1);
    curl_close($ch1);

    $ch2 = curl_init($apiUrl2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch2);
    curl_close($ch2);

    $ch3 = curl_init($apiUrl3);
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch3);
    curl_close($ch3);

    $ch4 = curl_init($apiUrl4);
    curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch4);
    curl_close($ch4);
    echo "i=>". $i."\n";
}
