<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\UploadedFile;

class Parser extends Model
{
	/**
	 * @var UploadedFile
	 */
	public $file;
	public $file_card;

	public function rules()
	{
		return [
			[['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'csv', 'maxSize' => 1024 * 1024 * 20],
			[['file_card'], 'file', 'skipOnEmpty' => true, 'extensions' => 'csv', 'maxSize' => 1024 * 1024 * 20],
        ];
	}

	public function upload()
	{
        //return var_dump($this->file_card->extension);
		if (!empty($this->file_card) && $this->file_card->extension != 'csv'){
			return false;
		}

		if (!empty($this->file) && $this->file->extension != 'csv'){
			return false;
		}

		try {
			$file = (!empty($this->file)) ? fopen($this->file->tempName,"r") : fopen($this->file_card->tempName,"r");

			/*$rand = rand(0, 1000000);
			$newFile = fopen("tmp/" . $rand . '.csv', 'w');
			fclose($newFile);
			file_put_contents("tmp/" . $rand . '.csv', $file);*/
			/*$data_arr = [];
			while(!feof($file))
			{
				$data_arr[] = fgetcsv($file, 0, ';');
			}
			fclose($file);

			$data['header'] = array_shift($data_arr);
			$data['body'] = $data_arr;

			$result = [];
			foreach ($data['body'] as $k1 => $k2){
				foreach ($data['header'] as $k => $v){
					$result[$k1][strtolower($v)] = $k2[$k];
				}
			}*/
            $result = $fields = [];
            $i = 0;
            if ($file) {
                while (($row = fgetcsv($file, 4096, ';')) !== false) {
                    if (empty($fields)) {
                        $fields = $row;
                        continue;
                    }
                    foreach ($row as $k=>$value) {
                        $result[$i][strtolower($fields[$k])] = $value;
                    }
                    $i++;
                }
                if (!feof($file)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($file);
            }

			return $result;
		} catch (Exception $e) {
			\Yii::$app->session->setFlash('error', $e->getMessage() . "\n");
		}
		return false;
	}

	public static function normalize($word){
        $chars = [
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Ã' => 'A',
            'Ä' => 'Ae',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'Oe',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'Ue',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Þ' => 'B',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'ae',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'oe',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'ue',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
        ];

		$word = str_replace("-", " ", $word);
		$word = str_replace("'", "", $word);
		$word = str_replace('"', "", $word);
		$word = strtr($word, $chars);
		$word = preg_replace('/[^a-zA-Z \[\]\"\(\)-]/s', '', $word);
        $word = mb_strtoupper(trim($word));
		$word = htmlspecialchars($word);

		return $word;
	}

	public static function generateCardName($firstname, $lastname, $maxLength = 19){
		$firstName = self::normalize($firstname);
		$lastName = self::normalize($lastname);

        //When everything is OK
        if (strlen($firstName . ' ' . $lastName) <= $maxLength) return $firstName . ' ' . $lastName;
        //Return only first part of firstName
        if (strpos($firstName, ' ')) $firstName = substr($firstName, 0, strpos($firstName, ' '));
        if (strlen($firstName . ' ' . $lastName) <= $maxLength) return $firstName . ' ' . $lastName;
        //Return only first letter of first name
        $firstName = substr($firstName, 0, 1);
        if (strlen($firstName . ' ' . $lastName) <= $maxLength) return $firstName . ' ' . $lastName;
        //Return only first part of lastname
        if (strpos($lastName, ' ')) $lastName = substr($lastName, 0, strpos($lastName, ' '));
        if (strlen($firstName . ' ' . $lastName) <= $maxLength) return $firstName . ' ' . $lastName;

        return substr($lastName, 0, $maxLength);
	}

	public static function generateCSV($array, $folder, $name){
		//check permissions! chown -R www-data:www-data /path/to/webserver/www
		if (!file_exists($folder)){
			mkdir($folder, 0755);
		}
		//remove all old files, leave only last
		$files = glob($folder . '/*'); // get all file names
		foreach($files as $file){
			if(is_file($file)){
				unlink($file);
			}
		}
		$fp = fopen( $folder . '/' . $name . '.csv', 'w');
		$keys = [];
		foreach ($array[0] as $k => $v){
			$keys[] = $k;
		}
		array_unshift($array , $keys);

		foreach ($array as $item) {
			fputcsv($fp, $item, ';');
		}

		fclose($fp);

		return true;
	}
}
