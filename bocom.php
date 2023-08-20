<?php

/*
Author: FIKRI
FB: @YAELAHFIK
IG : @F.I.K.R.I._
*/

$cookieBOCOM = file_get_contents('cookie.txt');
$namaFileURL = input("[•|•] MASUKKAN NAMA FILE URL BOCOM ?? *(CTH: data.txt)");
$hotelSearchReward = inputRP("[•|•] MAU CARI REWARD HOTEL YANG BERAPA ?? *(CTH: 50.000)");
if (empty($namaFileURL)) {
    echo "[!|!] Input Tidak Boleh Kosong.\n";
    die();
} else if (!is_numeric($hotelSearchReward) || empty($hotelSearchReward)) {
    echo "[!|!] Input Minimal Reward harus berupa angka saja.\n";
    die();
}


$rewardHOTELmin = preg_replace('/\D/', '', $hotelSearchReward);
$randss = generateRandomStringss();

if ($rewardHOTELmin > 300000) {
    echo "\n[!|!] GAK PUNYA OTAK, CARI SENDIRI TOLOL!!\n\n";
    die();
} else {
    $apages = file_get_contents($namaFileURL);
    $f = explode("\r\n", $apages);
    $f = array_unique($f);

    foreach ($f as $linkSearch) {
        if (strpos($linkSearch, "&offset") !== false) {
            $pecahOffset = explode("&offset", $linkSearch);
            $link = $pecahOffset[0];
        } else {
            $link = $linkSearch;
        }

        echo "\n[•|•] DITEMUKAN " . count($f) . " URL DARI FILE !!\n";
        echo "[•|•] PROSESS AGAK LAMBAT KARENA HANYAK CEK HOTEL YANG MENDAPATKAN REWARD MIN Rp $hotelSearchReward & TIDAK BUTUH PEMBAYARAN DIAWAL!!\n\n";

        $resultSearch = searchHotel($link, $cookieBOCOM);
        preg_match_all('/2028338ea">(.*?)<\/button><\/li><\/ol>/U', $resultSearch, $getLaPag);
        $Hassige = explode('ea">', $getLaPag[1][0]);
        $pageLasy = (int)$Hassige[2] * 25 - 25;
        $offset = 0;
        while ($offset <= $pageLasy) {
            $saveDATA = fopen("offset/offset0-$pageLasy-$randss.txt", "a");
            fputs($saveDATA, "$offset\r\n");
            fclose($saveDATA);
            $offset += 25;
        }
        echo "[•|•] BERHASIL MEMBUAT FILE OFFSET!! \n";

        $page = file_get_contents("offset/offset0-$pageLasy-$randss.txt");
        $pages = explode("\r\n", $page);
        $uniquePages = array_unique($pages);
        echo "[•|•] CEK URL $link  \n";

        foreach ($uniquePages as $searchPage) {
            $linkPage = $link . '&offset=' . $searchPage;
            $resultSearch = searchHotel($linkPage, $cookieBOCOM);

            if (strpos($resultSearch, "Page Not Found") === false && strpos($resultSearch, "Menampilkan" && "Showing") !== false) {
                preg_match('/16cb"><button aria-label=" (.*?)" aria-current="page"/U', $resultSearch, $pageress);
                preg_match_all('/4251">(.*)<\/h2>/U', $resultSearch, $track);
                preg_match_all('/<div class="f8425bf46a">(.*)<\/div>/U', $resultSearch, $trackss);
                preg_match_all('/class="a4225678b2"><a href="(.*)" class=/U', $resultSearch, $linkHtEL);
                preg_match_all('/>Earn Rp&nbsp;(.*)<\/span><\/span>/U', $resultSearch, $shrt);
                preg_match_all('/class="f6431b446c d5f78961c3">(.*):/U', $resultSearch, $searschN);
                preg_match_all('/6c47">(.*)ulasan<\/div>/U', $resultSearch, $ripiewHotel);

                $pageHalaman = empty($pageress) ? "ERROR!! LANJUT NEXT PAGE!!" : $pageress[1];
                $pageNegaraHalaman = empty($searschN) ? "ERROR!! LANJUT NEXT PAGE!!" : $searschN[1][0];

                echo "\n[•|•] GET DATA HOTEL " . strtoupper($pageNegaraHalaman) . " { offset=$searchPage } PAGE ~ " . $pageHalaman . " ~ \n";

                foreach ($linkHtEL[1] as $linkHOTEL) {
                    $resultLink = cekDetail($linkHOTEL, $cookieBOCOM);

                    if (preg_match_all('/class="bui-badge__text">\n(.*)\n<\/span>/U', $resultLink, $linkHtEL)) {
                        $rewardHotel = $linkHtEL[1][0];
                        preg_match('/class="hp-header--title--text">(.*?)<\/span>/U', $resultLink, $namaHotel);
                        preg_match('/6c47">(.*?)ulasan<\/div>/U', $resultLink, $ripiewHotel);
                        $shortLink = shortLink($linkHOTEL);
                        $response = json_decode($shortLink, true);
                        $linkShort = $response['short_url'];

                        if (strpos($rewardHotel, "Free!") === false && strpos($rewardHotel, "Earn" && "Dapatkan Kredit") !== false) {
                            preg_match_all('/class="bui-badge__text">\nDapatkan Kredit (.*)\n<\/span>/U', $resultLink, $rewadhtel);

                            $rewardList = array();
                            foreach ($rewadhtel[1] as $rewardHotelsss) {
                                $rewardValue = preg_replace('/\D/', '', $rewardHotelsss);
                                if ($rewardValue > $rewardHOTELmin) {
                                    $rewardList[] = $rewardHotelsss;
                                }
                            }

                            if (!empty($rewardList)) {
                                foreach ($rewardList as $index => $rewardHotelsss) {
                                    $rewardHOTEL =  "[ " . ($index + 1) . ". $rewardHotelsss ]";
                                }
                            } else {
                                $rewardHOTEL =  "KURANG DARI Rp $hotelSearchReward!!";
                            }

                            if (strpos($resultLink, "No credit card" && "Tanpa kartu kredit") !== false) {
                                $hasilPAGE = "[+] [ $ripiewHotel[1] Ulasan ] " . strtoupper($namaHotel[1]) . " => REWARD : $rewardHOTEL => TIDAK BUTUH CREDIT CARD!! | LINK : $linkShort\n";
                                echo $hasilPAGE;

                                $saveDATA = fopen("result/NoNeedCC-$pageNegaraHalaman.txt", "a");
                                fputs($saveDATA, "[+] $hasilPAGE HASIL PAGE CEK URL " . strtoupper($searschN[1][0]) . " ~ $pageress[1] ~ \r");
                                fclose($saveDATA);
                            } else {
                                $hasilPAGE = "[+] [ $ripiewHotel[1] Ulasan ] " . strtoupper($namaHotel[1]) . " => REWARD : " . implode(' ', $rewardList) . " => BUTUH CREDIT CARD!! | LINK : $linkShort\n";
                                echo $hasilPAGE;

                                $saveDATA = fopen("result/NeedCC-$pageNegaraHalaman.txt", "a");
                                fputs($saveDATA, "[+] [ $ripiewHotel[1] Ulasan ] " . strtoupper($namaHotel[1]) . " => REWARD : " . implode(' ', $rewardList) . " => BUTUH CREDIT CARD!! | LINK : $linkShort| HASIL PAGE CEK URL " . strtoupper($searschN[1][0]) . " ~ $pageress[1] ~ \r");
                                fclose($saveDATA);
                            }
                        } else {
                            // echo "[+] [ $ripiewHotel[1] Ulasan ] " . strtoupper($namaHotel[1]) . " => KOSONG TIDAK ADA REWARD!!\n";
                        }
                    }
                }
            } else {
                echo "[!] PAGE / HALAMAN TIDAK ADA RESULT!!\n";
            }
        }
    }
    echo "\nRESULT SAVE TO hasil/NeedCC-$pageNegaraHalaman.txt\n";
}


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

function generateRandomStringss()
{
    $bytess = random_bytes(16);
    $uuids = vsprintf('%s%s', str_split(bin2hex($bytess), 3));
    return $uuids;
}

function input($text)
{
    echo $text . " => : ";
    $a = trim(fgets(STDIN));
    return $a;
}
function inputRP($text)
{
    echo $text . " => Rp ";
    $a = trim(fgets(STDIN));
    return $a;
}

}
