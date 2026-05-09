<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status'=>'error','msg'=>'Unauthorized']); exit();
}

mysqli_report(MYSQLI_REPORT_OFF);
ini_set("display_errors",0);
error_reporting(0);
header('Content-Type: application/json');
$host="localhost";$user="root";$pass="";$db="ict_system";
$conn=mysqli_connect($host,$user,$pass,$db);
if(!$conn){echo json_encode(['status'=>'error','msg'=>'DB connect failed']);exit();}
function esc($c,$v){return mysqli_real_escape_string($c,trim($v??''));}
$full_name    =esc($conn,$_POST['username']   ??'');
$email_type   =esc($conn,$_POST['email_type'] ??'');
$status       =esc($conn,$_POST['status']     ??'actived');
$primary_email=esc($conn,$_POST['email_1']    ??'');
$password     =esc($conn,$_POST['password']   ??'');
$second_email =esc($conn,$_POST['email_2']    ??'');
$third_email  =esc($conn,$_POST['email_3']    ??'');
$department   =esc($conn,$_POST['department'] ??'');
$team         =esc($conn,$_POST['team']       ??'');
$phone        =esc($conn,$_POST['phone']      ??'');
$ins_number   =esc($conn,$_POST['ins_number'] ??'');
$halo_device  =esc($conn,$_POST['halo_id']    ??'');
$table_map=['Office 365'=>'office365_accounts','Survey 123'=>'survey123_accounts','Google account'=>'google_accounts','Trimble account'=>'trimble_accounts'];
$table=$table_map[$email_type]??'office365_accounts';
$sql="INSERT INTO `$table`(full_name,account_type,account_status,primary_email,password,second_email,third_email,department,team,phone,ins_number,halo_device_number)VALUES('$full_name','$email_type','$status','$primary_email','$password','$second_email','$third_email','$department','$team','$phone','$ins_number','$halo_device')";
if(mysqli_query($conn,$sql)){echo json_encode(['status'=>'saved']);}
else{echo json_encode(['status'=>'error','msg'=>mysqli_error($conn)]);}
mysqli_close($conn);