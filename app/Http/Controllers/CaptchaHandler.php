<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;



class CaptchaHandler extends BaseController
{


    public static function validateContactForm1($captcha){
        $userCaptchaResponse = intval($captcha);
        $decryptedAnswer = intval(Crypt::decryptString(session('contact_form_captcha_answer')));

        if ($userCaptchaResponse === $decryptedAnswer) {
            return true;
        } else {
            // Optionally, you could add more information here to help debug the issue
            return false;
        }
    }
    public function validateContactForm(Request $request)
    {
        $userCaptchaResponse = intval($request->input('captcha'));
        $decryptedAnswer = intval(Crypt::decryptString(session('contact_form_captcha_answer')));

        if ($userCaptchaResponse === $decryptedAnswer) {
            return response()->json(['valid' => true]);
        } else {
            // Optionally, you could add more information here to help debug the issue
            return response()->json([
                'valid' => false,
                'expected' => $decryptedAnswer,
                'received' => $userCaptchaResponse
            ], 422);
        }
    }


public function refreshContactForm()
{
    $number1 = mt_rand(1, 9);
    $number2 = mt_rand(1, 9);
    $image = imagecreatetruecolor(60, 30);
    $background = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagefill($image, 0, 0, $background);
    imagestring($image, 5, 5, 5, "$number1 + $number2", $textColor);
    ob_start();
    imagepng($image);
    $contents = ob_get_contents();
    ob_end_clean();
    $dataUri = "data:image/png;base64," . base64_encode($contents);
    imagedestroy($image);
    $encryptedAnswer = Crypt::encryptString($number1 + $number2);
    session(['contact_form_captcha_answer' => $encryptedAnswer]);
    return response()->json(['dataUri' => $dataUri]);
}






// SEGNALARSI VALIDATION

public function validateRegisterForm(Request $request)
{
    $userCaptchaResponse = intval($request->input('captcha'));
    $decryptedAnswer = intval(Crypt::decryptString(session('register_form_captcha_answer')));

    if ($userCaptchaResponse === $decryptedAnswer) {
        return response()->json(['valid' => true]);
    } else {
        // Optionally, you could add more information here to help debug the issue
        return response()->json([
            'valid' => false,
            'expected' => $decryptedAnswer,
            'received' => $userCaptchaResponse
        ], 422);
    }
}

public function refreshRegisterForm()
{
    $number1 = mt_rand(1, 9);
    $number2 = mt_rand(1, 9);
    $image = imagecreatetruecolor(60, 30);
    $background = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagefill($image, 0, 0, $background);
    imagestring($image, 5, 5, 5, "$number1 + $number2", $textColor);
    ob_start();
    imagepng($image);
    $contents = ob_get_contents();
    ob_end_clean();
    $dataUri = "data:image/png;base64," . base64_encode($contents);
    imagedestroy($image);
    $encryptedAnswer = Crypt::encryptString($number1 + $number2);
    session(['register_form_captcha_answer' => $encryptedAnswer]);
    return response()->json(['dataUri' => $dataUri]);
}






// Login VALIDATION

public function validateLoginForm(Request $request)
{
    $userCaptchaResponse = intval($request->input('captcha'));
    $decryptedAnswer = intval(Crypt::decryptString(session('login_form_captcha_answer')));

    if ($userCaptchaResponse === $decryptedAnswer) {
        return response()->json(['valid' => true]);
    } else {
        // Optionally, you could add more information here to help debug the issue
        return response()->json([
            'valid' => false,
            'expected' => $decryptedAnswer,
            'received' => $userCaptchaResponse
        ], 422);
    }
}

public function refreshLoginForm()
{
    $number1 = mt_rand(1, 9);
    $number2 = mt_rand(1, 9);
    $image = imagecreatetruecolor(60, 30);
    $background = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    imagefill($image, 0, 0, $background);
    imagestring($image, 5, 5, 5, "$number1 + $number2", $textColor);
    ob_start();
    imagepng($image);
    $contents = ob_get_contents();
    ob_end_clean();
    $dataUri = "data:image/png;base64," . base64_encode($contents);
    imagedestroy($image);
    $encryptedAnswer = Crypt::encryptString($number1 + $number2);
    session(['login_form_captcha_answer' => $encryptedAnswer]);
    return response()->json(['dataUri' => $dataUri]);
}


}
