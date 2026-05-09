﻿<?php
ini_set('display_errors','0');
ini_set('display_startup_errors','0');
error_reporting(0);
ob_start();
session_start();

function api(array $d):void{
    while(ob_get_level()>0)ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($d,JSON_UNESCAPED_UNICODE);
    exit();
}

if($_SERVER['REQUEST_METHOD']!=='POST') api(['status'=>'error','msg'=>'POST only']);

try{
    $conn=@mysqli_connect('localhost','root','','ict_system');
    if(!$conn) api(['status'=>'error','msg'=>'DB error: '.mysqli_connect_error()]);
    mysqli_set_charset($conn,'utf8mb4');

    $f=__DIR__.'/GoogleAuthenticator.php';
    if(!file_exists($f)) api(['status'=>'error','msg'=>'ບໍ່ພົບ GoogleAuthenticator.php']);
    require_once $f;
    $ga=new PHPGangsta_GoogleAuthenticator();

    $action=trim($_POST['action']??'');

    /* ── request_reset ─────────────────────────────────────────── */
    if($action==='request_reset'){
        $email =trim($_POST['email']       ??'');
        $newPw =trim($_POST['new_password']??'');
        $confPw=trim($_POST['confirm_pw']  ??'');

        if(!$email||!$newPw||!$confPw)      api(['status'=>'error','msg'=>'ກະລຸນາຕື່ມຂໍ້ມູນໃຫ້ຄົບ']);
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) api(['status'=>'error','msg'=>'Email ບໍ່ຖືກຕ້ອງ']);
        if(mb_strlen($newPw)<6)             api(['status'=>'error','msg'=>'ລະຫັດຕ້ອງຢ່າງໜ້ອຍ 6 ຕົວ']);
        if($newPw!==$confPw)                api(['status'=>'error','msg'=>'ລະຫັດທັງສອງຊ່ອງບໍ່ກົງກັນ']);

        $em=mysqli_real_escape_string($conn,$email);
        $r=mysqli_query($conn,"SELECT id,totp_secret FROM system_users WHERE email='$em' AND is_verified=1 LIMIT 1");
        if(!$r)   api(['status'=>'error','msg'=>'DB query error: '.mysqli_error($conn)]);
        $row=mysqli_fetch_assoc($r);
        if(!$row) api(['status'=>'error','msg'=>'ບໍ່ພົບ Email ນີ້ໃນລະບົບ']);
        if(empty($row['totp_secret'])) api(['status'=>'error','msg'=>'ບັນຊີນີ້ຍັງບໍ່ໄດ້ຕັ້ງຄ່າ Authenticator']);

        $_SESSION['reset_pending']=[
            'user_id'     =>(int)$row['id'],
            'email'       =>$email,
            'totp_secret' =>$row['totp_secret'],
            'new_password'=>password_hash($newPw,PASSWORD_BCRYPT),
            'expiry'      =>time()+600,
        ];
        api(['status'=>'need_totp','msg'=>'ເປີດ Authenticator App ໃສ່ລະຫັດ 6 ຕົວ']);
    }

    /* ── verify_totp ───────────────────────────────────────────── */
    if($action==='verify_totp'){
        $code=preg_replace('/\D/','',trim($_POST['code']??''));
        if(!isset($_SESSION['reset_pending'])) api(['status'=>'error','msg'=>'Session ໝົດ — ເລີ່ມໃໝ່']);

        $p=$_SESSION['reset_pending'];
        if(time()>$p['expiry']){
            unset($_SESSION['reset_pending']);
            api(['status'=>'error','msg'=>'Session ໝົດ 10 ນາທີ — ເລີ່ມໃໝ່']);
        }
        // ✅ ໃຊ້ totp_secret ຂອງ user ຄົນນີ້ (ຕົວດຽວກັບ register)
        if(!$ga->verifyCode($p['totp_secret'],$code,2))
            api(['status'=>'error','msg'=>'ລະຫັດ Authenticator ບໍ່ຖືກ ກະລຸນາລອງໃໝ່']);

        $uid=$p['user_id'];
        $pw=mysqli_real_escape_string($conn,$p['new_password']);
        if(!mysqli_query($conn,"UPDATE system_users SET password='$pw' WHERE id=$uid"))
            api(['status'=>'error','msg'=>'DB error: '.mysqli_error($conn)]);

        unset($_SESSION['reset_pending']);
        api(['status'=>'success','msg'=>'ປ່ຽນລະຫັດສຳເລັດ! Login ດ້ວຍລະຫັດໃໝ່ໄດ້ເລີຍ']);
    }

    api(['status'=>'error','msg'=>'Action ບໍ່ຮັບຮູ້']);

}catch(Throwable $e){
    api(['status'=>'error','msg'=>'Exception: '.$e->getMessage()]);
}

