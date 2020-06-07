<?php

namespace App\Http\Controllers;

//
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\CentralSetting;
//
use Image;

class ReportController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * Displayed number of signatures to use
     */
    CONST DISPLAY_NUMBER_SIGNATURES = 3;

    /**
     * Save signature as encrypted file in folder
     * 
     * @param Request $request
     * 
     * @return json
     */
    public function saveSignature(Request $request) {
        $pngFileRaw = "signature_" . time();
        $pngFileName = $pngFileRaw . ".png";
        $encFileName = $pngFileRaw . ".enc";

        $userId = Auth::user()->id;

        $path = storage_path('app/public/ratrak/' . $userId . '/');


        $pathThumbnail = storage_path('app/public/ratrak/' . $userId . '/thumbnail/');
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0775, true, true);
        }
        if (!File::exists($pathThumbnail)) {
            File::makeDirectory($pathThumbnail, $mode = 0775, true, true);
        }
        if (File::exists($path)) {
            /**
             * Create PNG file
             */
            Image::make(file_get_contents($request->dataURL))->save($path . $pngFileName);

            /**
             * Create Thumbnail
             */
            Image::make(file_get_contents($request->dataURL))->save($pathThumbnail . $pngFileName);
            $this->createThumbnail($pathThumbnail . $pngFileName);

            /**
             * Encrypt File
             */
            $password = Config::get('openssl.zip_signature');
            $this->encryptFile($path . $pngFileName, $password, $path . $encFileName);

            chmod($path . $pngFileName, 0755);
            chmod($path . $encFileName, 0755);
            usleep(500000);


            DB::table('users')->where('id', $userId)->update(['signature' => 1]);

            /**
             * Deleting the original PNG file
             */
            File::Delete($path . $pngFileName);
        }

        $delete = DB::table('centralSetting')
                ->where([['userId', '=', $userId], ['actionType', '=', CentralSetting::IN_PROCESS],])
                ->delete();

        if ($delete) {
            $set = new \stdClass();
            $set->colorPage = '#9bcbe1';
            $set->fontSize = '12';
            $set->background = '/storage/app/public/ratrak/reiseuhu-CXwnP8jO34M-unsplash.jpg';
            Auth::user()->setSettings(CentralSetting::RE_E_SIGNATURE, $set);
        } else {
            $set = new \stdClass();
            $set->colorPage = '#FFF03F';
            $set->fontSize = '12';
            $set->background = '/storage/app/public/ratrak/6968732-lake-mountains-view.jpg';
            Auth::user()->setSettings(CentralSetting::E_SIGNATURE, $set);
        }
        


        return response()->json(true);
    }

    /**
     * Redefine Signature
     * 
     * @param Request $request
     * @return json
     */
    public function redefineSignature(Request $request) {

        DB::table('users')->where('id', $request->userId)->update(['signature' => 0]);

        DB::table('centralSetting')
                ->where([['userId', '=', $request->userId], ['actionType', '=', CentralSetting::E_SIGNATURE],])
                ->update(['actionType' => CentralSetting::IN_PROCESS]);



        return response()->json(true);
    }

    /**
     * Encrypt the passed file and saves the result in a new file with ".enc" as suffix.
     * 
     * @param string $source Path to file that should be encrypted
     * @param string $key    The key used for the encryption
     * @param string $dest   File name where the encryped file should be written to.
     * @return string|false  Returns the file name that has been created or FALSE if an error occured
     */
    function encryptFile($source, $key, $dest) {
        $key = substr(sha1($key, true), 0, 16);
        $iv = openssl_random_pseudo_bytes(16);

        $error = false;
        if ($fpOut = fopen($dest, 'w')) {
            // Put the initialzation vector to the beginning of the file
            fwrite($fpOut, $iv);
            if ($fpIn = fopen($source, 'rb')) {
                while (!feof($fpIn)) {
                    $plaintext = fread($fpIn, 16 * 1000);
                    $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    // Use the first 16 bytes of the ciphertext as the next initialization vector
                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $ciphertext);
                }
                fclose($fpIn);
            } else {
                $error = true;
            }
            fclose($fpOut);
        } else {
            $error = true;
        }

        return $error ? false : $dest;
    }

    /**
     * Dencrypt the passed file and saves the result in a new file, removing the
     * last 4 characters from file name.
     * 
     * @param string $source Path to file that should be decrypted
     * @param string $key    The key used for the decryption (must be the same as for encryption)
     * @param string $dest   File name where the decryped file should be written to.
     * 
     * @return string|false  Returns the file name that has been created or FALSE if an error occured
     */
    function decryptFile($source, $key, $dest) {
        $key = substr(sha1($key, true), 0, 16);

        $error = false;
        if ($fpOut = fopen($dest, 'w')) {
            if ($fpIn = fopen($source, 'rb')) {
                // Get the initialzation vector from the beginning of the file
                $iv = fread($fpIn, 16);
                while (!feof($fpIn)) {
                    $ciphertext = fread($fpIn, 16 * (1000 + 1)); // we have to read one block more for decrypting than for encrypting
                    $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                    // Use the first 16 bytes of the ciphertext as the next initialization vector
                    $iv = substr($ciphertext, 0, 16);
                    fwrite($fpOut, $plaintext);
                }
                fclose($fpIn);
            } else {
                $error = true;
            }
            fclose($fpOut);
        } else {
            $error = true;
        }

        return $error ? false : $dest;
    }

    /**
     * @param int $installerNumber
     * @param int $numberOfSignatures
     * 
     * @return array of Symfony\Component\Finder\SplFileInfo
     */
    public function getLastSignatureArray(int $installerNumber, int $numberOfSignatures = 1): array {
        $allSignatures = array();
        $path = storage_path('/app/public/ratrak/' . $installerNumber . '/thumbnail/');


        if (File::exists($path)) {
            $allFiles = File::allFiles($path);
            if (!empty($allFiles)) {
                $allSignatures = $this->getRecentSignatures($allFiles, $numberOfSignatures);
            }
        }
        return $allSignatures;
    }

    /**
     * @param array $allFiles
     * @param int $numberOfSignatures
     * 
     * @return array
     */
    private function getRecentSignatures(array $allFiles = array(), int $numberOfSignatures = 1): array {
        $tempArray = array();

        foreach ($allFiles as $files) {
            $tempArray[$files->getATime()] = $files->getFilename();
        }

        krsort($tempArray);
        $output = array_slice($tempArray, 0, $numberOfSignatures);

        return $output;
    }

    /**
     * Create a thumbnail of specified size
     *
     * @param string $path path of thumbnail
     * @param int $width    50
     * @param int $height   20
     */
    public function createThumbnail($path, $width = 30, $height = 10) {
        $img = Image::make($path, $width, $height);
        $img->orientate();
        $img->resize(50, 25, function($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $img->save($path, 60);
    }

    /**


      /**
     * @param int $installerNumber
     * @param int $numberOfSignatures
     * 
     * @return array
     */
    public function getLastSignatureOrginalArray(int $installerNumber, int $numberOfSignatures = 1): array {
        $allSignatures = array();
        /**
         * Path directory with original signatures
         */
        $path = storage_path('/app/public/ratrak/' . $installerNumber . '/');

        if (File::exists($path)) {
            $allFiles = File::allFiles($path);
            if (!empty($allFiles)) {
                $allSignatures = $this->getRecentOrginalSignatures($allFiles, $numberOfSignatures);
            }
        }

        if (!empty($allSignatures)) {
            /**
             * Decrypt File
             */
            $password = Config::get('openssl.zip_signature');
            $ENC_FileName = $allSignatures[0];
            /**
             * File rename form: signature_1579277102.enc to: signature_1579277102.png
             */
            $allSignatures[0] = $this->renameFileExtensionTo($allSignatures[0], 'png');

            $this->decryptFile($path . $ENC_FileName, $password, $path . $allSignatures[0]);
        }
        return $allSignatures;
    }

    /**
     * @param array $allFiles
     * @param int $numberOfSignatures
     * 
     * @return array
     */
    public function getRecentOrginalSignatures(array $allFiles = array(), int $numberOfSignatures = 1): array {
        $tempArray = array();

        foreach ($allFiles as $files) {
            $path_info = pathinfo($files->getFilename());
            if ($path_info['extension'] == 'enc') {
                $tempArray[$files->getATime()] = $files->getFilename();
            }
        }

        krsort($tempArray);
        $output = array_slice($tempArray, 0, $numberOfSignatures);

        return $output;
    }

    /**
     * @param string $fileName
     * @param string $fileExtension
     * 
     * @return string
     */
    private function renameFileExtensionTo(string $fileName = '', string $fileExtension = 'png'): string {
        return preg_replace('/\..+$/', '.' . $fileExtension, $fileName);
    }

}
