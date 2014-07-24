<?php

require_once dirname(__FILE__) . '/../third-party/NotORM.php';
require_once dirname(__FILE__) . '/../configs/db.php';

class uup {

    private $pdo;
    private $db;

    public function __construct() {

        $this->pdo = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST,DB_USER,DB_PASS);
        $this->db = new NotORM($this->pdo);

    }

    public function getAllQuestion() {

        $questionArray = array();
        $questions = $this->db->sorular();
        foreach ($questions as $data) {
            $question = array(
                "id"        => $data["id"],
                "icerik"    => $data["icerik"],
                "test"      => $data["test"]
            );
            $questionArray[] = $question;
        } return $questionArray;

    }

    public function getQuestionsByTestId($testId) {

        $questionArray = array();
        $questions = $this->db->sorular()->where("test",$testId);
        foreach ($questions as $data) {
            $question = array(
                "id"        => $data["id"],
                "icerik"   => $data["icerik"]
            );
            $questionArray[] = $question;
        } return $questionArray;

    }

    public function getQuestionById($id) {

        if($data = $this->db->sorular()->where("id",$id)->fetch()) {
            $question = array(
                "id"        => $data["id"],
                "icerik"    => $data["icerik"],
                "test"      => $data["test"]
            );
            return $question;
        } else {
            $error = array(
                "error"     => "NO_CONTENT"
            );
            return $error;
        }

    }

    public function getRoleTestQuestionById($id) {

        $questionArray = array();
        $questions = $this->db->rol_anketi_secenekler()->where("soru_id",$id);
        $q = $this->db->rol_anketi_sorular()->where("id",$id)->fetch();
        foreach ($questions as $data) {
            $question = array(
                "soru_id"            => $q["id"],
                "soru"          => $q["soru"],
                "secenek"       => $data["secenek"]
            );
            $questionArray[] = $question;
        } return $questionArray;

    }

    public function getProfileTestByEpisodeId($id) {

        $testArray = array();
        $test = $this->db->kisilik_profili_secenekler()->where("bolum",$id);
        foreach ($test as $data) {
            $i = array(
                "id"        => $data["id"],
                "soru_no"   => $data["soru_no"],
                "secenek"   => $data["secenek"],
                "bolum"     => $data["bolum"]
            );
            $testArray[] = $i;
        } return $testArray;

    }

    public function save($adi, $soyadi, $questions) {

        $userId = $this->saveUser($adi,$soyadi);
        foreach($questions as $i) {
            for($j=1;$j<=419;$j++) {
                if($i[$j]) {
                    if(!$this->saveReply($userId,$j,$i[$j])) {
                        return false;
                    }
                }
            }
        }

        foreach($questions as $i) {
            for($j=1;$j<=40;$j++) {
                if($i["p".$j]) {
                    if(!$this->saveProfileMark($userId,$i["p".$j])){
                        return false;
                    }
                }
            }
        }


        $resultArray = array();
        $resultArray[0]["adi"] = $adi;
        $resultArray[0]["soyadi"] = $soyadi;
        $resultArray[1] = $this->getSoulResultWithReplies($this->getUserRepliesWithEpisode($userId,1));
        $resultArray[2] = $this->getSoulResultWithReplies($this->getUserRepliesWithEpisode($userId,2));
        $resultArray[3] = $this->getCatResultWithMarks($this->getUserProfileMarksWithEpisode($userId,1));
        $resultArray[4] = $this->getCatResultWithMarks($this->getUserProfileMarksWithEpisode($userId,2));

        return $resultArray;

    }

    private function getSoulResultWithReplies($replies) {
        $soulArray = array();
        foreach($replies as $reply) {
            $result = $this->getResult($reply["soru_id"],$reply["cevap"]);
            $soul = $this->getSoul($result["ruh_halleri_id"]);
            if($soul) {
                if($hal = $this->checkSoul($soulArray,$soul)) {
                    $soulArray[$hal] = $soulArray[$hal] + 1;
                } else {
                    $soulArray[$soul["hal"]] = 0;
                }
            }
        } return $soulArray;
    }

    private function getCatResultWithMarks($marks) {
        $catArray = array();
        foreach($marks as $mark) {
            $mark = $this->getMark($mark["secenek_id"]);
            if($mark) {
                if($cat = $this->checkCat($catArray,$mark)) {
                    $catArray[$cat] = $catArray[$cat] + 1;
                } else {
                    $catArray[$mark["cikarim"]] = 0;
                }
            }
        } return $catArray;
    }

    private function checkSoul($soulArray,$soul) {

        foreach($soulArray as $key => $value) {
            if($soul["hal"]==$key) {
                return $key;
            }
        } return false;

    }

    private function checkCat($catArray, $cat) {

        foreach($catArray as $key => $value) {
            if($cat["cikarim"]==$key) {
                return $key;
            }
        } return false;

    }

    private function getUserRepliesWithEpisode($userId,$episode) {

        $resultArray = array();
        $replies = $this->getUserReplies($userId);
        foreach($replies as $data) {
            if($question = $this->db->sorular()->where("id",$data["soru_id"])->fetch()) {
                if($question["test"]==$episode) {
                    $resultArray[] = $data;
                }
            }
        } return $resultArray;

    }

    private function getUserProfileMarksWithEpisode($userId,$episode) {

        $resultArray = array();
        $marks = $this->getUserProfileMarks($userId);
        foreach($marks as $data) {
            if($mark = $this->db->kisilik_profili_secenekler()->where("id",$data["secenek_id"])->fetch()) {
                if($mark["bolum"]==$episode) {
                    $resultArray[] = $data;
                }
            }
        }
        return $resultArray;

    }

    private function getUserProfileMarks($userId) {

        return $this->db->kisilik_profili_cevaplar()->where("kullanici_id",$userId);

    }

    private function getUserReplies($userId) {

        return $this->db->cevaplar()->where("kullanici_id",$userId);

    }

    private function getResult($questionId, $reply) {

        return $this->db->cikarimlar()->where("soru_id",$questionId)->where("cevap",$reply)->fetch();

    }

    private function getMark($id) {

        return $this->db->kisilik_profili_secenekler()->where("id",$id)->fetch();

    }

    private function getSoul($soulId) {

        return $this->db->ruh_halleri()->where("id",$soulId)->fetch();

    }

    private function saveUser($adi, $soyadi) {
        if($data = $this->db->kullanicilar()->insert(array(
            "adi"       => $adi,
            "soyadi"    => $soyadi
        ))) {
            return $data['id'];
        }
    }

    private function saveReply($userId,$questionId,$reply) {
        if($data = $this->db->cevaplar()->insert(array(
            "soru_id"           => $questionId,
            "kullanici_id"      => $userId,
            "cevap"             => $reply
        ))) {
            return true;
        } return false;
    }

    private function saveProfileMark($userId, $markId) {
        if($data = $this->db->kisilik_profili_cevaplar()->insert(array(
            "kullanici_id" => $userId,
            "secenek_id"   => $markId
        ))) {
            return true;
        } return false;
    }

    public function errorNotFound() {
        $error = array(
            "error"     => "NOT_FOUND"
        );
        return $error;
    }

    public function status() {
        $status = array(
            "version"     => "1.0"
        );
        return $status;
    }

}


