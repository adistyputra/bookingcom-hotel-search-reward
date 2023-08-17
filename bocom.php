<?php
$cookieBOCOM = file_get_contents('cookie.txt');
$link = input("MASUKKAN LINK ?? ");
echo "\nPROSESS AGAK LAMBAT KARENA HANYAK CEK HOTEL YANG MENDAPATKAN REWARD & TIDAK BUTUH PEMBAYARAN DIAWAL!!";

$page = file_get_contents('page.txt');
$pages = explode("\r\n", $page);
$uniquePages = array_unique($pages);

// BAGIAN UNTUK SEARCH SETIAP PAGE
foreach ($uniquePages as $searchPage) {
    $linkPage = $link . $searchPage;
    $resultSearch = searchHotel($linkPage, $cookieBOCOM);

    if (strpos($resultSearch, "Page Not Found")) {
        echo "LINK TIDAK VALID!! CEK ULANG!!";
    } else {
        preg_match_all('/4251">(.*)<\/h2>/U', $resultSearch, $track);
        preg_match('/16cb"><button aria-label=" (.*?)" aria-current="page"/U', $resultSearch, $pageress); // PAGE HALAMAN
        preg_match_all('/<div class="f8425bf46a">(.*)<\/div>/U', $resultSearch, $trackss);
        preg_match_all('/class="a4225678b2"><a href="(.*)" class=/U', $resultSearch, $linkHtEL);
        preg_match_all('/>Earn Rp&nbsp;(.*)<\/span><\/span>/U', $resultSearch, $shrt);
        echo "\nGET DATA HOTEL PAGE ~ " . $pageress[1] . " ~\n";

        // BAGIAN UNTUK GET LINK HOTEL
        foreach ($linkHtEL[1] as $linkHOTEL) {
            // MASUK KEBAGIAN DALAMNYA
            $resultLink = cekDetail($linkHOTEL, $cookieBOCOM);

            // SHORT LINK
            $shortLink = shortLink($linkHOTEL);
            preg_match('/"short_id":"(.*?)","expire/U', $shortLink, $shrt);

            if (preg_match_all('/class="bui-badge__text">\n(.*)\n<\/span>/U', $resultLink, $linkHtEL)) {
                $rewardHotel = $linkHtEL[1][0];
                preg_match('/class="hp-header--title--text">(.*?)<\/span>/U', $resultLink, $namaHotel);

                if (strpos($rewardHotel, "Free!") !== false) {
                } elseif (strpos($rewardHotel, "Earn" && "Dapatkan Kredit") !== false) {
                    preg_match_all('/class="bui-badge__text">\nDapatkan Kredit (.*)\n<\/span>/U', $resultLink, $rewadhtel);
                    $countReward = 0;
                    $rewardList = array();
                    foreach ($rewadhtel[1] as $rewardHotelsss) {
                        if (!empty($rewardHotelsss)) {
                            $countReward++;
                            $rewardList[] = "[$countReward. $rewardHotelsss]";
                        }
                    }
                    if (strpos($resultLink, "No credit card" && "Tanpa kartu kredit") !== false) {
                        $hasilPAGE =  "[+] " . strtoupper($namaHotel[1]) . " => REWARD : " . implode(' ', $rewardList) . " => TIDAK BUTUH CREDIT CARD!! | LINK : https://t.ly/$shrt[1]\n";
                        echo $hasilPAGE;
                    } else {
                        $hasilPAGE =  "[+] " . strtoupper($namaHotel[1]) . " => REWARD : " . implode(' ', $rewardList) . " => BUTUH CREDIT CARD!! | LINK : https://t.ly/$shrt[1]\n";
                        echo $hasilPAGE;
                    }

                    // UNTUK SAVE HASIL
                    $saveDATA = fopen("bocomLink.txt", "a");
                    fputs($saveDATA, "[+] HASIL PAGE : $pageress[1] => $hasilPAGE\r");
                    fclose($saveDATA);
                } else {
                    echo "[!] " . strtoupper($namaHotel[1]) . " HOTEL TIDAK ADA REWARD!! | LINK : https://t.ly/$shrt[1]\n";
                }
            } else {
            }
        }
    }
}

echo "RESULT SAVE TO bocomLink.txt\n";

function searchHotel($linkPage, $cookieBOCOM)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkPage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array(
        'Authority: www.booking.com',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: max-age=0',
        'Cookie: ' . $cookieBOCOM,
        'Dnt: 1',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}

function cekDetail($linkHOTEL, $cookieBOCOM)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkHOTEL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array(
        'Authority: www.booking.com',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: max-age=0',
        'Cookie: ' . $cookieBOCOM,
        'Dnt: 1',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $resultLink = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: DiSiNi!! ' . curl_error($ch);
        die();
    }
    curl_close($ch);
    return $resultLink;
}

function shortLink($linkHOTEL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://t.ly/api/v1/link/shorten');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"long_url\":\"$linkHOTEL\",\"domain\":\"https://t.ly/\"}");
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array(
        'Authority: t.ly',
        'Accept: application/json',
        'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
        'Authorization: Bearer PupuS2Z3b6qGKfFo4EGY3FwCqoJJUsJZwmeDOAMrvTE5ExhNsOb7evgLsKdD',
        'Content-Type: application/json'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}
