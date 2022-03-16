<?php
require __DIR__ . '/vendor/autoload.php';
include 'config.php';

use NdCaptcha\NdCaptcha;
use Curl\Curl;

class Agoda
{
    function __construct()
    {
        $this->curl = new Curl();
    }

    public function register($email, $password, $firstName, $lastName, $recaptchaToken)
    {
        $this->curl->setHeader('Host', 'www.agoda.com');
        $this->curl->setTimeout(50);
        $this->curl->setConnectTimeout(50);
        $this->curl->setHeader('Connection', 'keep-alive');
        $this->curl->setHeader('sec-ch-ua', '" Not A;Brand";v="99", "Chromium";v="98", "Google Chrome";v="98"');
        $this->curl->setHeader('UL-App-Id', 'dictator');
        $this->curl->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->curl->setHeader('sec-ch-ua-mobile', '?0');
        $this->curl->setHeader('UL-Fallback-Origin', 'https://www.agoda.com');
        $this->curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.81 Safari/537.36');
        $this->curl->setHeader('sec-ch-ua-platform', '"Windows"');
        $this->curl->setHeader('Accept', '*/*');
        $this->curl->setHeader('Origin', 'https://www.agoda.com');
        $this->curl->setHeader('Sec-Fetch-Site', 'same-origin');
        $this->curl->setHeader('Sec-Fetch-Mode', 'cors');
        $this->curl->setHeader('Sec-Fetch-Dest', 'empty');
        $this->curl->setHeader('Referer', 'https://www.agoda.com/id-id/ul/login/signup?appId=dictator&rpcId=dictator-%23universal-login-app-732&initialPath=signup&sdkVersion=5.1.3&type=email');
        $this->curl->setHeader('Accept-Encoding', 'gzip, deflate, br');
        $this->curl->setHeader('Accept-Language', 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7');
        $this->curl->setOpt(CURLOPT_ENCODING, "");
        $this->curl->post('https://www.agoda.com/ul/api/v1/signup', '{"credentials":{"username":"' . $email . '","password":"' . $password . '","authType":"email"},"firstName":"' . $firstName . '","lastName":"' . $lastName . '","newsLetter":true,"captchaVerifyInfo":{"captchaType":"recaptcha","captchaResult":{"recaptchaToken":"' . $recaptchaToken . '"}}}');

        if ($this->curl->error) {
            echo '[-] Error: Register - ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage . "\n\n";
        } else {
            $responseData = $this->curl->response;
            $responseHeader = $this->curl->responseHeaders;

            preg_match_all('/ul.token=(.*); Max-Age/', $responseHeader['set-cookie'], $token);

            $returnMap = [
                'success' => $responseData->success,
                'token' => $token[1][0] ?? null,
            ];
            return $returnMap;
        }
    }

    public function claimRewards($url, $token)
    {
        $this->curl->setHeader('Host', 'www.agoda.com');
        $this->curl->setTimeout(50);
        $this->curl->setConnectTimeout(50);
        $this->curl->setHeader('Connection', 'keep-alive');
        $this->curl->setHeader('Cache-Control', 'max-age=0');
        $this->curl->setHeader('Upgrade-Insecure-Requests', '1');
        $this->curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.81 Safari/537.36');
        $this->curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');
        $this->curl->setHeader('Sec-Fetch-Site', 'same-origin');
        $this->curl->setHeader('Sec-Fetch-Mode', 'navigate');
        $this->curl->setHeader('Sec-Fetch-User', '?1');
        $this->curl->setHeader('Sec-Fetch-Dest', 'document');
        $this->curl->setHeader('sec-ch-ua', '*/*');
        $this->curl->setHeader('Origin', '" Not A;Brand";v="99", "Chromium";v="98", "Google Chrome";v="98"');
        $this->curl->setHeader('sec-ch-ua-mobile', '?0');
        $this->curl->setHeader('sec-ch-ua-platform', '"Windows"');
        $this->curl->setHeader('Referer', 'https://www.agoda.com/id-id/account/signin.html');
        $this->curl->setHeader('Accept-Encoding', 'gzip, deflate, br');
        $this->curl->setHeader('Accept-Language', 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7');
        $this->curl->setHeader('Cookie', 'token=' . $token);
        $this->curl->setOpt(CURLOPT_ENCODING, "");
        $this->curl->get($url);

        if ($this->curl->error) {
            return false;
        } else {
            return true;
        }
    }

    public function getBalance($key)
    {
        $this->curl->setHeader('Host', 'www.2captcha.com');
        $this->curl->setTimeout(50);
        $this->curl->setConnectTimeout(50);
        $this->curl->setHeader('Connection', 'keep-alive');
        $this->curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.81 Safari/537.36');
        $this->curl->setOpt(CURLOPT_ENCODING, "");
        $this->curl->get('https://2captcha.com/res.php?key=' . $key . '&action=getbalance&json=1');

        if ($this->curl->error) {
            return false;
        } else {
            return $this->curl->response;
        }
    }
}

function writeLog($location, $text, $config)
{
    $file = fopen($location, $config);
    fwrite($file, $text);
    fclose($file);
}

// init class
$agoda = new Agoda;

$captchaKey = '8124602caffb00d30e8e7f965e4abde7';

$recaptcha = new NdCaptcha(
    $captchaKey,
    'https://www.agoda.com/id-id/account/signin.html?option=signup',
    '6LfGHMcZAAAAAAN-k_ejZXRAdcFwT3J-KK6EnzBE',
);

$rewardUrls = [
    'First ($5)' => 'https://www.agoda.com/id-id/app/agodacashcampaign?campaignToken=b6ee49c1fc6734aa0eae8b75014cbd3032b1fea3&refreshOnBack',
    'Second ($5)' => 'https://www.agoda.com/id-id/app/agodacashcampaign?campaigntoken=8a126505ef0fcf80769338910e5579f9e19c4b20&refreshonback=&view=nativeapp',
];

echo "Semakin banyak buyer, semakin extend captchakey ini. \n";
echo "Semakin banyak buyer, semakin extend captchakey ini. \n";
echo "Semakin banyak buyer, semakin extend captchakey ini. \n\n";

$file = file_get_contents('email.txt');
$lists = explode("\r\n", $file);

foreach ($lists as $key => $email) {
    $keyBalance = $agoda->getBalance($captchaKey);
    if ($keyBalance->request > 0.003) {
        echo '[!] 2Captcha Balance: $' . $keyBalance->request . "\n";
        echo '[+] Register with email ' . $email . "\n";

        // init
        $captcha = $recaptcha->init();

        if ($captcha['status']) {
            echo '[!] Recaptcha Token: ' . $captcha['captcha'] . "\n";
            $register = $agoda->register($email, $password, $firstName, $lastName, $captcha['captcha']);
            if ($register['success']) {
                echo '[+] Registration on the Agoda Platform with email ' . $email . ' was successful.' . "\n";

                foreach ($rewardUrls as $key => $rewardUrl) {
                    // echo '[+] Claiming rewards on ' . $key . "\n";
                    $claim = $agoda->claimRewards($rewardUrls[$key], $register['token']);
                    if ($claim) {
                        echo '[+] Claiming rewards on ' . $key . ' was successful.' . "\n";
                    } else {
                        echo '[-] Claiming rewards on ' . $key . ' failed.' . "\n";
                    }
                }

                writeLog('account.txt', $email . " | " . $firstName . " " . $lastName . " | " . $password . "\n", 'a+');
                echo "\n";
            } else {
                echo '[-] Error: User already exists with the given email' . "\n\n";
            }
        }
    } else {
        echo "[!] 2Captcha balance is exhaust.\n\n";
        break;
        exit;
    }
}
