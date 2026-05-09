﻿<?php
//  SESSION PROTECTION
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
header("Location: login.php"); exit();
}
mysqli_report(MYSQLI_REPORT_OFF);
ini_set("display_errors",0); error_reporting(0);

//  DATABASE CONNECTION
$host = "sql207.infinityfree.com";
$user = "if0_41843014";
$pass = "60suJN8BPgyU9SL";
$db   = "if0_41843014_ict_system";

// ສ້າງການເຊື່ອມຕໍ່
$conn = mysqli_connect("sql207.infinityfree.com", "if0_41843014", "6OsuJN8BPgyU9SL", "if0_41843014_ict_system");
if (!$conn) { die("Connection failed: ".mysqli_connect_error()); }

// Auto-create internet_records table if missing
@mysqli_query($conn,"CREATE TABLE IF NOT EXISTS `internet_records` (
`id`             INT AUTO_INCREMENT PRIMARY KEY,
`internet_local` VARCHAR(255) DEFAULT '',
`internet_type`  VARCHAR(100) DEFAULT '',
`package`        VARCHAR(255) DEFAULT '',
`price`          DECIMAL(12,2) DEFAULT 0,
`start_date`     DATE NULL,
`end_date`       DATE NULL,
`document_local` VARCHAR(255) DEFAULT '',
`document_link`  VARCHAR(500) DEFAULT '',
`remark`         TEXT,
`created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

//  DELETE: Accounts
if (isset($_GET['action']) && $_GET['action']=='delete' && isset($_GET['table'])) {
$id    = (int)($_GET['id'] ?? 0);
$table = mysqli_real_escape_string($conn,$_GET['table'] ?? '');
$allowed_tables=['office365_accounts','google_accounts','survey123_accounts','trimble_accounts'];
if (in_array($table,$allowed_tables)) {
if (mysqli_query($conn,"DELETE FROM `$table` WHERE id=$id")) {
echo "<script>window.location.href='?page=account&action=list&status=deleted';</script>"; exit;
}
}
}

//  DELETE: Devices
if (isset($_GET['page'])&&$_GET['page']=='device'&&isset($_GET['action'])&&$_GET['action']=='delete') {
$id=$_GET['id']??''; $src=$_GET['src']??'';
$id=mysqli_real_escape_string($conn,$id); $src=mysqli_real_escape_string($conn,$src);
$dev_tbl_allowed=['laptops','desktops','tablets','phones','monitors','printers','dgps','powerbanks','ups'];
if (!in_array($src,$dev_tbl_allowed)||empty($src)) {
echo "<script>alert('Table not found'); history.back();</script>"; exit();
}
if (mysqli_query($conn,"DELETE FROM `$src` WHERE id='$id'")) {
echo "<script>window.location.href='index.php?page=device&action=list&status=deleted';</script>"; exit();
} else {
echo "<script>alert('Error: ".mysqli_error($conn)."'); history.back();</script>"; exit();
}
}

//  DELETE: Card Records
if (isset($_GET['page'])&&$_GET['page']=='card_record'&&isset($_GET['action'])&&$_GET['action']=='delete') {
$id=(int)($_GET['id']??0);
$tc=@mysqli_query($conn,"SHOW TABLES LIKE 'card_records'");
if($tc&&mysqli_num_rows($tc)>0&&!empty($id)){
if(mysqli_query($conn,"DELETE FROM card_records WHERE id='$id'")){
echo "<script>window.location.href='index.php?page=card_record&action=list&status=deleted';</script>"; exit();
}
}
echo "<script>alert('Delete failed'); history.back();</script>"; exit();
}

//  DELETE: Mistakes
if (isset($_GET['page'])&&$_GET['page']=='mistake'&&isset($_GET['action'])&&$_GET['action']=='delete') {
$id=(int)($_GET['id']??0);
$tbl_chk=@mysqli_query($conn,"SHOW TABLES LIKE 'device_mistakes'");
if($tbl_chk&&mysqli_num_rows($tbl_chk)>0&&!empty($id)){
if(mysqli_query($conn,"DELETE FROM device_mistakes WHERE id='$id'")){
echo "<script>window.location.href='index.php?page=mistake&action=list&status=deleted';</script>"; exit();
}
}
echo "<script>alert('Delete failed'); history.back();</script>"; exit();
}

//  DELETE: Employees
if (isset($_GET['page'])&&$_GET['page']=='employees'&&isset($_GET['action'])&&$_GET['action']=='delete') {
$id=(int)($_GET['id']??0);
$tc=@mysqli_query($conn,"SHOW TABLES LIKE 'employees'");
if($tc&&mysqli_num_rows($tc)>0&&!empty($id)){
if(mysqli_query($conn,"DELETE FROM employees WHERE id='$id'")){
echo "<script>window.location.href='index.php?page=employees&action=list&status=deleted';</script>"; exit();
}
}
echo "<script>alert('Delete failed'); history.back();</script>"; exit();
}

//  DELETE: Internet Records
if (isset($_GET['page'])&&$_GET['page']=='internet'&&isset($_GET['action'])&&$_GET['action']=='delete') {
$id=(int)($_GET['id']??0);
if(!empty($id)){
if(mysqli_query($conn,"DELETE FROM internet_records WHERE id='$id'")){
echo "<script>window.location.href='index.php?page=internet&action=list&status=deleted';</script>"; exit();
}
}
echo "<script>alert('Delete failed'); history.back();</script>"; exit();
}

//  DELETE: Equipment Stock
if (isset($_GET['page'])&&$_GET['page']=='equipment_stock'&&isset($_GET['action'])&&$_GET['action']=='delete') {
$id=(int)($_GET['id']??0);
$tc=@mysqli_query($conn,"SHOW TABLES LIKE 'equipment_stock'");
if($tc&&mysqli_num_rows($tc)>0&&!empty($id)){
if(mysqli_query($conn,"DELETE FROM equipment_stock WHERE id='$id'")){
echo "<script>window.location.href='index.php?page=equipment_stock&action=list&status=deleted';</script>"; exit();
}
}
echo "<script>alert('Delete failed'); history.back();</script>"; exit();
}

//  DELETE: Equipment Issues
if (isset($_GET['page'])&&$_GET['page']=='equipment_stock'&&isset($_GET['action'])&&$_GET['action']=='delete_issue') {
$id=(int)($_GET['id']??0);
$tc=@mysqli_query($conn,"SHOW TABLES LIKE 'equipment_issues'");
if($tc&&mysqli_num_rows($tc)>0&&!empty($id)){
if(mysqli_query($conn,"DELETE FROM equipment_issues WHERE id='$id'")){
echo "<script>window.location.href='index.php?page=equipment_stock&action=issue_list&status=deleted';</script>"; exit();
}
}
echo "<script>alert('Delete failed'); history.back();</script>"; exit();
}

//  AJAX: Halo ID Lookup
if (isset($_GET['lookup'])&&$_GET['lookup']==='1') {
header('Content-Type: application/json; charset=utf-8');
$halo_id=mysqli_real_escape_string($conn,trim($_GET['halo_id']??''));
if(empty($halo_id)){echo json_encode(['status'=>'error','msg'=>'No Halo ID provided']);exit();}
$dev_table_map2=['Laptop'=>'laptops','Desktop'=>'desktops','Tablet'=>'tablets','Phone'=>'phones','Monitor'=>'monitors','DGPS'=>'dgps','PowerBank'=>'powerbanks'];
$found=null;
foreach($dev_table_map2 as $dtype=>$tbl){
$tr=@mysqli_query($conn,"SHOW TABLES LIKE '$tbl'");
if(!$tr||mysqli_num_rows($tr)==0)continue;
$r=@mysqli_query($conn,"SELECT * FROM `$tbl` WHERE halo_id='$halo_id' LIMIT 1");
if($r&&mysqli_num_rows($r)>0){$row=mysqli_fetch_assoc($r);$row['source_table']=$tbl;$row['device_type_label']=$dtype;$found=$row;break;}
}
echo $found?json_encode(['status'=>'found','data'=>$found]):json_encode(['status'=>'not_found','msg'=>"No device found with Halo ID: $halo_id"]);
exit();
}

//  AJAX: INS Number Lookup
if (isset($_GET['lookup_ins'])&&$_GET['lookup_ins']==='1') {
header('Content-Type: application/json; charset=utf-8');
$ins=mysqli_real_escape_string($conn,trim($_GET['ins_number']??''));
if(empty($ins)){echo json_encode(['status'=>'error','msg'=>'No INS provided']);exit();}
$found_emp=null;
$te=@mysqli_query($conn,"SHOW TABLES LIKE 'employees'");
if($te&&mysqli_num_rows($te)>0){$re=@mysqli_query($conn,"SELECT * FROM employees WHERE ins_number='$ins' LIMIT 1");if($re&&mysqli_num_rows($re)>0)$found_emp=mysqli_fetch_assoc($re);}
if(!$found_emp){
foreach(['laptops','desktops','tablets','phones','monitors','dgps','powerbanks'] as $tbl){
$tc=@mysqli_query($conn,"SHOW TABLES LIKE '$tbl'");if(!$tc||mysqli_num_rows($tc)==0)continue;
$rd=@mysqli_query($conn,"SELECT username,department,team,ins_number,location_local AS location FROM `$tbl` WHERE ins_number='$ins' LIMIT 1");
if($rd&&mysqli_num_rows($rd)>0){$found_emp=mysqli_fetch_assoc($rd);break;}
}
}
if(!$found_emp){
foreach(['office365_accounts','survey123_accounts','google_accounts','trimble_accounts'] as $tbl){
$tc=@mysqli_query($conn,"SHOW TABLES LIKE '$tbl'");if(!$tc||mysqli_num_rows($tc)==0)continue;
$ra=@mysqli_query($conn,"SELECT full_name AS username,department,team,ins_number FROM `$tbl` WHERE ins_number='$ins' LIMIT 1");
if($ra&&mysqli_num_rows($ra)>0){$found_emp=mysqli_fetch_assoc($ra);break;}
}
}
echo $found_emp?json_encode(['status'=>'found','data'=>$found_emp]):json_encode(['status'=>'not_found','msg'=>"No user found with INS: $ins"]);
exit();
}

//  AJAX: Equipment E ID Lookup
if (isset($_GET['lookup_eid'])&&$_GET['lookup_eid']==='1') {
header('Content-Type: application/json; charset=utf-8');
$e_id=mysqli_real_escape_string($conn,trim($_GET['e_id']??''));
if(empty($e_id)){echo json_encode(['status'=>'error','msg'=>'No E ID provided']);exit();}
$te=@mysqli_query($conn,"SHOW TABLES LIKE 'equipment_stock'");
if(!$te||mysqli_num_rows($te)==0){echo json_encode(['status'=>'not_found','msg'=>"No equipment found with E ID: $e_id"]);exit();}
$re=@mysqli_query($conn,"SELECT * FROM equipment_stock WHERE e_id='$e_id' LIMIT 1");
if($re&&mysqli_num_rows($re)>0){
$eq=mysqli_fetch_assoc($re);
echo json_encode(['status'=>'found','data'=>['e_id'=>$eq['e_id'],'eng_name'=>$eq['eng_name'],'lao_name'=>$eq['lao_name'],'e_new'=>$eq['e_new'],'old_stock'=>$eq['old_stock'],'all_stock'=>$eq['all_stock'],'type'=>$eq['type']]]);
}else{
echo json_encode(['status'=>'not_found','msg'=>"No equipment found with E ID: $e_id"]);
}
exit();
}

//  CONFIG
$current_page=$_GET['page']??'dashboard';
$action=$_GET['action']??'list';

function safeCount($conn,$table){
try{$r=mysqli_query($conn,"SELECT COUNT(*) c FROM `$table`");return($r)?(int)mysqli_fetch_assoc($r)['c']:0;}catch(Throwable $e){return 0;}
}

$c_sv      = safeCount($conn,'survey123_accounts');
$c_off     = safeCount($conn,'office365_accounts');
$c_google  = safeCount($conn,'google_accounts');
$c_trimble = safeCount($conn,'trimble_accounts');
$c_mistake = safeCount($conn,'device_mistakes');
$c_card    = safeCount($conn,'card_records');
$c_internet= safeCount($conn,'internet_records');

$dev_type_map=['Laptop'=>'laptops','Desktop'=>'desktops','Tablet'=>'tablets','Phone'=>'phones','Monitor'=>'monitors','DGPS'=>'dgps','PowerBank'=>'powerbanks'];
$dev_type_icons=['Laptop'=>'fa-laptop','Desktop'=>'fa-desktop','Tablet'=>'fa-tablet-alt','Phone'=>'fa-mobile-alt','Monitor'=>'fa-tv','DGPS'=>'fa-satellite-dish','PowerBank'=>'fa-battery-full'];
$dev_type_colors=['Laptop'=>['--c:#6366f1','--ibg:#eef2ff','--ic:#6366f1'],'Desktop'=>['--c:#3b82f6','--ibg:#eff6ff','--ic:#3b82f6'],'Tablet'=>['--c:#8b5cf6','--ibg:#f5f3ff','--ic:#8b5cf6'],'Phone'=>['--c:#ec4899','--ibg:#fdf2f8','--ic:#ec4899'],'Monitor'=>['--c:#0ea5e9','--ibg:#f0f9ff','--ic:#0ea5e9'],'DGPS'=>['--c:#10b981','--ibg:#f0fdf4','--ic:#10b981'],'PowerBank'=>['--c:#f59e0b','--ibg:#fffbeb','--ic:#f59e0b']];

$c_devices_total=0; $dev_counts=[];
foreach($dev_type_map as $dtype=>$tbl){$cnt=safeCount($conn,$tbl);$dev_counts[$dtype]=$cnt;$c_devices_total+=$cnt;}

$status_list=['Working','Spare','Broken','Write-off','Pending write-off'];
$status_counts=array_fill_keys($status_list,0);
$type_status_counts=[];
foreach($dev_type_map as $dtype=>$tbl){
$type_status_counts[$dtype]=array_fill_keys($status_list,0);
try{
$r=@mysqli_query($conn,"SHOW TABLES LIKE '$tbl'");if(!$r||mysqli_num_rows($r)==0)continue;
foreach($status_list as $st){$st_esc=mysqli_real_escape_string($conn,$st);$r2=@mysqli_query($conn,"SELECT COUNT(*) c FROM `$tbl` WHERE status='$st_esc'");if($r2){$c=(int)mysqli_fetch_assoc($r2)['c'];$type_status_counts[$dtype][$st]=$c;$status_counts[$st]+=$c;}}
}catch(Throwable $e){}
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>ICT Control Center | HALO Laos</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700;800;900&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--navy:#01244d;--navy2:#013874;--blue:#0057b8;--sky:#38bdf8;--accent:#06c3ff;--bg:#f0f4fb;--bg2:#e8eef8;--surface:#ffffff;--border:#dce6f5;--border2:#c8d8ee;--text:#111827;--text2:#374151;--muted:#6b7fa3;--success:#16a34a;--danger:#dc2626;--r-sm:8px;--r-md:12px;--r-lg:16px;--r-xl:22px;--r-2xl:30px;--shadow-sm:0 1px 4px rgba(1,36,77,.07);--shadow-md:0 4px 16px rgba(1,36,77,.10);--shadow-lg:0 8px 32px rgba(1,36,77,.13);}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{font-size:15px;}
body{font-family:'Inter','Noto Sans Lao',system-ui,sans-serif;background:var(--bg);color:var(--text);display:flex;height:100vh;overflow:hidden;-webkit-font-smoothing:antialiased;}
::-webkit-scrollbar{width:5px;height:5px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:#c2cfe0;border-radius:10px;}::-webkit-scrollbar-thumb:hover{background:#96b0cc;}

/* SIDEBAR */
.sidebar{width:240px;min-width:240px;background:var(--navy);display:flex;flex-direction:column;position:relative;overflow:hidden;z-index:40;box-shadow:2px 0 20px rgba(1,36,77,.18);transition:width .25s ease;}
.sidebar::before{content:'';position:absolute;top:-100px;right:-100px;width:300px;height:300px;background:radial-gradient(circle,rgba(6,195,255,.10) 0%,transparent 65%);pointer-events:none;}
.logo-wrap{padding:1.375rem 1.125rem 1rem;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:.75rem;position:relative;z-index:1;}
.logo-icon{width:36px;height:36px;flex-shrink:0;background:linear-gradient(135deg,rgba(6,195,255,.22),rgba(0,87,184,.15));border:1px solid rgba(6,195,255,.3);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:.85rem;}
.logo-text h1{font-family:'Geist Mono',monospace;font-size:.7rem;font-weight:600;color:#fff;letter-spacing:.08em;line-height:1;}
.logo-text p{font-size:.55rem;color:rgba(255,255,255,.28);letter-spacing:.14em;text-transform:uppercase;margin-top:2px;}
.sidebar nav{flex:1;overflow-y:auto;padding:.875rem .75rem;position:relative;z-index:1;}
.nav-section-label{font-size:.52rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.18);padding:.25rem .5rem;margin:.75rem 0 .35rem;}
.nav-link{display:flex;align-items:center;gap:.625rem;padding:.55rem .75rem;border-radius:var(--r-sm);color:rgba(255,255,255,.48);font-size:.78rem;font-weight:500;text-decoration:none;transition:all .15s ease;margin-bottom:1px;cursor:pointer;border:none;background:transparent;width:100%;text-align:left;justify-content:space-between;}
.nav-link:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85);}
.nav-link.active{background:linear-gradient(90deg,rgba(255,255,255,.15),rgba(255,255,255,.03));color:#ffffff;border-left:3px solid #ffffff;padding-left:calc(.75rem - 3px);font-weight:600;}
.menu-open > .nav-link{background:linear-gradient(90deg,rgba(255,255,255,.15),rgba(255,255,255,.03));color:#ffffff;border-left:3px solid #ffffff;padding-left:calc(.75rem - 3px);font-weight:600;}
.nav-icon{width:28px;height:28px;border-radius:7px;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;font-size:.72rem;flex-shrink:0;transition:all .15s;}
.nav-link.active .nav-icon{background:rgba(255,255,255,.20);color:#ffffff;}
.menu-open > .nav-link .nav-icon{background:rgba(255,255,255,.20);color:#ffffff;}
.nav-chevron{font-size:.52rem;color:rgba(255,255,255,.18);transition:transform .2s;flex-shrink:0;}
.menu-open>button .nav-chevron{transform:rotate(180deg);}
.submenu{display:none;padding:.15rem 0 .15rem .75rem;border-left:1px solid rgba(255,255,255,.07);margin-left:1rem;}
.submenu a{display:flex;align-items:center;gap:.45rem;padding:.35rem .6rem;border-radius:6px;color:rgba(255,255,255,.35);font-size:.73rem;text-decoration:none;transition:all .14s;margin-bottom:1px;}
.submenu a::before{content:'';width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.15);flex-shrink:0;transition:background .14s;}
.submenu a:hover{color:rgba(255,255,255,.82);background:rgba(255,255,255,.04);}
.submenu a:hover::before{background:var(--accent);}
.sidebar-footer{padding:.75rem;border-top:1px solid rgba(255,255,255,.05);position:relative;z-index:1;}
.ver-card{background:rgba(6,195,255,.05);border:1px solid rgba(6,195,255,.12);border-radius:var(--r-md);padding:.625rem .875rem;display:flex;align-items:center;gap:.5rem;}
.ver-card i{color:var(--accent);font-size:.9rem;}
.ver-card .vl{font-size:.55rem;color:rgba(255,255,255,.25);text-transform:uppercase;letter-spacing:.1em;}
.ver-card .vn{font-family:'Geist Mono',monospace;font-size:.68rem;color:rgba(255,255,255,.6);font-weight:600;}

/* HEADER */
header{height:60px;background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1.75rem;gap:1rem;flex-shrink:0;box-shadow:var(--shadow-sm);position:relative;z-index:20;}
.hdr-title{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);text-align:center;}
.hdr-title h2{font-family:'Geist Mono',monospace;font-size:.65rem;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--navy);}
.hdr-title span{font-size:.57rem;color:var(--muted);display:block;margin-top:1px;}
.online-dot{display:inline-flex;align-items:center;gap:.35rem;font-size:.67rem;color:var(--success);font-weight:600;}
.online-dot::before{content:'';width:6px;height:6px;border-radius:50%;background:#22c55e;animation:pulse-dot 2s infinite;}
@keyframes pulse-dot{0%,100%{box-shadow:0 0 0 3px rgba(34,197,94,.15);}50%{box-shadow:0 0 0 7px rgba(34,197,94,.04);}}
.user-pill{display:flex;align-items:center;gap:.5rem;background:var(--bg);border:1px solid var(--border);padding:.28rem .75rem .28rem .28rem;border-radius:100px;margin-left:auto;}
.avatar{width:28px;height:28px;background:linear-gradient(135deg,var(--navy2),var(--blue));border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Geist Mono',monospace;font-size:.62rem;color:#fff;font-weight:600;}
.user-pill .uname{font-size:.7rem;font-weight:600;color:var(--text);}
.user-pill .urole{font-size:.57rem;color:var(--muted);}
.btn-logout{display:inline-flex;align-items:center;gap:.35rem;padding:.32rem .75rem;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;border-radius:100px;font-family:inherit;font-size:.67rem;font-weight:700;text-decoration:none;cursor:pointer;transition:all .15s ease;white-space:nowrap;flex-shrink:0;}
.btn-logout i{font-size:.65rem;}
.btn-logout:hover{background:#dc2626;color:#fff;border-color:#dc2626;transform:translateY(-1px);box-shadow:0 3px 10px rgba(220,38,38,.22);}

/* MAIN */
main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;}
.content{flex:1;overflow-y:auto;padding:1.5rem 1.75rem;}
.page-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.375rem;gap:1rem;flex-wrap:wrap;}
.page-hdr h2{font-size:1.5rem;font-weight:800;color:var(--navy);letter-spacing:-.025em;line-height:1;}
.page-hdr p{font-size:.75rem;color:var(--muted);margin-top:.25rem;}
.actions-row{display:flex;gap:.45rem;flex-wrap:wrap;}
.btn-act{display:inline-flex;align-items:center;gap:.375rem;padding:.45rem .875rem;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r-sm);font-size:.73rem;font-weight:600;color:var(--text2);text-decoration:none;transition:all .15s;white-space:nowrap;}
.btn-act i{font-size:.68rem;color:var(--blue);}
.btn-act:hover{border-color:var(--blue);color:var(--blue);background:#f0f6ff;transform:translateY(-1px);box-shadow:0 3px 10px rgba(0,87,184,.1);}
.btn-primary{display:inline-flex;align-items:center;gap:.45rem;padding:.5rem 1.125rem;background:var(--navy);color:#fff;border:none;border-radius:var(--r-sm);font-family:inherit;font-size:.75rem;font-weight:700;cursor:pointer;text-decoration:none;transition:all .15s;}
.btn-primary:hover{background:var(--navy2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(1,36,77,.22);}
.divider{height:1px;background:var(--border);margin:1.375rem 0 1rem;}

/* FORMS */
.form-wrap{max-width:880px;margin:0 auto;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);overflow:hidden;box-shadow:var(--shadow-lg);}
.form-header{background:linear-gradient(90deg,var(--navy),var(--navy2));padding:1.25rem 1.5rem;display:flex;align-items:center;gap:.875rem;color:#fff;}
.form-header-icon{width:42px;height:42px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.15);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.form-header h3{font-size:.9rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;}
.form-header p{font-size:.68rem;color:rgba(255,255,255,.5);margin-top:2px;}
.form-body{padding:1.625rem 1.5rem;}
.form-section{margin-bottom:1.5rem;}
.form-section-hdr{display:flex;align-items:center;gap:.625rem;padding-bottom:.7rem;border-bottom:1px solid var(--border);margin-bottom:1rem;}
.step-badge{width:26px;height:26px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:800;flex-shrink:0;}
.form-section-hdr p{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--navy);}
.form-grid{display:grid;gap:.8rem;}
.g2{grid-template-columns:1fr 1fr;}
.g3{grid-template-columns:1fr 1fr 1fr;}
.g4{grid-template-columns:1fr 1fr 1fr 1fr;}
.g-span2{grid-column:span 2;}
.g-span3{grid-column:span 3;}
label.field-lbl{display:block;font-size:.7rem;font-weight:600;color:var(--text2);margin-bottom:.35rem;}
label.field-lbl span{color:var(--danger);}
.field-wrap{position:relative;}
.field-wrap i.fi{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.78rem;pointer-events:none;}
.field-wrap.ta-icon i.fi{top:.95rem;transform:none;}
input.field,select.field,textarea.field{width:100%;padding:.65rem 1rem .65rem 2.5rem;background:#f8fafd;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:inherit;font-size:.78rem;color:var(--text);outline:none;transition:all .15s;appearance:none;}
input.field:focus,select.field:focus,textarea.field:focus{border-color:var(--blue);background:#fff;box-shadow:0 0 0 3px rgba(0,87,184,.07);}
input.field:hover,select.field:hover{border-color:var(--border2);}
.select-arrow{position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.52rem;pointer-events:none;}
.cred-box{background:var(--bg);padding:.875rem;border-radius:var(--r-md);border:1px solid var(--border);}
.btn-submit{width:100%;padding:.9rem;background:linear-gradient(90deg,var(--navy),var(--navy2));color:#fff;border:none;border-radius:var(--r-md);font-family:inherit;font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;cursor:pointer;transition:all .18s;display:flex;align-items:center;justify-content:center;gap:.6rem;margin-top:1.5rem;box-shadow:0 2px 12px rgba(1,36,77,.15);}
.btn-submit:hover{filter:brightness(1.08);transform:translateY(-2px);box-shadow:0 6px 20px rgba(1,36,77,.22);}

/* TABLES */
.tbl-wrap{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);overflow:hidden;box-shadow:var(--shadow-md);}
.tbl-scroll{overflow:auto;max-height:calc(100vh - 260px);}
.tbl-scroll thead th{position:sticky;top:0;z-index:20;background:#f4f8fd;box-shadow:0 1px 0 var(--border);}
.filter-bar{padding:.8rem 1.25rem;background:var(--bg);border-bottom:1px solid var(--border);display:flex;flex-wrap:wrap;gap:.5rem;align-items:center;}
.filter-lbl{font-size:.58rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);}
.filter-select,.filter-input{padding:.4rem .7rem;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:inherit;font-size:.73rem;color:var(--text);outline:none;transition:border-color .15s;appearance:none;}
.filter-select:focus,.filter-input:focus{border-color:var(--blue);}
.filter-input{padding:.4rem .7rem .4rem 1.9rem;}
.search-wrap{position:relative;}
.search-wrap i{position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.68rem;}
.btn-filter{padding:.4rem .875rem;background:var(--navy);color:#fff;border:none;border-radius:var(--r-sm);font-family:inherit;font-size:.7rem;font-weight:700;cursor:pointer;transition:all .15s;}
.btn-filter:hover{background:var(--navy2);}
.btn-clear{font-size:.7rem;color:var(--danger);font-weight:700;text-decoration:none;padding:.4rem .5rem;border-radius:var(--r-sm);transition:background .14s;}
.btn-clear:hover{background:#fee2e2;}
table{width:100%;border-collapse:separate;border-spacing:0;}
thead tr{background:#f4f8fd;}
thead th{padding:.75rem 1rem;font-size:.72rem;font-weight:600;letter-spacing:.01em;color:var(--muted);white-space:nowrap;border-bottom:1.5px solid var(--border);text-align:left;user-select:none;position:sticky;top:0;z-index:20;background:#f4f8fd;}
thead th:first-child{padding-left:1.375rem;}
thead th:last-child{padding-right:1.375rem;text-align:center;}
thead th.th-center{text-align:center;}
thead th.th-blue{background:#edf5ff;color:#1d4ed8;}
thead th.th-red{background:#fff1f1;color:#dc2626;}
thead th.th-green{background:#f0fdf4;color:#15803d;}
thead th.th-teal{background:#f0fdfa;color:#0f766e;}
tbody tr:nth-child(even){background:#fafbfd;}
tbody tr:nth-child(odd){background:var(--surface);}
tbody tr{transition:background .1s;}
tbody tr:hover{background:#eef5ff !important;}
tbody tr:hover td.sticky-l200{background:#ddeeff !important;}
tbody tr:hover td.sticky-l0{background:#ddeeff !important;}
tbody tr:last-child td{border-bottom:none;}
tbody td{padding:.75rem 1rem;font-size:.8rem;color:var(--text2);border-bottom:1px solid #eef3fb;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;vertical-align:middle;text-align:left;line-height:1.4;}
tbody td:first-child{padding-left:1.375rem;}
tbody td:last-child{padding-right:1.375rem;text-align:center;}
td.td-center{text-align:center;}td.td-mono{font-family:'Geist Mono',monospace;font-size:.72rem;color:var(--muted);letter-spacing:.02em;}
td.td-bold{font-weight:600;color:var(--navy);}td.td-blue{color:var(--blue);font-weight:500;}
td.td-red{color:#dc2626;font-weight:500;}td.td-green{color:#15803d;font-weight:500;}
td.td-teal{color:#0f766e;font-weight:500;}
.sticky-l0{position:sticky;left:0;z-index:10;background:inherit;}
.sticky-l200{position:sticky;left:0;z-index:10;background:inherit;border-right:2px solid var(--border);box-shadow:3px 0 8px rgba(1,36,77,.05);}
.sticky-r{position:sticky;right:0;z-index:10;background:inherit;box-shadow:-4px 0 12px rgba(1,36,77,.06);}
.status-pill{display:inline-flex;align-items:center;gap:.25rem;padding:.2rem .6rem;border-radius:100px;font-size:.58rem;font-weight:700;letter-spacing:.04em;white-space:nowrap;}
.pill-green{background:#dcfce7;color:#15803d;}.pill-red{background:#fee2e2;color:#b91c1c;}
.pill-blue{background:#dbeafe;color:#1e40af;}.pill-orange{background:#ffedd5;color:#c2410c;}
.pill-gray{background:#f1f5f9;color:#475569;}.pill-purple{background:#f5f3ff;color:#6d28d9;}
.pill-teal{background:#ccfbf1;color:#0f766e;}.pill-yellow{background:#fef9c3;color:#854d0e;}
/* Expiring = orange override */
.pill-expiring{background:#ffedd5;color:#c2410c;border:1px solid #fed7aa;font-weight:800;}
@keyframes spin-slow{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
.action-btns{display:flex;align-items:center;justify-content:center;gap:.35rem;}
.action-btn{width:28px;height:28px;border-radius:7px;border:none;display:flex;align-items:center;justify-content:center;font-size:.72rem;cursor:pointer;transition:all .14s;text-decoration:none;}
.action-btn.edit{background:#e8f1ff;color:var(--blue);}.action-btn.del{background:#fef0f0;color:#dc2626;}
.action-btn.edit:hover{background:var(--blue);color:#fff;transform:scale(1.08);}
.action-btn.del:hover{background:#dc2626;color:#fff;transform:scale(1.08);}
.empty-row td{padding:3rem;text-align:center;color:var(--muted);}
.empty-row i{font-size:2.25rem;display:block;margin-bottom:.75rem;opacity:.25;}
.placeholder-card{background:var(--surface);border:1.5px dashed var(--border);border-radius:var(--r-xl);padding:3rem;text-align:center;color:var(--muted);}
.placeholder-card i{font-size:2.75rem;display:block;margin-bottom:.875rem;opacity:.22;}

/* DASHBOARD STYLES */
.db-section{margin-bottom:1.375rem;}
.db-sec-label{display:flex;align-items:center;gap:.5rem;font-size:.6rem;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--muted);margin-bottom:.75rem;padding-left:.125rem;}
.db-sec-label i{font-size:.75rem;color:var(--blue);}
.db-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:.875rem;}
.db-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:.875rem;}
.db-grid-7{display:grid;grid-template-columns:repeat(7,1fr);gap:.75rem;}
.db-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:1.125rem 1.25rem;display:flex;align-items:center;gap:1rem;text-decoration:none;color:var(--text);transition:all .18s ease;position:relative;overflow:hidden;cursor:pointer;}
.db-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--dc,var(--blue));transform:scaleX(0);transform-origin:left;transition:transform .22s ease;}
.db-card:hover{box-shadow:0 6px 24px rgba(1,36,77,.11);transform:translateY(-2px);border-color:rgba(0,87,184,.14);}
.db-card:hover::before{transform:scaleX(1);}
.db-icon{width:48px;height:48px;flex-shrink:0;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;background:var(--dibg,#eef2fb);color:var(--dic,var(--blue));transition:all .18s;}
.db-card:hover .db-icon{background:var(--dic,var(--blue));color:#fff;transform:scale(1.06);}
.db-lbl{font-size:.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:.2rem;}
.db-val{font-size:1.875rem;font-weight:900;color:var(--navy);line-height:1;font-feature-settings:'tnum';}

.status-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:.75rem;margin-bottom:1.375rem;}
.status-col-wrap{display:flex;flex-direction:column;gap:.4rem;}
.status-type-hdr{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.7rem .875rem;background:var(--surface);border:1.5px solid var(--border);border-radius:12px;font-size:.78rem;font-weight:700;color:var(--navy);border-left:3px solid var(--type-c,var(--blue));transition:all .16s ease;}
.status-type-hdr:hover{box-shadow:0 3px 12px rgba(1,36,77,.09);transform:translateY(-1px);}
.st-cell{display:flex;align-items:center;justify-content:space-between;padding:.55rem .875rem;background:var(--surface);border:1px solid var(--border);border-radius:9px;text-decoration:none;color:var(--text);transition:all .16s ease;position:relative;overflow:hidden;}
.st-cell::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--sc,var(--blue));transform:scaleX(0);transform-origin:left;transition:transform .18s;}
.st-cell:hover{box-shadow:0 3px 12px rgba(1,36,77,.1);transform:translateY(-1px);}
.st-cell:hover::before{transform:scaleX(1);}
.st-cell-left{display:flex;align-items:center;gap:.4rem;}
.st-cell-ico{width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.65rem;flex-shrink:0;}
.st-cell-name{font-size:.7rem;font-weight:400;letter-spacing:.01em;text-transform:capitalize;}
.st-cell-num{font-size:1.2rem;font-weight:800;color:var(--navy);font-feature-settings:'tnum';}
.st-working{--sc:#16a34a;}.st-spare{--sc:#2563eb;}.st-broken{--sc:#dc2626;}.st-writeoff{--sc:#7c3aed;}.st-pending{--sc:#d97706;}
.st-working .st-cell-ico{background:#dcfce7;color:#15803d;}.st-spare .st-cell-ico{background:#dbeafe;color:#1e40af;}
.st-broken .st-cell-ico{background:#fee2e2;color:#b91c1c;}.st-writeoff .st-cell-ico{background:#f5f3ff;color:#7c3aed;}
.st-pending .st-cell-ico{background:#fef3c7;color:#b45309;}
.st-working .st-cell-name{color:#15803d;}.st-spare .st-cell-name{color:#1e40af;}
.st-broken .st-cell-name{color:#b91c1c;}.st-writeoff .st-cell-name{color:#7c3aed;}.st-pending .st-cell-name{color:#b45309;}
.bottom-grid{display:grid;grid-template-columns:1fr 320px;gap:1rem;margin-top:1.125rem;}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);padding:1.375rem;}
.card-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;}
.card-title{font-size:.82rem;font-weight:700;color:var(--navy);display:flex;align-items:center;gap:.45rem;}
.badge-sm{font-size:.52rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:var(--bg);border:1px solid var(--border);padding:.18rem .55rem;border-radius:100px;}
.guide-grid{display:grid;grid-template-columns:1fr 1fr;gap:.6rem;}
.guide-item{display:flex;align-items:flex-start;gap:.6rem;padding:.75rem;background:var(--bg);border-radius:var(--r-sm);border:1px solid transparent;transition:all .15s;}
.guide-item:hover{background:#fff;border-color:var(--border);box-shadow:var(--shadow-sm);}
.guide-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.78rem;flex-shrink:0;}
.guide-item h5{font-size:.73rem;font-weight:700;color:var(--navy);margin-bottom:.1rem;}
.guide-item p{font-size:.66rem;color:var(--muted);line-height:1.5;}

/* Internet Dashboard Table */
.inet-table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-xl);overflow:hidden;box-shadow:var(--shadow-md);margin-bottom:1.375rem;}
.inet-table-hdr{padding:1rem 1.375rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.inet-table-hdr .card-title{font-size:.85rem;}
.inet-status-active{background:#ccfbf1;color:#0f766e;}
.inet-status-expired{background:#fee2e2;color:#b91c1c;}
.inet-status-soon{background:#fef9c3;color:#854d0e;}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
<div class="logo-wrap">
<div class="logo-icon"><i class="fas fa-cube"></i></div>
<div class="logo-text"><h1>ICT SYSTEM</h1><p>Halo Trust Laos</p></div>
</div>
<nav>
<p class="nav-section-label">Main Menu</p>
<a href="?page=dashboard" class="nav-link <?= $current_page=='dashboard'?'active':'' ?>">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-chart-pie"></i></span>Dashboard</span>
</a>

<div class="menu-group <?= $current_page=='account'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('accountSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-user-shield"></i></span>Account</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="accountSub" class="submenu" style="<?= $current_page=='account'?'display:block':'' ?>">
<a href="?page=account&action=form">New Account</a>
<a href="?page=account&action=list">All Account</a>
</div>
</div>

<div class="menu-group <?= $current_page=='device'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('deviceSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-laptop-medical"></i></span>ICT Device</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="deviceSub" class="submenu" style="<?= $current_page=='device'?'display:block':'' ?>">
<a href="?page=device&action=form">New Device</a>
<a href="?page=device&action=transfer">Transfer Device</a>
<a href="?page=device&action=list">All Device</a>
</div>
</div>

<div class="menu-group <?= $current_page=='mistake'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('mistakeSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span>Devices Mistake</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="mistakeSub" class="submenu" style="<?= $current_page=='mistake'?'display:block':'' ?>">
<a href="?page=mistake&action=form">New Mistake</a>
<a href="?page=mistake&action=list">Mistake List</a>
</div>
</div>

<div class="menu-group <?= $current_page=='card_record'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('cardSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-address-card"></i></span>Card Record</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="cardSub" class="submenu" style="<?= $current_page=='card_record'?'display:block':'' ?>">
<a href="?page=card_record&action=form">New Record</a>
<a href="?page=card_record&action=list">View Record</a>
</div>
</div>

<div class="menu-group <?= $current_page=='equipment_stock'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('equipStockSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-boxes-stacked"></i></span>ICT Equipment Stock</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="equipStockSub" class="submenu" style="<?= $current_page=='equipment_stock'?'display:block':'' ?>">
<a href="?page=equipment_stock&action=form">New Equipment</a>
<a href="?page=equipment_stock&action=list">All Equipment</a>
<a href="?page=equipment_stock&action=issue_form">Issue Equipment</a>
<a href="?page=equipment_stock&action=issue_list">Issue List</a>
</div>
</div>

<div class="menu-group <?= $current_page=='employees'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('employeesSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-users"></i></span>Employees</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="employeesSub" class="submenu" style="<?= $current_page=='employees'?'display:block':'' ?>">
<a href="?page=employees&action=form">New User</a>
<a href="?page=employees&action=list">User List</a>
</div>
</div>

<!-- -->
<div class="menu-group <?= $current_page=='internet'?'menu-open':'' ?>">
<button class="nav-link" onclick="toggleSub('internetSub',this)">
<span style="display:flex;align-items:center;gap:.625rem;"><span class="nav-icon"><i class="fas fa-wifi"></i></span>Internet</span>
<i class="fas fa-chevron-down nav-chevron"></i>
</button>
<div id="internetSub" class="submenu" style="<?= $current_page=='internet'?'display:block':'' ?>">
<a href="?page=internet&action=form">Add New</a>
<a href="?page=internet&action=list">All Internet</a>
</div>
</div>

</nav>
<div class="sidebar-footer">
<div class="ver-card">
<i class="fas fa-shield-alt"></i>
<div><div class="vl">System Version</div><div class="vn">V 2.8.0 Pro</div></div>
</div>
</div>
</aside>

<!-- MAIN -->
<main>
<header>
<div class="online-dot">Online</div>
<div class="hdr-title">
<h1 style="font-weight: bold;">Dashboard Panel</h1>
<span>ICT Management HALO Trust Laos</span>
</div>
<div class="user-pill" style="margin-left:auto;">
<div class="avatar">A</div>
<div>
<div class="uname">Admin ICT</div>
<div class="urole">Halo Trust Laos</div>
</div>
</div>
<a href="logout.php" class="btn-logout" onclick="return confirmLogout()"><i class="fas fa-right-from-bracket"></i> Logout</a>
</header>

<div class="content">
<?php if($current_page=='dashboard'): ?>
    
    <div class="page-hdr" style="margin-bottom:1.25rem;">
    <div><h2>System Dashboard</h2><p>ICT Management Overview Halo Trust Laos</p></div>
    <div class="actions-row">
    <?php $qb=[
    ['page'=>'account',    'action'=>'form',    'icon'=>'fa-user-plus',         'label'=>'New Account'],
    ['page'=>'device',     'action'=>'form',    'icon'=>'fa-laptop-medical',    'label'=>'New Device'],
    ['page'=>'device',     'action'=>'transfer','icon'=>'fa-exchange-alt',      'label'=>'Transfer Device'],
    ['page'=>'card_record','action'=>'form',       'icon'=>'fa-address-card',      'label'=>'New Card'],
    ['page'=>'equipment_stock','action'=>'issue_form','icon'=>'fa-share-square',     'label'=>'Issue Equipment'],
    ['page'=>'mistake',    'action'=>'form',    'icon'=>'fa-exclamation-circle','label'=>'New Mistake'],
    ['page'=>'internet',   'action'=>'form',    'icon'=>'fa-wifi',              'label'=>'New Internet'],
    ];
    foreach($qb as $b): ?>
        <a href="?page=<?=$b['page']?>&action=<?=$b['action']?>" class="btn-act"><i class="fas <?=$b['icon']?>"></i><?=$b['label']?></a>
        <?php endforeach; ?>
        </div>
        </div>
        
        <!-- Account Management -->
        <div class="db-section">
        <div class="db-sec-label"><i class="fas fa-users-cog"></i> Account Management</div>
        <div class="db-grid-4">
        <a href="?page=account&action=list&filter_type=Survey+123" class="db-card" style="--dc:#3b82f6;--dibg:#eff6ff;--dic:#3b82f6;"><div class="db-icon"><i class="fas fa-poll-h"></i></div><div><div class="db-lbl">Survey 123</div><div class="db-val"><?=$c_sv?></div></div></a>
        <a href="?page=account&action=list&filter_type=Office+365" class="db-card" style="--dc:#f97316;--dibg:#fff7ed;--dic:#f97316;"><div class="db-icon"><i class="fab fa-microsoft"></i></div><div><div class="db-lbl">Office 365</div><div class="db-val"><?=$c_off?></div></div></a>
        <a href="?page=account&action=list&filter_type=Google+account" class="db-card" style="--dc:#ef4444;--dibg:#fef2f2;--dic:#ef4444;"><div class="db-icon"><i class="fab fa-google"></i></div><div><div class="db-lbl">Google Account</div><div class="db-val"><?=$c_google?></div></div></a>
        <a href="?page=account&action=list&filter_type=Trimble+account" class="db-card" style="--dc:#6366f1;--dibg:#eef2ff;--dic:#6366f1;"><div class="db-icon"><i class="fas fa-globe"></i></div><div><div class="db-lbl">Trimble Account</div><div class="db-val"><?=$c_trimble?></div></div></a>
        </div>
        </div>
        
        <!-- Device Status Overview -->
        <div class="db-section">
        <div class="db-sec-label"><i class="fas fa-circle-check"></i> Device Status Overview</div>
        <div class="status-grid">
        <?php foreach($dev_type_map as $dtype=>$tbl):
            $icon=$dev_type_icons[$dtype]; $colors=$dev_type_colors[$dtype];
            $dic=str_replace('--ic:','',$colors[2]);
            $cnt_w=$type_status_counts[$dtype]['Working']??0; $cnt_s=$type_status_counts[$dtype]['Spare']??0;
            $cnt_b=$type_status_counts[$dtype]['Broken']??0; $cnt_wo=$type_status_counts[$dtype]['Write-off']??0;
            $cnt_pw=$type_status_counts[$dtype]['Pending write-off']??0;
            ?>
            <div class="status-col-wrap">
            <a href="?page=device&action=list&type=<?=urlencode($dtype)?>" class="status-type-hdr" style="--type-c:<?=$dic?>;text-decoration:none;">
            <div style="display:flex;align-items:center;gap:.45rem;flex:1;"><i class="fas <?=$icon?>" style="color:<?=$dic?>;font-size:.95rem;"></i><span style="font-size:.78rem;font-weight:700;color:var(--navy);"><?=$dtype?></span></div>
            <span style="font-size:1.25rem;font-weight:900;color:<?=$dic?>;font-feature-settings:'tnum';"><?=$cnt_w+$cnt_s+$cnt_b+$cnt_wo+$cnt_pw?></span>
            </a>
            <a href="?page=device&action=list&type=<?=urlencode($dtype)?>&status=Working" class="st-cell st-working"><div class="st-cell-left"><div class="st-cell-ico"><i class="fas fa-check-circle"></i></div><span class="st-cell-name">Working</span></div><div class="st-cell-num"><?=$cnt_w?></div></a>
            <a href="?page=device&action=list&type=<?=urlencode($dtype)?>&status=Spare" class="st-cell st-spare"><div class="st-cell-left"><div class="st-cell-ico"><i class="fas fa-box"></i></div><span class="st-cell-name">Spare</span></div><div class="st-cell-num"><?=$cnt_s?></div></a>
            <a href="?page=device&action=list&type=<?=urlencode($dtype)?>&status=Broken" class="st-cell st-broken"><div class="st-cell-left"><div class="st-cell-ico"><i class="fas fa-tools"></i></div><span class="st-cell-name">Broken</span></div><div class="st-cell-num"><?=$cnt_b?></div></a>
            <a href="?page=device&action=list&type=<?=urlencode($dtype)?>&status=Pending+write-off" class="st-cell st-pending"><div class="st-cell-left"><div class="st-cell-ico"><i class="fas fa-clock"></i></div><span class="st-cell-name">Pending</span></div><div class="st-cell-num"><?=$cnt_pw?></div></a>
            <a href="?page=device&action=list&type=<?=urlencode($dtype)?>&status=Write-off" class="st-cell st-writeoff"><div class="st-cell-left"><div class="st-cell-ico"><i class="fas fa-ban"></i></div><span class="st-cell-name">Write-off</span></div><div class="st-cell-num"><?=$cnt_wo?></div></a>
            </div>
            <?php endforeach; ?>
            </div>
            </div>
            
            <!-- -->
            <?php
            $inet_all = @mysqli_query($conn,"SELECT * FROM internet_records ORDER BY end_date ASC");
            $inet_rows = [];
            if($inet_all){ while($ir=mysqli_fetch_assoc($inet_all)) $inet_rows[]=$ir; }
            $today = date('Y-m-d');
            $inet_active=0; $inet_expiring=0; $inet_expired=0;
            foreach($inet_rows as $ir){
            if(empty($ir['end_date'])){ $inet_active++; continue; }
            $diff = (strtotime($ir['end_date'])-strtotime($today))/86400;
            if($diff<0) $inet_expired++;
            elseif($diff<=30) $inet_expiring++;
            else $inet_active++;
            }
            ?>
            <div class="db-section">
            <div class="db-sec-label"><i class="fas fa-wifi"></i> Internet Overview</div>
            <!-- Summary Cards -->
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.875rem;margin-bottom:.875rem;">
            <a href="?page=internet&action=list" class="db-card" style="--dc:#0f766e;--dibg:#f0fdfa;--dic:#0f766e;">
            <div class="db-icon"><i class="fas fa-wifi"></i></div>
            <div><div class="db-lbl">Total Internet</div><div class="db-val"><?=$c_internet?></div></div>
            </a>
            <a href="?page=internet&action=list&filter_status=active" class="db-card" style="--dc:#16a34a;--dibg:#dcfce7;--dic:#16a34a;">
            <div class="db-icon"><i class="fas fa-circle-check"></i></div>
            <div><div class="db-lbl">Active</div><div class="db-val"><?=$inet_active?></div></div>
            </a>
            <a href="?page=internet&action=list&filter_status=expiring" class="db-card" style="--dc:#d97706;--dibg:#ffedd5;--dic:#c2410c;border-color:#fed7aa;">
            <div class="db-icon" style="background:#ffedd5;color:#c2410c;"><i class="fas fa-clock"></i></div>
            <div style="flex:1;">
            <div class="db-lbl">Expiring (&le;30d)</div>
            <div class="db-val" style="color:#c2410c;"><?=$inet_expiring?></div>
            <?php if($inet_expiring>0):
                // Show up to 3 expiring contracts with days
                $shown=0;
                foreach($inet_rows as $xr){
                if($shown>=3) break;
                $xend=$xr['end_date']??'';
                if(empty($xend)) continue;
                $xdiff=round((strtotime($xend)-strtotime($today_db??date('Y-m-d')))/86400);
                if($xdiff>=0&&$xdiff<=30){
                echo '<div style="font-size:.58rem;color:#c2410c;font-weight:700;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">'.htmlspecialchars(mb_substr($xr['internet_local']??'',0,18)).'â€¦ <span style="background:#c2410c;color:#fff;border-radius:4px;padding:0 4px;">'.$xdiff.'d</span></div>';
                $shown++;
                }
                }
            endif; ?>
            </div>
            </a>
            <a href="?page=internet&action=list&filter_status=expired" class="db-card" style="--dc:#dc2626;--dibg:#fee2e2;--dic:#dc2626;">
            <div class="db-icon"><i class="fas fa-circle-xmark"></i></div>
            <div><div class="db-lbl">Expired</div><div class="db-val"><?=$inet_expired?></div></div>
            </a>
            </div>
            <!-- Internet Table -->
            <?php if(!empty($inet_rows)): ?>
                <div class="inet-table-wrap">
                <div class="inet-table-hdr">
                <div class="card-title"><i class="fas fa-list" style="color:var(--blue);"></i> Internet Records</div>
                <a href="?page=internet&action=list" style="font-size:.7rem;color:var(--blue);text-decoration:none;font-weight:600;">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="tbl-scroll">
                <table style="min-width:900px;">
                <thead><tr>
                <th style="width:50px;">#</th>
                <th style="width:180px;">Internet Local</th>
                <th style="width:130px;">Type</th>
                <th style="width:170px;">Package</th>
                <th style="width:130px;text-align:right;">Price (LAK)</th>
                <th style="width:120px;text-align:center;">Start Date</th>
                <th style="width:120px;text-align:center;">End Date</th>
                <th style="width:100px;text-align:center;">Days Left</th>
                <th style="width:110px;text-align:center;">Status</th>
                <th style="width:220px;">Document Link</th>
                </tr></thead>
                <tbody>
                <?php $inet_no=1; foreach(array_slice($inet_rows,0,10) as $ir):
                    $end=$ir['end_date'];
                    if(empty($end)){$status_lbl='Active';$pill_cls='pill-teal';}
                    else{$diff_days_db=round((strtotime($end)-strtotime($today))/86400);if($diff_days_db<0){$status_lbl='Expired';$pill_cls='pill-red';}elseif($diff_days_db<=30){$status_lbl='Expiring';$pill_cls='pill-expiring';$diff=$diff_days_db;}else{$status_lbl='Active';$pill_cls='pill-teal';}}
                    ?>
                    <tr>
                    <td style="color:var(--muted);font-weight:700;"><?=$inet_no++?></td>
                    <td class="td-bold"><?=htmlspecialchars($ir['internet_local']??'')?></td>
                    <td><?=htmlspecialchars($ir['internet_type']??'')?></td>
                    <td><?=htmlspecialchars($ir['package']??'')?></td>
                    <td style="text-align:right;font-family:'Geist Mono',monospace;font-size:.78rem;font-weight:600;color:var(--navy);"><?=number_format((float)($ir['price']??0))?></td>
                    <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($ir['start_date']??'')?></td>
                    <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($end??'')?></td>
                    <td style="text-align:center;font-size:.75rem;font-weight:600;">
                    <?php 
                    if(empty($end)) {
                    echo '<span style="color:var(--muted);">—</span>';
                    } else {
                    $diff_days = round((strtotime($end) - strtotime($today)) / 86400);
                    if($diff_days < 0) {
                    echo '<span style="color:#dc2626;font-weight:700;">Expired</span>';
                    } elseif($diff_days == 0) {
                    echo '<span style="color:#f59e0b;font-weight:700;">Today</span>';
                    } elseif($diff_days <= 30) {
                    echo '<span style="color:#f59e0b;font-weight:700;">' . $diff_days . ' days</span>';
                    } else {
                    echo '<span style="color:#059669;font-weight:600;">' . $diff_days . ' days</span>';
                    }
                    }
                    ?>
                    </td>
                    <td style="text-align:center;">
                    <?php if($status_lbl==='Expiring' && isset($diff) && $diff>=0): ?>
                        <span class="status-pill pill-expiring" style="display:inline-flex;flex-direction:column;align-items:center;padding:.25rem .6rem;gap:1px;">
                        <span style="font-size:.55rem;letter-spacing:.06em;text-transform:uppercase;">Expiring</span>
                        <span style="font-size:.95rem;font-weight:900;line-height:1;" id="cntdwn-<?=$inet_no?>"><span class="db-countdown" data-days="<?=(int)$diff?>"></span></span>
                        </span>
                        <?php else: ?>
                            <span class="status-pill <?=$pill_cls?>"><?=$status_lbl?></span>
                            <?php endif; ?>
                            </td>
                            <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;">
                            <?php if(!empty($ir['document_link'])): ?>
                                <a href="<?=htmlspecialchars($ir['document_link'])?>" target="_blank" style="color:var(--blue);font-size:.73rem;text-decoration:none;" title="<?=htmlspecialchars($ir['document_link'])?>"><i class="fas fa-external-link-alt"></i> <?=htmlspecialchars(substr($ir['document_link'],0,35)).(strlen($ir['document_link'])>35?'...':'')?></a>
                                <?php else: ?><span style="color:var(--muted);font-size:.72rem;">â€”</span><?php endif; ?>
                                    </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($inet_rows)): ?>
                                        <tr class="empty-row"><td colspan="9"><i class="fas fa-wifi"></i>No internet records yet</td></tr>
                                        <?php endif; ?>
                                        </tbody>
                                        </table>
                                        </div>
                                        </div>
                                        <!-- Countdown JS for dashboard -->
                                        <script>
                                        (function(){
                                        document.querySelectorAll('.db-countdown').forEach(function(el){
                                        var days = parseInt(el.getAttribute('data-days'));
                                        function tick(){
                                        var d = Math.floor(days);
                                        el.textContent = d + 'd';
                                        }
                                        tick();
                                        // Animate: pulse every second to simulate live countdown feel
                                        setInterval(function(){ el.closest('.status-pill') && (el.closest('.status-pill').style.opacity = el.closest('.status-pill').style.opacity==='0.7'?'1':'0.7'); }, 1000);
                                        setTimeout(function(){ if(el.closest('.status-pill')) el.closest('.status-pill').style.opacity='1'; }, 500);
                                        });
                                        })();
                                        </script>
                                        <?php else: ?>
                                            <div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:var(--r-xl);padding:2rem;text-align:center;color:var(--muted);">
                                            <i class="fas fa-wifi" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.2;"></i>
                                            <p style="font-size:.75rem;">No internet records yet. <a href="?page=internet&action=form" style="color:var(--blue);font-weight:600;">Add one now</a></p>
                                            </div>
                                            <?php endif; ?>
                                            </div>
                                            
                                            
                                            
                                            <?php
                                            // ====================================================
                                            //  ACCOUNT FORM 
                                            // ====================================================
                                            elseif($current_page=='account'&&$action=='form'):
                                                $edit_data=null;$is_edit=false;
                                                if(isset($_GET['id'])&&isset($_GET['table'])){
                                                $id=(int)($_GET['id']??0);
                                                $table=$_GET['table']??'';
                                                $allowed=['office365_accounts','google_accounts','survey123_accounts','trimble_accounts'];
                                                if(in_array($table,$allowed)){
                                                $table_escaped=mysqli_real_escape_string($conn,$table);
                                                $res=mysqli_query($conn,"SELECT * FROM `$table_escaped` WHERE id=$id");
                                                if($res){
                                                $edit_data=mysqli_fetch_assoc($res);
                                                $is_edit=true;
                                                }
                                                }
                                                }
                                                ?>
                                                <div class="form-wrap">
                                                <div class="form-header">
                                                <div class="form-header-icon"><i class="fas <?=$is_edit?'fa-edit':'fa-user-plus'?>"></i></div>
                                                <div>
                                                <h3><?=$is_edit?'Edit Account':'New Account'?></h3>
                                                <p><?=$is_edit?'Update existing user information':'Register a new system or GIS user account'?></p>
                                                </div>
                                                </div>
                                                <div class="form-body">
                                                <form id="accountForm" action="<?=$is_edit?'update_account.php':'save_account.php'?>" method="POST">
                                                <?php if($is_edit): ?>
                                                    <input type="hidden" name="update_id" value="<?=$id?>">
                                                    <input type="hidden" name="update_table" value="<?=$table?>">
                                                    <?php endif; ?>
                                                    
                                                    <!-- ======== SECTION 1 : User Identity & Account Type ======== -->
                                                    <div class="form-section">
                                                    <div class="form-section-hdr">
                                                    <div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div>
                                                    <p>User Identity &amp; Account Type</p>
                                                    </div>
                                                    <div class="form-grid g3">
                                                    <div>
                                                    <label class="field-lbl">Username / Full Name <span>*</span></label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-user-circle fi"></i>
                                                    <input type="text" name="username" class="field" placeholder="Enter Full Name"
                                                    value="<?=$edit_data['full_name']??''?>" required>
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">Account Type</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-at fi"></i>
                                                    <select name="email_type" class="field">
                                                    <option value="" disabled <?=!$is_edit?'selected':''?>>Please select type</option>
                                                    <?php foreach(["Office 365","Survey 123","Google account","Trimble account"] as $t){
                                                    $sel=(isset($edit_data['account_type'])&&$edit_data['account_type']==$t)?'selected':'';
                                                    echo "<option value='$t' $sel>$t</option>";
                                                    }?>
                                                    </select>
                                                    <i class="fas fa-chevron-down select-arrow"></i>
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">Account Status</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-circle-check fi"></i>
                                                    <select name="status" class="field">
                                                    <option value="actived" <?=(isset($edit_data['account_status'])&&strtolower($edit_data['account_status'])=='actived')?'selected':''?>>Actived</option>
                                                    <option value="inactived" <?=(isset($edit_data['account_status'])&&strtolower($edit_data['account_status'])=='inactived')?'selected':''?>>Inactived</option>
                                                    <option value="spare" <?=(isset($edit_data['account_status'])&&strtolower($edit_data['account_status'])=='spare')?'selected':''?>>Spare</option>
                                                    </select>
                                                    <i class="fas fa-chevron-down select-arrow"></i>
                                                    </div>
                                                    </div>
                                                    </div>
                                                    </div>
                                                    
                                                    <!-- ======== SECTION 2 : Credentials & Email Addresses ======== -->
                                                    <div class="form-section">
                                                    <div class="form-section-hdr">
                                                    <div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">2</div>
                                                    <p>Credentials &amp; Email Addresses</p>
                                                    </div>
                                                    <div class="form-grid g2" style="margin-bottom:.875rem;">
                                                    <div>
                                                    <label class="field-lbl">Office365 / SV123 / Gmail / Trimble</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-envelope fi"></i>
                                                    <input type="text" name="email_1" class="field" placeholder="Enter Email or Username"
                                                    value="<?=$edit_data['primary_email']??''?>">
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">Account Password</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-lock fi"></i>
                                                    <input type="text" name="password" class="field" placeholder="Enter password"
                                                    value="<?=$edit_data['password']??''?>">
                                                    </div>
                                                    </div>
                                                    </div>
                                                    <div class="form-grid g2">
                                                    <div>
                                                    <label class="field-lbl">Second Email</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-envelope-open fi"></i>
                                                    <input type="email" name="email_2" class="field" placeholder="Optional email"
                                                    value="<?=$edit_data['second_email']??''?>">
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">Third Email</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-envelope-open fi"></i>
                                                    <input type="email" name="email_3" class="field" placeholder="Optional email"
                                                    value="<?=$edit_data['third_email']??''?>">
                                                    </div>
                                                    </div>
                                                    </div>
                                                    </div>
                                                    
                                                    <!-- ======== SECTION 3 : Organization & Asset Info ======== -->
                                                    <div class="form-section">
                                                    <div class="form-section-hdr">
                                                    <div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">3</div>
                                                    <p>Organization &amp; Asset Info</p>
                                                    </div>
                                                    <div class="form-grid g3">
                                                    <div>
                                                    <label class="field-lbl">Department</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-sitemap fi"></i>
                                                    <select name="department" class="field">
                                                    <option value="" disabled <?=!$is_edit?'selected':''?>>Select Department</option>
                                                    <?php foreach(["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Expat"] as $d){
                                                    $sel=(isset($edit_data['department'])&&$edit_data['department']==$d)?'selected':'';
                                                    echo "<option value='$d' $sel>$d</option>";
                                                    }?>
                                                    </select>
                                                    <i class="fas fa-chevron-down select-arrow"></i>
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">Team</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-users fi"></i>
                                                    <input type="text" name="team" class="field" placeholder="Enter team name"
                                                    value="<?=$edit_data['team']??''?>">
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">Phone Number</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-phone-alt fi"></i>
                                                    <input type="text" name="phone" class="field" placeholder="020 XXXXXXXX"
                                                    value="<?=$edit_data['phone']??''?>">
                                                    </div>
                                                    </div>
                                                    <div>
                                                    <label class="field-lbl">INS Number</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-id-card fi"></i>
                                                    <input type="text" name="ins_number" class="field" placeholder="Enter INS number"
                                                    value="<?=$edit_data['ins_number']??''?>">
                                                    </div>
                                                    </div>
                                                    <div class="g-span2">
                                                    <label class="field-lbl">Halo Device Number</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-laptop-medical fi"></i>
                                                    <input type="text" name="halo_id" class="field" placeholder="Enter device number"
                                                    value="<?=$edit_data['halo_device_number']??''?>">
                                                    </div>
                                                    </div>
                                                    <div class="g-span3">
                                                    <label class="field-lbl">Remark</label>
                                                    <div class="field-wrap">
                                                    <i class="fas fa-comment-dots fi"></i>
                                                    <input type="text" name="remark" class="field" placeholder="Enter remark (optional)"
                                                    value="<?=$edit_data['remark']??''?>">
                                                    </div>
                                                    </div>
                                                    </div>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn-submit">
                                                    <i class="fas <?=$is_edit?'fa-save':'fa-cloud-upload-alt'?>"></i>
                                                    <?=$is_edit?'Update Account Information':'Save Account Information'?>
                                                    </button>
                                                    </form>
                                                    </div>
                                                    </div>
                                                    
                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                    <script>
                                                    document.getElementById('accountForm').addEventListener('submit',function(e){
                                                    e.preventDefault();
                                                    const form=this;
                                                    const isEdit=form.querySelector('input[name="update_id"]')!==null;
                                                    const action=isEdit?'update_account.php':'save_account.php';
                                                    fetch(action,{method:'POST',body:new FormData(form)})
                                                    .then(r=>r.json())
                                                    .then(data=>{
                                                    if(data.status==='saved'){
                                                    Swal.fire({icon:'success',title:'Saved Successfully!',text:'Account information has been saved.',confirmButtonColor:'#002855',timer:2500,timerProgressBar:true});
                                                    if(!isEdit)form.reset();
                                                    }else if(data.status==='updated'){
                                                    Swal.fire({icon:'success',title:'Updated Successfully!',text:'Account information has been updated.',confirmButtonColor:'#002855',timer:2500,timerProgressBar:true});
                                                    }else{
                                                    Swal.fire({icon:'error',title:'Error Occurred!',text:data.msg||'Unable to save data.',confirmButtonColor:'#d33'});
                                                    }
                                                    })
                                                    .catch(()=>{
                                                    Swal.fire({icon:'error',title:'Connection Failed!',text:'Please try again.',confirmButtonColor:'#d33'});
                                                    });
                                                    });
                                                    </script>
                                                    
                                                    <?php
                                                    // ====================================================
                                                    //  ACCOUNT LIST
                                                    // ====================================================
                                                    elseif($current_page=='account'&&$action=='list'):
                                                        $filter_type=isset($_GET['filter_type'])?mysqli_real_escape_string($conn,$_GET['filter_type']):'';
                                                        $filter_dept=isset($_GET['filter_dept'])?mysqli_real_escape_string($conn,$_GET['filter_dept']):'';
                                                        $sub_where="";
                                                        if($filter_type!='') $sub_where.=" AND account_type='$filter_type'";
                                                        if($filter_dept!='') $sub_where.=" AND department='$filter_dept'";
                                                        
                                                        // ✅ ເພີ່ມ remark ໃນທຸກ UNION
                                                        $sql="SELECT id,full_name,account_type,account_status,
                 primary_email AS `Office365-SV123-Gmail-Trimble`,
                 password,second_email,third_email,
                 department,team,ins_number,halo_device_number,
                 remark,phone,
                 'office365_accounts' as source_table
          FROM office365_accounts
          WHERE (full_name IS NOT NULL AND full_name!='') $sub_where

          UNION ALL

          SELECT id,full_name,account_type,account_status,
                 primary_email AS `Office365-SV123-Gmail-Trimble`,
                 password,second_email,third_email,
                 department,team,ins_number,halo_device_number,
                 remark,phone,
                 'google_accounts' as source_table
          FROM google_accounts
          WHERE (full_name IS NOT NULL AND full_name!='') $sub_where

          UNION ALL

          SELECT id,full_name,account_type,account_status,
                 primary_email AS `Office365-SV123-Gmail-Trimble`,
                 password,second_email,third_email,
                 department,team,ins_number,halo_device_number,
                 remark,phone,
                 'survey123_accounts' as source_table
          FROM survey123_accounts
          WHERE (full_name IS NOT NULL AND full_name!='') $sub_where

          UNION ALL

          SELECT id,full_name,account_type,account_status,
                 primary_email AS `Office365-SV123-Gmail-Trimble`,
                 password,second_email,third_email,
                 department,team,ins_number,halo_device_number,
                 remark,phone,
                 'trimble_accounts' as source_table
          FROM trimble_accounts
          WHERE (full_name IS NOT NULL AND full_name!='') $sub_where";
                                                        
                                                        $result=mysqli_query($conn,$sql);
                                                        ?>
                                                        
                                                        <div class="page-hdr">
                                                        <div><h2>All System Accounts</h2><p>List of all user accounts in the system</p></div>
                                                        <a href="?page=account&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> New Account</a>
                                                        </div>
                                                        
                                                        <div class="tbl-wrap">
                                                        <form method="GET" class="filter-bar">
                                                        <input type="hidden" name="page" value="account">
                                                        <input type="hidden" name="action" value="list">
                                                        <span class="filter-lbl">Filter:</span>
                                                        <select name="filter_type" class="filter-select">
                                                        <option value="">All Account Types</option>
                                                        <option value="Office 365"      <?=$filter_type=='Office 365'?'selected':''?>>Office 365</option>
                                                        <option value="Google account"  <?=$filter_type=='Google account'?'selected':''?>>Google account</option>
                                                        <option value="Survey 123"      <?=$filter_type=='Survey 123'?'selected':''?>>Survey 123</option>
                                                        <option value="Trimble account" <?=$filter_type=='Trimble account'?'selected':''?>>Trimble account</option>
                                                        </select>
                                                        <select name="filter_dept" class="filter-select">
                                                        <option value="">All Departments</option>
                                                        <?php foreach(["GIS","ICT","HR","Finance","Liaison","Facility","OPS","Fleet","Electical","Medical","Logistic","Expat"] as $d)
                                                        echo "<option value='$d' ".($filter_dept==$d?'selected':'').">$d</option>"; ?>
                                                        </select>
                                                        <div class="search-wrap">
                                                        <i class="fas fa-search"></i>
                                                        <input type="text" id="tableSearch" class="filter-input" placeholder="Search name, email, type...">
                                                        </div>
                                                        <button type="submit" class="btn-filter">Apply</button>
                                                        <?php if($filter_type||$filter_dept): ?>
                                                            <a href="?page=account&action=list" class="btn-clear">Clear All</a>
                                                            <?php endif; ?>
                                                            </form>
                                                            
                                                            <div class="tbl-scroll">
                                                            <table id="accountTable" style="min-width:2100px;">
                                                            <thead>
                                                            <tr>
                                                            <th class="sticky-l200" style="width:200px;background:#f4f7fd;">Full Name</th>
                                                            <th style="width:120px;">Type</th>
                                                            <th style="width:110px;" class="th-center">Status</th>
                                                            <th style="width:220px;">Office365/SV123/Gmail/Trimble</th>
                                                            <th style="width:160px;">Password</th>
                                                            <th style="width:200px;">Second Email</th>
                                                            <th style="width:200px;">Third Email</th>
                                                            <th style="width:130px;">Department</th>
                                                            <th style="width:110px;">Team</th>
                                                            <th style="width:110px;" class="th-center">INS Number</th>
                                                            <th style="width:160px;">Halo Device</th>
                                                            <th style="width:180px;">Remark</th>
                                                            <th style="width:130px;">Phone</th>
                                                            <th class="sticky-r" style="width:90px;text-align:center;">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php if($result&&mysqli_num_rows($result)>0):while($row=mysqli_fetch_assoc($result)): ?>
                                                                <tr>
                                                                <td class="td-bold sticky-l200" style="background:var(--surface);"><?=htmlspecialchars($row['full_name'])?></td>
                                                                <td><?=htmlspecialchars($row['account_type'])?></td>
                                                                <td>
                                                                <?php
                                                                $st=strtolower($row['account_status']??'');
                                                                $spill=$st=='actived'?'pill-green':($st=='spare'?'pill-blue':'pill-red');
                                                                ?>
                                                                <span class="status-pill <?=$spill?>"><?=strtoupper($row['account_status']??'-')?></span>
                                                                </td>
                                                                <td><?=htmlspecialchars($row['Office365-SV123-Gmail-Trimble'])?></td>
                                                                <td class="td-mono"><?=htmlspecialchars($row['password'])?></td>
                                                                <td><?=htmlspecialchars($row['second_email'])?></td>
                                                                <td><?=htmlspecialchars($row['third_email'])?></td>
                                                                <td><?=htmlspecialchars($row['department'])?></td>
                                                                <td><?=htmlspecialchars($row['team'])?></td>
                                                                <td><?=htmlspecialchars($row['ins_number'])?></td>
                                                                <td><?=htmlspecialchars($row['halo_device_number'])?></td>
                                                                <!-- ✅ Cell Remark ໃໝ່ -->
                                                                <td><?=htmlspecialchars($row['remark']??'')?></td>
                                                                <td class="td-bold"><?=htmlspecialchars($row['phone'])?></td>
                                                                <td class="sticky-r" style="text-align:center;background:var(--surface);">
                                                                <div class="action-btns">
                                                                <a href="?page=account&action=form&id=<?=$row['id']?>&table=<?=$row['source_table']?>" class="action-btn edit">
                                                                <i class="fas fa-edit"></i>
                                                                </a>
                                                                <button class="action-btn del" onclick="deleteAccount(<?=$row['id']?>,'<?=$row['source_table']?>')">
                                                                <i class="fas fa-trash"></i>
                                                                </button>
                                                                </div>
                                                                </td>
                                                                </tr>
                                                                <?php endwhile;else: ?>
                                                                    <!-- ✅ colspan=14 (ເພີ່ມຈາກ 13 ເປັນ 14) -->
                                                                    <tr class="empty-row"><td colspan="14"><i class="fas fa-folder-open"></i>No data found</td></tr>
                                                                    <?php endif; ?>
                                                                    </tbody>
                                                                    </table>
                                                                    </div>
                                                                    </div>
                                                                    
                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                    <script>
                                                                    function deleteAccount(id,tableName){
                                                                    Swal.fire({
                                                                    title:'Are you sure?',
                                                                    text:'This action cannot be undone!',
                                                                    icon:'warning',
                                                                    showCancelButton:true,
                                                                    confirmButtonColor:'#d33',
                                                                    cancelButtonColor:'#64748b',
                                                                    confirmButtonText:'Yes, Delete',
                                                                    cancelButtonText:'Cancel',
                                                                    reverseButtons:true
                                                                    }).then(r=>{
                                                                    if(r.isConfirmed) window.location.href='?page=account&action=delete&id='+id+'&table='+tableName;
                                                                    });
                                                                    }
                                                                    
                                                                    const _p=new URLSearchParams(window.location.search);
                                                                    if(_p.get('status')==='deleted'){
                                                                    Swal.fire({title:'Deleted!',text:'Data has been deleted successfully.',icon:'success',timer:2000,showConfirmButton:false});
                                                                    window.history.replaceState({},document.title,window.location.pathname+window.location.search.split('&status=')[0]);
                                                                    }
                                                                    
                                                                    document.getElementById('tableSearch').addEventListener('keyup',function(){
                                                                    const v=this.value.toLowerCase();
                                                                    document.querySelectorAll('#accountTable tbody tr:not(.empty-row)').forEach(r=>{
                                                                    r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';
                                                                    });
                                                                    });
                                                                    </script>
                                                                    <?php
                                                                    //  DEVICE TRANSFER
                                                                    elseif($current_page=='device'&&$action=='transfer'): ?>
                                                                        <div id="lookup-banner" style="display:none;margin-bottom:1rem;padding:.75rem 1.125rem;border-radius:10px;font-size:.8rem;font-weight:600;"></div>
                                                                        <div class="form-wrap">
                                                                        <div class="form-header"><div class="form-header-icon"><i class="fas fa-exchange-alt"></i></div><div><h3>Transfer Device</h3><p>Enter Halo ID to auto-fill device information</p></div></div>
                                                                        <div class="form-body">
                                                                        <form id="transferForm" action="save_transfer.php" method="POST">
                                                                        <input type="hidden" name="source_table" id="f_source_table">
                                                                        <div class="form-section">
                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>Device Identity ” Enter Halo ID to auto-fill</p></div>
                                                                        <div class="form-grid g3">
                                                                        <div><label class="field-lbl">Halo ID <span>*</span></label><div class="field-wrap" style="display:flex;gap:.5rem;align-items:center;"><i class="fas fa-fingerprint fi"></i><input type="text" name="halo_id" id="halo_id_input" class="field" placeholder="Enter Halo ID then press Enter" required style="flex:1;"><button type="button" id="lookup_btn" style="padding:.65rem 1rem;background:var(--navy);color:#fff;border:none;border-radius:8px;font-size:.75rem;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0;transition:all .15s;" onmouseover="this.style.background='var(--navy2)'" onmouseout="this.style.background='var(--navy)'"><i class="fas fa-search"></i> Search</button></div></div>
                                                                        <div><label class="field-lbl">Serial Number</label><div class="field-wrap"><i class="fas fa-barcode fi"></i><input type="text" name="serial_number" id="f_serial" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Device Type</label><div class="field-wrap"><i class="fas fa-laptop fi"></i><input type="text" name="device_type" id="f_device_type" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Brand</label><div class="field-wrap"><i class="fas fa-tag fi"></i><input type="text" name="brand" id="f_brand" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Model</label><div class="field-wrap"><i class="fas fa-cube fi"></i><input type="text" name="model" id="f_model" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Current Status</label><div class="field-wrap"><i class="fas fa-circle-check fi"></i><input type="text" name="current_status" id="f_status" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        </div></div>
                                                                        <div class="form-section">
                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#fee2e2;color:#b91c1c;">2</div><p>Transfer From ” Current Owner (Auto-filled)</p></div>
                                                                        <div class="form-grid g3">
                                                                        <div><label class="field-lbl">Current Username</label><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" name="from_username" id="f_from_username" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Current Department</label><div class="field-wrap"><i class="fas fa-sitemap fi"></i><input type="text" name="from_department" id="f_from_department" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Current Team</label><div class="field-wrap"><i class="fas fa-users fi"></i><input type="text" name="from_team" id="f_from_team" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Current INS Number</label><div class="field-wrap"><i class="fas fa-id-card fi"></i><input type="text" name="from_ins_number" id="f_from_ins" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        <div><label class="field-lbl">Current Location</label><div class="field-wrap"><i class="fas fa-map-marker-alt fi"></i><input type="text" name="from_location" id="f_from_location" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                        </div></div>
                                                                        <div class="form-section">
                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#dcfce7;color:#15803d;">3</div><p>Transfer To” New Owner &nbsp;<span style="font-size:.62rem;font-weight:600;color:var(--muted);text-transform:none;letter-spacing:0;">(Enter INS to auto-fill)</span></p></div>
                                                                        <div class="form-grid g3">
                                                                        <div><label class="field-lbl">New INS Number <span>*</span></label><div class="field-wrap" style="display:flex;gap:.5rem;align-items:center;"><i class="fas fa-id-card fi"></i><input type="text" name="to_ins_number" id="to_ins_input" class="field" placeholder="Scan or type INS number" required style="flex:1;"><button type="button" id="ins_lookup_btn" style="padding:.65rem .875rem;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:.72rem;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0;" onmouseover="this.style.background='var(--navy)'" onmouseout="this.style.background='var(--blue)'"><i class="fas fa-search"></i></button></div><div id="ins-banner" style="display:none;margin-top:.4rem;padding:.45rem .75rem;border-radius:7px;font-size:.72rem;font-weight:600;"></div></div>
                                                                        <div><label class="field-lbl">New Username <span>*</span></label><div class="field-wrap"><i class="fas fa-user-check fi"></i><input type="text" name="to_username" id="to_username" class="field" placeholder="Auto-filled from INS" required></div></div>
                                                                        <div><label class="field-lbl">New Department <span>*</span></label><div class="field-wrap"><i class="fas fa-sitemap fi"></i><select name="to_department" id="to_department" class="field" required><option value="" disabled selected>Select Department</option><?php foreach(["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Expat"] as $d) echo "<option value='$d'>$d</option>"; ?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                        <div><label class="field-lbl">New Team</label><div class="field-wrap"><i class="fas fa-users fi"></i><input type="text" name="to_team" id="to_team" class="field" placeholder="Auto-filled or enter manually"></div></div>
                                                                        <div><label class="field-lbl">New Location</label><div class="field-wrap"><i class="fas fa-map-marker-alt fi"></i><input type="text" name="to_location" id="to_location" class="field" placeholder="Location / Local"></div></div>
                                                                        <div><label class="field-lbl">Transfer Date <span>*</span></label><div class="field-wrap"><i class="fas fa-calendar-alt fi"></i><input type="date" name="transfer_date" id="transfer_date" class="field" required value="<?=date('Y-m-d')?>"></div></div>
                                                                        </div></div>
                                                                        <div class="form-section">
                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#f5f3ff;color:#7c3aed;">4</div><p>Additional Information</p></div>
                                                                        <div class="field-wrap ta-icon"><i class="fas fa-comment-dots fi"></i><textarea name="remark" class="field" rows="3" placeholder="Reason for transfer or additional notes..." style="padding-top:.7rem;resize:vertical;"></textarea></div>
                                                                        </div>
                                                                        <button type="submit" id="submit_btn" class="btn-submit" disabled style="opacity:.45;cursor:not-allowed;"><i class="fas fa-exchange-alt"></i> Confirm Transfer</button>
                                                                        </form></div></div>
                                                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                        <script>
                                                                        function setField(id,val){const el=document.getElementById(id);if(el)el.value=val||'';}
                                                                        function showBanner(msg,type){const b=document.getElementById('lookup-banner');b.style.display='block';b.style.background=type==='success'?'#dcfce7':(type==='error'?'#fee2e2':'#dbeafe');b.style.color=type==='success'?'#15803d':(type==='error'?'#b91c1c':'#1d4ed8');b.style.border='1px solid '+(type==='success'?'#bbf7d0':(type==='error'?'#fecaca':'#bfdbfe'));b.innerHTML=msg;}
                                                                        function enableSubmit(e){const btn=document.getElementById('submit_btn');btn.disabled=!e;btn.style.opacity=e?'1':'.45';btn.style.cursor=e?'pointer':'not-allowed';}
                                                                        function lookupHaloId(){const halo=document.getElementById('halo_id_input').value.trim();if(!halo){showBanner('<i class="fas fa-exclamation-circle"></i> Please enter a Halo ID first.','error');return;}showBanner('<i class="fas fa-spinner fa-spin"></i> Searching...','info');enableSubmit(false);fetch('index.php?lookup=1&halo_id='+encodeURIComponent(halo)).then(r=>r.json()).then(data=>{if(data.status==='found'){const d=data.data;setField('f_serial',d.serial_number);setField('f_device_type',d.device_type);setField('f_brand',d.brand);setField('f_model',d.model);setField('f_status',d.status);setField('f_source_table',d.source_table);setField('f_from_username',d.username);setField('f_from_department',d.department);setField('f_from_team',d.team);setField('f_from_ins',d.ins_number);setField('f_from_location',d.location_local);showBanner('<i class="fas fa-check-circle"></i> Device found: <b>'+d.device_type+' â€” '+d.brand+' '+d.model+'</b>','success');enableSubmit(true);}else{['f_serial','f_device_type','f_brand','f_model','f_status','f_source_table','f_from_username','f_from_department','f_from_team','f_from_ins','f_from_location'].forEach(id=>setField(id,''));showBanner('<i class="fas fa-times-circle"></i> '+data.msg,'error');enableSubmit(false);}}).catch(()=>{showBanner('<i class="fas fa-exclamation-triangle"></i> Connection error.','error');enableSubmit(false);});}
                                                                        document.getElementById('halo_id_input').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();lookupHaloId();}});
                                                                        document.getElementById('lookup_btn').addEventListener('click',lookupHaloId);
                                                                        function lookupINS(){const ins=document.getElementById('to_ins_input').value.trim();const banner=document.getElementById('ins-banner');if(!ins){banner.style.cssText='display:block;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;';banner.innerHTML='<i class="fas fa-exclamation-circle"></i> Please enter INS number.';return;}banner.style.cssText='display:block;background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;';banner.innerHTML='<i class="fas fa-spinner fa-spin"></i> Searching...';fetch('index.php?lookup_ins=1&ins_number='+encodeURIComponent(ins)).then(r=>r.json()).then(data=>{if(data.status==='found'){const d=data.data;const uname=document.getElementById('to_username');const dept=document.getElementById('to_department');const team=document.getElementById('to_team');const loc=document.getElementById('to_location');if(uname)uname.value=d.username||d.full_name||'';if(dept&&d.department){for(let i=0;i<dept.options.length;i++){if(dept.options[i].value===d.department){dept.selectedIndex=i;break;}}}if(team)team.value=d.team||'';if(loc)loc.value=d.location||d.location_local||'';banner.style.cssText='display:block;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;';banner.innerHTML='<i class="fas fa-check-circle"></i> Found: <b>'+(d.username||d.full_name||ins)+'</b>'+(d.department?' | <b>'+d.department+'</b>':'');}else{banner.style.cssText='display:block;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;';banner.innerHTML='<i class="fas fa-exclamation-triangle"></i> '+data.msg+' â€” fill manually.';}}).catch(()=>{banner.style.cssText='display:block;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;';banner.innerHTML='Connection error.';});}
                                                                        document.getElementById('to_ins_input').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();lookupINS();}});
                                                                        document.getElementById('ins_lookup_btn').addEventListener('click',lookupINS);
                                                                        let insTimer;document.getElementById('to_ins_input').addEventListener('input',function(){clearTimeout(insTimer);if(this.value.trim().length>=4)insTimer=setTimeout(lookupINS,600);});
                                                                        document.getElementById('transferForm').addEventListener('submit',function(e){e.preventDefault();const form=this;fetch('save_transfer.php',{method:'POST',body:new FormData(form)}).then(r=>{const ct=r.headers.get('content-type')||'';if(!ct.includes('json'))return r.text().then(t=>{throw new Error(t.substring(0,200));});return r.json();}).then(data=>{if(data.status==='saved'){Swal.fire({icon:'success',title:'Transfer Complete!',text:'Device transferred successfully.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true}).then(()=>{form.reset();document.getElementById('transfer_date').value='<?=date('Y-m-d')?>';document.getElementById('lookup-banner').style.display='none';enableSubmit(false);});}else{Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});}}).catch(err=>Swal.fire({icon:'error',title:'Connection Error!',text:err.message||'Please try again.',confirmButtonColor:'#dc2626'}));});
                                                                        </script>
                                                                        
                                                                        <?php
                                                                        //  DEVICE FORM
                                                                        elseif($current_page=='device'&&$action=='form'):
                                                                            $is_edit=false;
                                                                            $row=['id'=>'','device_type'=>'','halo_id'=>'','brand'=>'','model'=>'','serial_number'=>'','date_in'=>'','date_out'=>'','username'=>'','department'=>'','team'=>'','location_local'=>'','ins_number'=>'','status'=>'Working','sv123_user'=>'','sv123_pass'=>'','gmail_address'=>'','gmail_pass'=>'','dgps_mail'=>'','dgps_pass'=>'','bitlocker_pass'=>'','bitlocker_id'=>'','bitlocker_key'=>'','source_table'=>'','remark'=>''];
                                                                            if(isset($_GET['id'])&&!empty($_GET['id'])){
                                                                            $id=(int)($_GET['id']??0);
                                                                            $dev_table_map2=['Laptop'=>'laptops','Desktop'=>'desktops','Tablet'=>'tablets','Phone'=>'phones','Monitor'=>'monitors','DGPS'=>'dgps','PowerBank'=>'powerbanks'];
                                                                            $src=isset($_GET['src'])?mysqli_real_escape_string($conn,$_GET['src']):'';
                                                                            if($src&&in_array($src,array_values($dev_table_map2))){$eq=mysqli_query($conn,"SELECT *, '$src' as source_table FROM `$src` WHERE id='$id'");}
                                                                            else{$eq=null;foreach($dev_table_map2 as $dt=>$tbl){$tr=mysqli_query($conn,"SHOW TABLES LIKE '$tbl'");if($tr&&mysqli_num_rows($tr)>0){$r2=mysqli_query($conn,"SELECT *, '$tbl' as source_table FROM `$tbl` WHERE id='$id'");if($r2&&mysqli_num_rows($r2)>0){$eq=$r2;break;}}}}
                                                                            if($eq&&($ed=mysqli_fetch_assoc($eq))){$is_edit=true;$row=$ed;}
                                                                            }
                                                                            ?>
                                                                            <div class="form-wrap">
                                                                            <div class="form-header"><div class="form-header-icon"><i class="fas <?=$is_edit?'fa-edit':'fa-laptop-medical'?>"></i></div><div><h3><?=$is_edit?'Update ICT Device':'New Device'?></h3><p>Add or modify device information in the inventory system</p></div></div>
                                                                            <div class="form-body">
                                                                            <form id="deviceForm" action="save_device.php" method="POST">
                                                                            <?php if($is_edit): ?><input type="hidden" name="id" value="<?=htmlspecialchars($row['id'])?>"><input type="hidden" name="source_table" value="<?=htmlspecialchars($row['source_table']??'')?>"> <?php endif; ?>
                                                                                <div class="form-section">
                                                                                <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>Device Identity</p></div>
                                                                                <div class="form-grid g3">
                                                                                <div><label class="field-lbl">Device Type <span>*</span></label><div class="field-wrap"><i class="fas fa-laptop fi"></i><select name="device_type" class="field" required><option value="" disabled <?=!$is_edit?'selected':''?>>Select Device Type</option><?php foreach(["Laptop","Desktop","Tablet","Phone","Monitor","DGPS","PowerBank"] as $t) echo "<option value='$t' ".($row['device_type']==$t?'selected':'').">$t</option>"; ?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                <div><label class="field-lbl">Halo ID</label><div class="field-wrap"><i class="fas fa-fingerprint fi"></i><input type="text" name="halo_id" class="field" placeholder="Halo ID" value="<?=htmlspecialchars($row['halo_id']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Brand</label><div class="field-wrap"><i class="fas fa-tag fi"></i><input type="text" name="brand" class="field" placeholder="Brand Name" value="<?=htmlspecialchars($row['brand']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Model</label><div class="field-wrap"><i class="fas fa-cube fi"></i><input type="text" name="model" class="field" placeholder="Device Model" value="<?=htmlspecialchars($row['model']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Serial Number</label><div class="field-wrap"><i class="fas fa-barcode fi"></i><input type="text" name="serial_number" class="field" placeholder="Serial Number" value="<?=htmlspecialchars($row['serial_number']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Date In</label><div class="field-wrap"><i class="fas fa-calendar-plus fi"></i><input type="date" name="date_in" class="field" value="<?=htmlspecialchars($row['date_in']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Date Out</label><div class="field-wrap"><i class="fas fa-calendar-minus fi"></i><input type="date" name="date_out" class="field" value="<?=htmlspecialchars($row['date_out']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Month Used</label><div class="field-wrap"><i class="fas fa-calendar-days fi"></i><input type="text" name="month_used" class="field" placeholder="Auto-calculated" readonly style="background:#f0f4fb;" value="<?php if(!empty($row['date_in'])){$date_in=new DateTime($row['date_in']);$now=new DateTime();$diff=$now->diff($date_in);echo $diff->m + ($diff->y * 12);}?>"></div></div>
                                                                                <div><label class="field-lbl">Year Used</label><div class="field-wrap"><i class="fas fa-calendar-year fi"></i><input type="text" name="year_used" class="field" placeholder="Auto-calculated" readonly style="background:#f0f4fb;" value="<?php if(!empty($row['date_in'])){$date_in=new DateTime($row['date_in']);$now=new DateTime();$diff=$now->diff($date_in);echo number_format($diff->days / 365.25, 1);}?>"></div></div>
                                                                                <div><label class="field-lbl">Username</label><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" name="username" class="field" placeholder="Username" value="<?=htmlspecialchars($row['username']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Department</label><div class="field-wrap"><i class="fas fa-sitemap fi"></i><select name="department" class="field"><option value="" disabled <?=!$is_edit?'selected':''?>>Select Department</option><?php foreach(["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Expat"] as $d) echo "<option value='$d' ".($row['department']==$d?'selected':'').">$d</option>"; ?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                <div><label class="field-lbl">Team</label><div class="field-wrap"><i class="fas fa-users fi"></i><input type="text" name="team" class="field" placeholder="Team Name" value="<?=htmlspecialchars($row['team']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Location / Local</label><div class="field-wrap"><i class="fas fa-map-marker-alt fi"></i><input type="text" name="location_local" class="field" placeholder="Location" value="<?=htmlspecialchars($row['location_local']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">INS Number</label><div class="field-wrap"><i class="fas fa-id-card fi"></i><input type="text" name="ins_number" class="field" placeholder="INS Number" value="<?=htmlspecialchars($row['ins_number']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">Device Status</label><div class="field-wrap"><i class="fas fa-circle-check fi"></i><select name="status" class="field"><option value="Working" <?=$row['status']=='Working'?'selected':''?>>Working</option><option value="Spare" <?=$row['status']=='Spare'?'selected':''?>>Spare</option><option value="Broken" <?=$row['status']=='Broken'?'selected':''?>>Broken</option><option value="Pending write-off" <?=$row['status']=='Pending write-off'?'selected':''?>>Pending write-off</option><option value="Write-off" <?=$row['status']=='Write-off'?'selected':''?>>Write-off</option></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                </div></div>
                                                                                <div class="form-section">
                                                                                <div class="form-section-hdr"><div class="step-badge" style="background:#e0e7ff;color:#4338ca;">2</div><p>System &amp; GIS Accounts</p></div>
                                                                                <div class="form-grid g2">
                                                                                <div class="cred-box"><div style="font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:.45rem;">Survey123 Credentials</div><div style="display:flex;flex-direction:column;gap:.5rem;"><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" name="sv123_user" class="field" placeholder="SV123 User" value="<?=htmlspecialchars($row['sv123_user']??'')?>"></div><div class="field-wrap"><i class="fas fa-lock fi"></i><input type="text" name="sv123_password" class="field" placeholder="SV123 Password" value="<?=htmlspecialchars($row['sv123_pass']??'')?>"></div></div></div>
                                                                                <div class="cred-box"><div style="font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:.45rem;">Google Account</div><div style="display:flex;flex-direction:column;gap:.5rem;"><div class="field-wrap"><i class="fab fa-google fi"></i><input type="text" name="gmail_address" class="field" placeholder="example@gmail.com" value="<?=htmlspecialchars($row['gmail_address']??'')?>"></div><div class="field-wrap"><i class="fas fa-lock fi"></i><input type="text" name="gmail_password" class="field" placeholder="Gmail Password" value="<?=htmlspecialchars($row['gmail_pass']??'')?>"></div></div></div>
                                                                                <div class="cred-box" style="grid-column:span 2;"><div style="font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:.45rem;">DGPS / Professional Mail</div><div class="form-grid g2"><div class="field-wrap"><i class="fas fa-envelope fi"></i><input type="email" name="dgps_mail" class="field" placeholder="DGPS Mail" value="<?=htmlspecialchars($row['dgps_mail']??'')?>"></div><div class="field-wrap"><i class="fas fa-lock fi"></i><input type="text" name="dgps_password" class="field" placeholder="DGPS Password" value="<?=htmlspecialchars($row['dgps_pass']??'')?>"></div></div></div>
                                                                                </div></div>
                                                                                <div class="form-section">
                                                                                <div class="form-section-hdr"><div class="step-badge" style="background:#dcfce7;color:#15803d;">3</div><p>Security &amp; Hardware</p></div>
                                                                                <div class="form-grid g2">
                                                                                <div><label class="field-lbl">BitLocker Password</label><div class="field-wrap"><i class="fas fa-key fi"></i><input type="text" name="bitlocker_password" class="field" placeholder="BitLocker Password" value="<?=htmlspecialchars($row['bitlocker_pass']??'')?>"></div></div>
                                                                                <div><label class="field-lbl">BitLocker Identifier</label><div class="field-wrap"><i class="fas fa-fingerprint fi"></i><input type="text" name="bitlocker_identifier" class="field" placeholder="BitLocker Identifier" value="<?=htmlspecialchars($row['bitlocker_id']??'')?>"></div></div>
                                                                                <div class="g-span2"><label class="field-lbl">BitLocker Recovery Key (48-digit)</label><div class="field-wrap"><i class="fas fa-shield-halved fi"></i><input type="text" name="bitlocker_key" class="field" placeholder="BitLocker Recovery Key" value="<?=htmlspecialchars($row['bitlocker_key']??'')?>"></div></div>
                                                                                <div class="g-span2"><label class="field-lbl">Remark</label><div class="field-wrap"><i class="fas fa-comment-dots fi"></i><input type="text" name="remark" class="field" placeholder="Remark" value="<?=htmlspecialchars($row['remark']??'')?>"></div></div>
                                                                                </div></div>
                                                                                <button type="submit" class="btn-submit"><i class="fas fa-save"></i><?=$is_edit?'Update Changes':'Register ICT Device'?></button>
                                                                                </form></div></div>
                                                                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                <script>
                                                                                document.getElementById('deviceForm').addEventListener('submit',function(e){e.preventDefault();const form=this;const isEdit=form.querySelector('input[name="id"]')!==null;fetch('save_device.php',{method:'POST',body:new FormData(form)}).then(r=>{const ct=r.headers.get('content-type')||'';if(!ct.includes('application/json'))return r.text().then(txt=>{throw new Error('Server error: '+txt.substring(0,300));});return r.json();}).then(data=>{if(data.status==='saved'){Swal.fire({icon:'success',title:'Saved Successfully!',text:'Device information has been saved.',confirmButtonColor:'#002855',timer:2500,timerProgressBar:true});if(!isEdit)form.reset();}else if(data.status==='updated'){Swal.fire({icon:'success',title:'Updated Successfully!',confirmButtonColor:'#002855',timer:2500,timerProgressBar:true});}else{Swal.fire({icon:'error',title:'Error Occurred!',text:data.msg||'Unable to save.',confirmButtonColor:'#d33'});}}).catch(err=>{Swal.fire({icon:'error',title:'Error Occurred!',text:err.message||'Please try again.',confirmButtonColor:'#d33'});});});
                                                                                </script>
                                                                                
                                                                                <?php
                                                                                //  DEVICE LIST
                                                                                elseif($current_page=='device'&&$action=='list'):
                                                                                    $filter_type=isset($_GET['type'])?mysqli_real_escape_string($conn,$_GET['type']):'';
                                                                                    $filter_dept=isset($_GET['dept'])?mysqli_real_escape_string($conn,$_GET['dept']):'';
                                                                                    $filter_status=isset($_GET['status'])?mysqli_real_escape_string($conn,$_GET['status']):'';
                                                                                    $unions=[];
                                                                                    foreach($dev_type_map as $dtype=>$tbl){
                                                                                    $tr=mysqli_query($conn,"SHOW TABLES LIKE '$tbl'");if(!$tr||mysqli_num_rows($tr)==0)continue;
                                                                                    $has_blid=false;$cr=mysqli_query($conn,"SHOW COLUMNS FROM `$tbl` LIKE 'bitlocker_id'");if($cr&&mysqli_num_rows($cr)>0)$has_blid=true;
                                                                                    $blid_col=$has_blid?"bitlocker_id":"'' AS bitlocker_id";
                                                                                    $where="1=1";
                                                                                    if($filter_type!='') $where.=" AND device_type='$filter_type'";
                                                                                    if($filter_dept!='') $where.=" AND department='$filter_dept'";
                                                                                    if($filter_status!='') $where.=" AND status='$filter_status'";
                                                                                    $unions[]="SELECT id,device_type,halo_id,brand,model,serial_number,date_in,date_out,username,department,team,location_local,ins_number,status,sv123_user,sv123_pass,gmail_address,gmail_pass,dgps_mail,dgps_pass,bitlocker_pass,$blid_col,bitlocker_key,remark,'$tbl' AS source_table FROM `$tbl` WHERE $where";
                                                                                    }
                                                                                    if(empty($unions)){$result=null;}else{$sql="SELECT * FROM (".implode(" UNION ALL ",$unions).") AS all_devices ORDER BY id DESC";$result=mysqli_query($conn,$sql);}
                                                                                    ?>
                                                                                    <div class="page-hdr"><div><h2>All ICT Devices</h2><p>List of all ICT devices in the system</p></div><a href="?page=device&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> New Device</a></div>
                                                                                    <div class="tbl-wrap">
                                                                                    <form method="GET" class="filter-bar"><input type="hidden" name="page" value="device"><input type="hidden" name="action" value="list">
                                                                                    <span class="filter-lbl">Filter:</span>
                                                                                    <select name="type" class="filter-select"><option value="">All Device Types</option><?php foreach(["Laptop","Desktop","Tablet","Phone","Monitor","DGPS","PowerBank"] as $t) echo "<option value='$t' ".(($_GET['type']??'')==$t?'selected':'').">$t</option>"; ?></select>
                                                                                    <select name="dept" class="filter-select"><option value="">All Departments</option><?php foreach(["GIS","ICT","HR","Finance","Liaison","Facility","OPS","Fleet","Electical","Medical","Logistic","Operation","Translator","Eore","Expat"] as $d) echo "<option value='$d' ".(($_GET['dept']??'')==$d?'selected':'').">$d</option>"; ?></select>
                                                                                    <select name="status" class="filter-select"><option value="">All Status</option><?php foreach(["Working","Spare","Broken","Pending write-off","Write-off"] as $s) echo "<option value='$s' ".(($_GET['status']??'')==$s?'selected':'').">$s</option>"; ?></select>
                                                                                    <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="tableSearch" class="filter-input" placeholder="Search Halo ID, name, serial..."></div>
                                                                                    <button type="submit" class="btn-filter">Apply</button>
                                                                                    <?php if(isset($_GET['type'])||isset($_GET['dept'])||isset($_GET['status'])): ?><a href="?page=device&action=list" class="btn-clear">Clear All</a><?php endif; ?>
                                                                                        </form>
                                                                                        <div class="tbl-scroll"><table id="deviceTable" style="min-width:4500px;">
                                                                                        <thead><tr>
                                                                                        <th class="sticky-l0" style="width:140px;background:#f4f8fd;border-right:2px solid var(--border);">Device Type</th>
                                                                                        <th style="width:120px;">Halo ID</th><th style="width:140px;">Brand</th><th style="width:150px;">Model</th>
                                                                                        <th style="width:180px;">Serial Number</th><th style="width:120px;" class="th-center">Date In</th>
                                                                                        <th style="width:120px;" class="th-center">Date Out</th><th style="width:100px;" class="th-center">Month Used</th><th style="width:100px;" class="th-center">Year Used</th><th style="width:170px;">Username</th>
                                                                                        <th style="width:140px;">Department</th><th style="width:110px;">Team</th><th style="width:160px;">Location</th>
                                                                                        <th style="width:140px;" class="th-center">INS Number</th><th style="width:100px;" class="th-center">Status</th>
                                                                                        <th class="th-blue" style="width:200px;">SV123 User</th><th class="th-blue" style="width:150px;">SV123 Pass</th>
                                                                                        <th class="th-red" style="width:210px;">Gmail Address</th><th class="th-red" style="width:150px;">Gmail Pass</th>
                                                                                        <th class="th-green" style="width:210px;">DGPS Mail</th><th class="th-green" style="width:150px;">DGPS Pass</th>
                                                                                        <th style="width:170px;">BitLocker Pass</th><th style="width:280px;">BitLocker Identifier</th><th style="width:420px;">BitLocker Key</th><th style="width:240px;">Remark</th>
                                                                                        <th class="sticky-r" style="width:90px;text-align:center;">Action</th>
                                                                                        </tr></thead><tbody>
                                                                                        <?php if($result&&mysqli_num_rows($result)>0):while($row=mysqli_fetch_assoc($result)):$st=$row['status']??'';$pill_cls=match($st){'Working'=>'pill-green','Spare'=>'pill-blue','Broken'=>'pill-red','Pending write-off'=>'pill-orange','Write-off'=>'pill-purple',default=>'pill-gray'}; ?>
                                                                                        <tr>
                                                                                        <td class="sticky-l0 td-bold" style="background:var(--surface);border-right:2px solid var(--border);"><?=htmlspecialchars($row['device_type'])?></td>
                                                                                        <td class="td-blue"><?=htmlspecialchars($row['halo_id'])?></td><td><?=htmlspecialchars($row['brand'])?></td><td><?=htmlspecialchars($row['model'])?></td>
                                                                                        <td class="td-mono"><?=htmlspecialchars($row['serial_number'])?></td><td class="td-center"><?=htmlspecialchars($row['date_in'])?></td><td class="td-center"><?=htmlspecialchars($row['date_out'])?></td>
                                                                                        <td class="td-center"><?php if(!empty($row['date_in'])){$date_in=new DateTime($row['date_in']);$now=new DateTime();$diff=$now->diff($date_in);echo $diff->m + ($diff->y * 12);}else{echo '-';}?></td>
                                                                                        <td class="td-center"><?php if(!empty($row['date_in'])){$date_in=new DateTime($row['date_in']);$now=new DateTime();$diff=$now->diff($date_in);echo number_format($diff->days / 365.25, 1);}else{echo '-';}?></td>
                                                                                        <td><?=htmlspecialchars($row['username'])?></td><td><?=htmlspecialchars($row['department'])?></td><td><?=htmlspecialchars($row['team'])?></td><td><?=htmlspecialchars($row['location_local'])?></td>
                                                                                        <td><?=htmlspecialchars($row['ins_number'])?></td><td style="text-align:center;"><span class="status-pill <?=$pill_cls?>"><?=htmlspecialchars($st)?></span></td>
                                                                                        <td class="td-blue"><?=htmlspecialchars($row['sv123_user'])?></td><td class="td-mono"><?=htmlspecialchars($row['sv123_pass']??$row['sv123_password']??'')?></td>
                                                                                        <td class="td-red"><?=htmlspecialchars($row['gmail_address'])?></td><td class="td-mono"><?=htmlspecialchars($row['gmail_pass']??$row['gmail_password']??'')?></td>
                                                                                        <td class="td-green"><?=htmlspecialchars($row['dgps_mail'])?></td><td class="td-mono"><?=htmlspecialchars($row['dgps_pass']??$row['dgps_password']??'')?></td>
                                                                                        <td class="td-mono"><?=htmlspecialchars($row['bitlocker_pass']??$row['bitlocker_password']??'')?></td>
                                                                                        <td class="td-mono"><?=htmlspecialchars($row['bitlocker_id']??'')?></td>
                                                                                        <td class="td-mono" style="font-size:.65rem;"><?=htmlspecialchars($row['bitlocker_key'])?></td>
                                                                                        <td style="font-style:italic;color:var(--muted);"><?=htmlspecialchars($row['remark'])?></td>
                                                                                        <td class="sticky-r" style="background:var(--surface);"><div class="action-btns"><a href="?page=device&action=form&id=<?=$row['id']?>&src=<?=$row['source_table']?>" class="action-btn edit"><i class="fas fa-edit"></i></a><button class="action-btn del" onclick="confirmDelete(<?=$row['id']?>,'<?=$row['source_table']?>')"><i class="fas fa-trash"></i></button></div></td>
                                                                                        </tr>
                                                                                        <?php endwhile;else: ?><tr class="empty-row"><td colspan="26"><i class="fas fa-folder-open"></i>No device data found</td></tr><?php endif; ?>
                                                                                            </tbody></table></div></div>
                                                                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                            <script>
                                                                                            function confirmDelete(id,srcTable){Swal.fire({title:'Are you sure?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#d33',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='?page=device&action=delete&id='+id+'&src='+srcTable;});}
                                                                                            const _p2=new URLSearchParams(window.location.search);
                                                                                            if(_p2.get('status')==='deleted'){Swal.fire({title:'Deleted!',text:'Data deleted successfully.',icon:'success',timer:2000,showConfirmButton:false});}
                                                                                            document.getElementById('tableSearch').addEventListener('keyup',function(){const v=this.value.toLowerCase();document.querySelectorAll('#deviceTable tbody tr:not(.empty-row)').forEach(r=>{r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';});});
                                                                                            </script>
                                                                                            
                                                                                            <?php
                                                                                            //  MISTAKE FORM
                                                                                            elseif($current_page=='mistake'&&$action=='form'):
                                                                                                $m_edit=null;$m_is_edit=false;
                                                                                                if(isset($_GET['id'])&&!empty($_GET['id'])){$mid=(int)($_GET[array_key_exists("eq_id",$_GET)?"eq_id":"id"]??0);$tbl_chk2=@mysqli_query($conn,"SHOW TABLES LIKE 'device_mistakes'");if($tbl_chk2&&mysqli_num_rows($tbl_chk2)>0){$mr=@mysqli_query($conn,"SELECT * FROM device_mistakes WHERE id='$mid'");if($mr&&($md=mysqli_fetch_assoc($mr))){$m_edit=$md;$m_is_edit=true;}}}
                                                                                                ?>
                                                                                                <div class="form-wrap">
                                                                                                <div class="form-header"><div class="form-header-icon"><i class="fas fa-exclamation-triangle"></i></div><div><h3><?=$m_is_edit?'Edit Mistake Record':'New Mistake'?></h3><p>Report and log device issues or mistakes</p></div></div>
                                                                                                <div class="form-body">
                                                                                                <form id="mistakeForm" action="save_mistake.php" method="POST">
                                                                                                <?php if($m_is_edit): ?><input type="hidden" name="mistake_id" value="<?=$m_edit['id']?>"><?php endif; ?>
                                                                                                    <div class="form-section">
                                                                                                    <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>Device &amp; User Identity</p></div>
                                                                                                    <div class="form-grid g3">
                                                                                                    <div><label class="field-lbl">Serial Number <span>*</span></label><div class="field-wrap"><i class="fas fa-barcode fi"></i><input type="text" name="serial_number" class="field" placeholder="Enter Serial" value="<?=htmlspecialchars($m_edit['serial_number']??'')?>" required></div></div>
                                                                                                    <div><label class="field-lbl">Halo ID <span>*</span></label><div class="field-wrap"><i class="fas fa-fingerprint fi"></i><input type="text" name="halo_id" class="field" placeholder="Halo ID" value="<?=htmlspecialchars($m_edit['halo_id']??'')?>" required></div></div>
                                                                                                    <div><label class="field-lbl">INS Number</label><div class="field-wrap"><i class="fas fa-id-card-clip fi"></i><input type="text" name="ins_number" class="field" placeholder="INS Number" value="<?=htmlspecialchars($m_edit['ins_number']??'')?>"></div></div>
                                                                                                    <div><label class="field-lbl">Username <span>*</span></label><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" name="username" class="field" placeholder="Username" value="<?=htmlspecialchars($m_edit['username']??'')?>" required></div></div>
                                                                                                    <div><label class="field-lbl">Department <span>*</span></label><div class="field-wrap"><i class="fas fa-building fi"></i><select name="department" class="field" required><option value="" disabled <?=!$m_is_edit?'selected':''?>>Select Department</option><?php foreach(["Finance","Operation","Fleet","HR","Liaison","GIS","Electrician","Translator","Logistic","Eore","Medical","Expat"] as $d){$sel=(isset($m_edit['department'])&&$m_edit['department']==$d)?'selected':'';echo "<option value='$d' $sel>$d</option>";}?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                                    <div><label class="field-lbl">Team <span>*</span></label><div class="field-wrap"><i class="fas fa-users-gear fi"></i><input type="text" name="team" class="field" placeholder="Team Name" value="<?=htmlspecialchars($m_edit['team']??'')?>" required></div></div>
                                                                                                    <div><label class="field-lbl">Date Turn <span>*</span></label><div class="field-wrap"><i class="fas fa-calendar-day fi"></i><input type="date" name="date_turn" class="field" value="<?=htmlspecialchars($m_edit['date_turn']??'')?>" required></div></div>
                                                                                                    </div></div>
                                                                                                    <div class="form-section">
                                                                                                    <div class="form-section-hdr"><div class="step-badge" style="background:#fee2e2;color:#b91c1c;">2</div><p>Problem Description</p></div>
                                                                                                    <div class="form-grid" style="gap:.875rem;">
                                                                                                    <div><label class="field-lbl">Problem Case <span>*</span></label><div class="field-wrap"><i class="fas fa-tools fi"></i><select name="problem_case" class="field" required><option value="" disabled <?=!$m_is_edit?'selected':''?>>Please choose a problem case</option><?php foreach(["ໃຊ້ເປັນບ່ອນແຊຣ໌ອິນເຕີເນັດ(ຮັອດສະປອດ)","ເບິ່ງຢູທູບ","ເບິ່ງໜັງ","ເຂັດແຕກ","ໜ້າຈໍແຕກ","ມີຮອຍແຕກເລັກນ້ອຍ","ແບັດເຕີຣີໝົດໄວ","ເຄື່ອງຊ້າ","ເຄື່ອງຊ້າແຮງ","ບໍ່ສາມາດໃຊ້ງານໄດ້","ບັນຫາຮູສາກ","ບັນຫາຟອມເຂົ້າສູ່ລະບົບ","ໜ້າຈໍເຮັດວຽກຜິດປົກກະຕິ (ໜ້າຈໍເພ)"] as $pc){$sel=(isset($m_edit['problem_case'])&&$m_edit['problem_case']==$pc)?'selected':'';echo "<option value='$pc' $sel>$pc</option>";}?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                                    <div><label class="field-lbl">Remark</label><div class="field-wrap ta-icon"><i class="fas fa-comment-dots fi"></i><textarea name="remark" class="field" rows="3" placeholder="Additional notes" style="padding-top:.7rem;resize:vertical;"><?=htmlspecialchars($m_edit['remark']??'')?></textarea></div></div>
                                                                                                    </div></div>
                                                                                                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i><?=$m_is_edit?'Update Mistake Record':'Save Mistake Record'?></button>
                                                                                                    </form></div></div>
                                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                    <script>
                                                                                                    document.getElementById('mistakeForm').addEventListener('submit',function(e){e.preventDefault();const form=this;const isEdit=form.querySelector('input[name="mistake_id"]')!==null;fetch('save_mistake.php',{method:'POST',body:new FormData(form)}).then(r=>{const ct=r.headers.get('content-type')||'';if(!ct.includes('json'))return r.text().then(t=>{throw new Error(t.substring(0,200));});return r.json();}).then(data=>{if(data.status==='saved'){Swal.fire({icon:'success',title:'Saved!',text:'Mistake record has been saved.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});if(!isEdit)form.reset();}else if(data.status==='updated'){Swal.fire({icon:'success',title:'Updated!',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});}else{Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});}}).catch(err=>Swal.fire({icon:'error',title:'Connection Error!',text:err.message,confirmButtonColor:'#dc2626'}));});
                                                                                                    </script>
                                                                                                    
                                                                                                    <?php
                                                                                                    //  MISTAKE LIST
                                                                                                    elseif($current_page=='mistake'&&$action=='list'):
                                                                                                        $filter_dept=isset($_GET['dept'])?mysqli_real_escape_string($conn,$_GET['dept']):'';
                                                                                                        $filter_problem=isset($_GET['problem'])?mysqli_real_escape_string($conn,$_GET['problem']):'';
                                                                                                        $where="1=1";if($filter_dept!='')$where.=" AND department='$filter_dept'";if($filter_problem!='')$where.=" AND problem_case='$filter_problem'";
                                                                                                        $tbl_check=@mysqli_query($conn,"SHOW TABLES LIKE 'device_mistakes'");
                                                                                                        if($tbl_check&&mysqli_num_rows($tbl_check)>0){$m_result=mysqli_query($conn,"SELECT * FROM device_mistakes WHERE $where ORDER BY id DESC");}else{$m_result=null;}
                                                                                                        ?>
                                                                                                        <div class="page-hdr"><div><h2>Mistake List</h2><p>All reported device issues and mistakes</p></div><a href="?page=mistake&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> New Mistake</a></div>
                                                                                                        <div class="tbl-wrap">
                                                                                                        <form method="GET" class="filter-bar"><input type="hidden" name="page" value="mistake"><input type="hidden" name="action" value="list">
                                                                                                        <span class="filter-lbl">Filter:</span>
                                                                                                        <select name="dept" class="filter-select"><option value="">All Departments</option><?php foreach(["Finance","Operation","Fleet","HR","Liaison","GIS","Electrician","Translator","Logistic","Eore","Medical","Expat"] as $d) echo "<option value='$d' ".($filter_dept==$d?'selected':'').">$d</option>"; ?></select>
                                                                                                        <select name="problem" class="filter-select"><option value="">All Problems</option><?php foreach(["ໃຊ້ເປັນບ່ອນແຊຣ໌ອິນເຕີເນັດ (ຮັອດສະປອດ)","ເບິ່ງຢູທູບ","ເບິ່ງໜັງ","ເຂັດ(ຝາຫຼັງ)ແຕກ","ໜ້າຈໍແຕກ","ມີຮອຍແຕກເລັກນ້ອຍ","ແບັດເຕີຣີໝົດໄວ","ເຄື່ອງຊ້າ","ເຄື່ອງຊ້າແຮງ","ບໍ່ສາມາດໃຊ້ງານໄດ້","ບັນຫາຮູສາກ","ບັນຫາຟອມເຂົ້າສູ່ລະບົບ","ໜ້າຈໍເຮັດວຽກຜິດປົກກະຕິ (ໜ້າຈໍເພ)"] as $pc) echo "<option value='$pc' ".($filter_problem==$pc?'selected':'').">$pc</option>"; ?></select>
                                                                                                        <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="mistakeSearch" class="filter-input" placeholder="Search serial, name, Halo ID..."></div>
                                                                                                        <button type="submit" class="btn-filter">Apply</button>
                                                                                                        <?php if($filter_dept||$filter_problem): ?><a href="?page=mistake&action=list" class="btn-clear">Clear All</a><?php endif; ?>
                                                                                                            </form>
                                                                                                            <div class="tbl-scroll"><table id="mistakeTable" style="min-width:1400px;">
                                                                                                            <thead><tr>
                                                                                                            <th class="sticky-l200 th-center" style="width:50px;background:#f4f8fd;">#</th>
                                                                                                            <th style="width:180px;">Serial Number</th><th style="width:140px;">Halo ID</th>
                                                                                                            <th style="width:120px;text-align:center;">INS Number</th><th style="width:180px;">Username</th>
                                                                                                            <th style="width:130px;">Department</th><th style="width:110px;">Team</th>
                                                                                                            <th style="width:120px;text-align:center;">Date Turn</th>
                                                                                                            <th style="width:200px;text-align:center;">Problem Case</th><th style="width:250px;">Remark</th>
                                                                                                            <th class="sticky-r" style="width:90px;text-align:center;">Action</th>
                                                                                                            </tr></thead><tbody>
                                                                                                            <?php if($m_result&&mysqli_num_rows($m_result)>0):$no=1;while($row=mysqli_fetch_assoc($m_result)): ?>
                                                                                                                <tr>
                                                                                                                <td class="sticky-l200 td-bold" style="background:var(--surface);color:var(--muted);text-align:center;"><?=$no++?></td>
                                                                                                                <td class="td-mono"><?=htmlspecialchars($row['serial_number']??'')?></td>
                                                                                                                <td class="td-blue td-bold"><?=htmlspecialchars($row['halo_id']??'')?></td>
                                                                                                                <td style="text-align:center;"><?=htmlspecialchars($row['ins_number']??'')?></td>
                                                                                                                <td class="td-bold"><?=htmlspecialchars($row['username']??'')?></td>
                                                                                                                <td><?=htmlspecialchars($row['department']??'')?></td><td><?=htmlspecialchars($row['team']??'')?></td>
                                                                                                                <td style="text-align:center;"><?=htmlspecialchars($row['date_turn']??'')?></td>
                                                                                                                <td style="text-align:center;"><span class="status-pill pill-orange"><?=htmlspecialchars($row['problem_case']??'')?></span></td>
                                                                                                                <td style="font-style:italic;color:var(--muted);"><?=htmlspecialchars($row['remark']??'')?></td>
                                                                                                                <td class="sticky-r" style="background:var(--surface);text-align:center;"><div class="action-btns"><a href="?page=mistake&action=form&id=<?=$row['id']?>" class="action-btn edit"><i class="fas fa-edit"></i></a><button class="action-btn del" onclick="deleteMistake(<?=$row['id']?>)"><i class="fas fa-trash"></i></button></div></td>
                                                                                                                </tr>
                                                                                                                <?php endwhile;else: ?><tr class="empty-row"><td colspan="11"><i class="fas fa-folder-open"></i>No mistake records found</td></tr><?php endif; ?>
                                                                                                                    </tbody></table></div></div>
                                                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                    <script>
                                                                                                                    function deleteMistake(id){Swal.fire({title:'Delete this record?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='?page=mistake&action=delete&id='+id;});}
                                                                                                                    const _pm=new URLSearchParams(window.location.search);
                                                                                                                    if(_pm.get('status')==='deleted'){Swal.fire({title:'Deleted!',text:'Record deleted.',icon:'success',timer:2000,showConfirmButton:false});window.history.replaceState({},'',location.pathname+location.search.replace(/&status=[^&]*/,''));}
                                                                                                                    document.getElementById('mistakeSearch').addEventListener('keyup',function(){const v=this.value.toLowerCase();document.querySelectorAll('#mistakeTable tbody tr:not(.empty-row)').forEach(r=>{r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';});});
                                                                                                                    </script>
                                                                                                                    
                                                                                                                    <?php
                                                                                                                    //  CARD RECORD FORM
                                                                                                                    elseif($current_page=='card_record'&&$action=='form'):
                                                                                                                        $c_edit=null;$c_is_edit=false;
                                                                                                                        if(isset($_GET['id'])&&!empty($_GET['id'])){$cid=(int)($_GET[array_key_exists("eq_id",$_GET)?"eq_id":"id"]??0);$tc=@mysqli_query($conn,"SHOW TABLES LIKE 'card_records'");if($tc&&mysqli_num_rows($tc)>0){$cr2=@mysqli_query($conn,"SELECT * FROM card_records WHERE id='$cid'");if($cr2&&($cd=mysqli_fetch_assoc($cr2))){$c_edit=$cd;$c_is_edit=true;}}}
                                                                                                                        ?>
                                                                                                                        <div class="form-wrap">
                                                                                                                        <div class="form-header"><div class="form-header-icon"><i class="fas fa-address-card"></i></div><div><h3><?=$c_is_edit?'Edit Card Record':'New Card'?></h3><p>Register and manage staff identification cards</p></div></div>
                                                                                                                        <div class="form-body">
                                                                                                                        <form id="cardForm" action="save_card.php" method="POST">
                                                                                                                        <?php if($c_is_edit): ?><input type="hidden" name="card_id" value="<?=$c_edit['id']?>"><?php endif; ?>
                                                                                                                            <div class="form-section">
                                                                                                                            <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>User &amp; Identification &nbsp;<span style="font-size:.62rem;font-weight:600;color:var(--muted);text-transform:none;letter-spacing:0;">(Enter INS to auto-fill)</span></p></div>
                                                                                                                            <div class="form-grid g3" style="grid-template-columns:1fr 1fr 1fr 1fr;">
                                                                                                                            <div><label class="field-lbl">INS Number <span>*</span></label><div class="field-wrap" style="display:flex;gap:.4rem;align-items:center;"><i class="fas fa-id-card-clip fi"></i><input type="text" id="card_ins_input" name="ins_number" class="field" placeholder="Scan or type INS" style="flex:1;" value="<?=htmlspecialchars($c_edit['ins_number']??'')?>" required><button type="button" id="card_ins_btn" style="padding:.65rem .75rem;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:.72rem;cursor:pointer;flex-shrink:0;"><i class="fas fa-search"></i></button></div><div id="card-ins-banner" style="display:none;margin-top:.4rem;padding:.4rem .7rem;border-radius:7px;font-size:.7rem;font-weight:600;"></div></div>
                                                                                                                            <div><label class="field-lbl">Username <span>*</span></label><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" id="card_username" name="username" class="field" placeholder="Full Name" value="<?=htmlspecialchars($c_edit['username']??'')?>" required></div></div>
                                                                                                                            <div><label class="field-lbl">Department <span>*</span></label><div class="field-wrap"><i class="fas fa-building fi"></i><select id="card_dept" name="department" class="field" required><option value="" disabled <?=!$c_is_edit?'selected':''?>>Select Department</option><?php foreach(["Finance","Operation","Fleet","HR","Liaison","GIS","Electrician","Translator","Logistic","Eore","Medical","Expat"] as $d){$sel=(isset($c_edit['department'])&&$c_edit['department']==$d)?'selected':'';echo "<option value='$d' $sel>$d</option>";}?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                                                            <div><label class="field-lbl">Team</label><div class="field-wrap"><i class="fas fa-users-gear fi"></i><input type="text" id="card_team" name="team" class="field" placeholder="Team Name" value="<?=htmlspecialchars($c_edit['team']??'')?>"></div></div>
                                                                                                                            </div></div>
                                                                                                                            <div class="form-section">
                                                                                                                            <div class="form-section-hdr"><div class="step-badge" style="background:#dcfce7;color:#15803d;">2</div><p>Card Details &amp; Pricing</p></div>
                                                                                                                            <div class="form-grid g3">
                                                                                                                            <div><label class="field-lbl">Card Number <span>*</span></label><div class="field-wrap"><i class="fas fa-barcode fi"></i><input type="text" name="card_number" class="field" placeholder="Enter Card No." value="<?=htmlspecialchars($c_edit['card_number']??'')?>" required></div></div>
                                                                                                                            <div><label class="field-lbl">Price <span>*</span></label><div class="field-wrap"><i class="fas fa-money-bill-wave fi"></i><select id="price" name="price" class="field" onchange="checkLimit()" required><option value="0" <?=(($c_edit['price']??'0')=='0')?'selected':''?>>-- Select Price --</option><option value="10000" <?=(($c_edit['price']??'')=='10000')?'selected':''?>>10,000 LAK</option><option value="50000" <?=(($c_edit['price']??'')=='50000')?'selected':''?>>50,000 LAK</option></select><i class="fas fa-chevron-down select-arrow"></i></div><p id="limit-warning" class="hidden" style="color:#ef4444;font-size:.65rem;font-weight:700;margin-top:.3rem;"><i class="fas fa-exclamation-circle"></i> Sorry: This INS has exceeded 60,000 LAK/month!</p></div>
                                                                                                                            <div><label class="field-lbl">Date Issue</label><div class="field-wrap"><i class="fas fa-calendar-check fi"></i><input type="date" name="date_issue" class="field" value="<?=htmlspecialchars($c_edit['date_issue']??'')?>"></div></div>
                                                                                                                            </div></div>
                                                                                                                            <div class="form-section">
                                                                                                                            <div class="form-section-hdr"><div class="step-badge" style="background:#f5f3ff;color:#7c3aed;">3</div><p>Additional Information</p></div>
                                                                                                                            <div class="cred-box"><label class="field-lbl">Remark</label><div class="field-wrap ta-icon"><i class="fas fa-comment-dots fi"></i><textarea name="remark" class="field" rows="3" placeholder="Additional notes..." style="padding-top:.7rem;resize:vertical;"><?=htmlspecialchars($c_edit['remark']??'')?></textarea></div></div>
                                                                                                                            </div>
                                                                                                                            <button type="submit" class="btn-submit"><i class="fas fa-save"></i><?=$c_is_edit?'Update Card Record':'Save Card Record'?></button>
                                                                                                                            </form></div></div>
                                                                                                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                            <script>
                                                                                                                            function checkLimit(){const ins=document.getElementById('card_ins_input').value;const price=parseInt(document.getElementById('price').value);const w=document.getElementById('limit-warning');if(ins&&price>60000){w.classList.remove('hidden');}else{w.classList.add('hidden');}}
                                                                                                                            function lookupCardINS(){const ins=document.getElementById('card_ins_input').value.trim();const banner=document.getElementById('card-ins-banner');if(!ins){banner.style.cssText='display:block;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;';banner.innerHTML='<i class="fas fa-exclamation-circle"></i> Please enter INS number.';return;}banner.style.cssText='display:block;background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;';banner.innerHTML='<i class="fas fa-spinner fa-spin"></i> Searching...';fetch('index.php?lookup_ins=1&ins_number='+encodeURIComponent(ins)).then(r=>r.json()).then(data=>{if(data.status==='found'){const d=data.data;const uname=document.getElementById('card_username');const dept=document.getElementById('card_dept');const team=document.getElementById('card_team');if(uname)uname.value=d.username||d.full_name||'';if(dept&&d.department){for(let i=0;i<dept.options.length;i++){if(dept.options[i].value===d.department){dept.selectedIndex=i;break;}}}if(team)team.value=d.team||'';banner.style.cssText='display:block;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;';banner.innerHTML='<i class="fas fa-check-circle"></i> Found: <b>'+(d.username||d.full_name||ins)+'</b>'+(d.department?' | <b>'+d.department+'</b>':'')+(d.team?' | '+d.team:'');}else{banner.style.cssText='display:block;background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;';banner.innerHTML='<i class="fas fa-exclamation-triangle"></i> '+data.msg+' â€” fill manually.';}}).catch(()=>{banner.style.cssText='display:block;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;';banner.innerHTML='Connection error.';});}
                                                                                                                            document.getElementById('card_ins_btn').addEventListener('click',lookupCardINS);
                                                                                                                            document.getElementById('card_ins_input').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();lookupCardINS();}});
                                                                                                                            let cardInsTimer;document.getElementById('card_ins_input').addEventListener('input',function(){clearTimeout(cardInsTimer);if(this.value.trim().length>=4)cardInsTimer=setTimeout(lookupCardINS,600);});
                                                                                                                            document.getElementById('cardForm').addEventListener('submit',function(e){e.preventDefault();const form=this;const isEdit=form.querySelector('input[name="card_id"]')!==null;fetch('save_card.php',{method:'POST',body:new FormData(form)}).then(r=>{const ct=r.headers.get('content-type')||'';if(!ct.includes('json'))return r.text().then(t=>{throw new Error(t.substring(0,200));});return r.json();}).then(data=>{if(data.status==='saved'){Swal.fire({icon:'success',title:'Saved!',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});if(!isEdit)form.reset();}else if(data.status==='updated'){Swal.fire({icon:'success',title:'Updated!',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});}else{Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});}}).catch(err=>Swal.fire({icon:'error',title:'Connection Error!',text:err.message,confirmButtonColor:'#dc2626'}));});
                                                                                                                            </script>
                                                                                                                            
                                                                                                                            <?php
                                                                                                                            //  CARD RECORD LIST
                                                                                                                            elseif($current_page=='card_record'&&$action=='list'):
                                                                                                                                $cf_dept=isset($_GET['dept'])?mysqli_real_escape_string($conn,$_GET['dept']):'';
                                                                                                                                $cf_ins=isset($_GET['ins'])?mysqli_real_escape_string($conn,$_GET['ins']):'';
                                                                                                                                $cf_month=isset($_GET['month'])?mysqli_real_escape_string($conn,$_GET['month']):'';
                                                                                                                                $c_where="1=1";if($cf_dept!='')$c_where.=" AND department='$cf_dept'";if($cf_ins!='')$c_where.=" AND ins_number LIKE '%$cf_ins%'";if($cf_month!=''){$ym=explode('-',$cf_month);if(count($ym)==2)$c_where.=" AND YEAR(date_issue)='".(int)$ym[0]."' AND MONTH(date_issue)='".(int)$ym[1]."'";}
                                                                                                                                $tc2=@mysqli_query($conn,"SHOW TABLES LIKE 'card_records'");
                                                                                                                                if($tc2&&mysqli_num_rows($tc2)>0){$c_res=mysqli_query($conn,"SELECT * FROM card_records WHERE $c_where ORDER BY date_issue DESC, id DESC");}else{$c_res=null;}
                                                                                                                                ?>
                                                                                                                                <div class="page-hdr"><div><h2>Card Records</h2><p>All staff card issuance records</p></div><a href="?page=card_record&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> New Record</a></div>
                                                                                                                                <div class="tbl-wrap">
                                                                                                                                <form method="GET" class="filter-bar" style="justify-content:flex-start;"><input type="hidden" name="page" value="card_record"><input type="hidden" name="action" value="list">
                                                                                                                                <span class="filter-lbl">Filter:</span>
                                                                                                                                <select name="dept" class="filter-select"><option value="">All Departments</option><?php foreach(["Finance","Operation","Fleet","HR","Liaison","GIS","Electrician","Translator","Logistic","Eore","Medical","Expat"] as $d) echo "<option value='$d' ".($cf_dept==$d?'selected':'').">$d</option>"; ?></select>
                                                                                                                                <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="cardSearch" class="filter-input" placeholder="Search name, INS, card no..."></div>
                                                                                                                                <button type="submit" class="btn-filter">Apply</button>
                                                                                                                                <?php if($cf_dept||$cf_ins||$cf_month): ?><a href="?page=card_record&action=list" class="btn-clear">Clear All</a><?php endif; ?>
                                                                                                                                    <div style="margin-left:auto;display:flex;align-items:center;gap:.5rem;"><label style="font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);white-space:nowrap;"><i class="fas fa-calendar-alt" style="color:var(--blue);margin-right:.25rem;"></i>Month</label><input type="month" name="month" class="filter-select" value="<?=htmlspecialchars($cf_month)?>" style="padding:.4rem .7rem;cursor:pointer;min-width:145px;"><?php if($cf_month){$d_parts=explode('-',$cf_month);$months_name=['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];echo '<span class="status-pill pill-blue">'.((count($d_parts)==2)?$months_name[(int)$d_parts[1]].' '.$d_parts[0]:$cf_month).'</span>';} ?></div>
                                                                                                                                    </form>
                                                                                                                                    <div class="tbl-scroll"><table id="cardTable" style="min-width:1300px;">
                                                                                                                                    <thead><tr>
                                                                                                                                    <th class="sticky-l200 th-center" style="width:50px;background:#f4f8fd;">#</th>
                                                                                                                                    <th style="width:120px;text-align:center;">INS Number</th><th style="width:190px;">Username</th>
                                                                                                                                    <th style="width:130px;">Department</th><th style="width:110px;">Team</th>
                                                                                                                                    <th style="width:160px;">Card Number</th><th style="width:140px;text-align:center;">Price (LAK)</th>
                                                                                                                                    <th style="width:130px;text-align:center;">Date Issue</th><th style="width:250px;">Remark</th>
                                                                                                                                    <th class="sticky-r" style="width:90px;text-align:center;">Action</th>
                                                                                                                                    </tr></thead><tbody>
                                                                                                                                    <?php if($c_res&&mysqli_num_rows($c_res)>0):$cno=1;while($row=mysqli_fetch_assoc($c_res)): ?>
                                                                                                                                        <tr>
                                                                                                                                        <td class="sticky-l200" style="background:var(--surface);color:var(--muted);text-align:center;font-weight:700;"><?=$cno++?></td>
                                                                                                                                        <td class="td-mono"><?=htmlspecialchars($row['ins_number']??'')?></td>
                                                                                                                                        <td class="td-bold"><?=htmlspecialchars($row['username']??'')?></td>
                                                                                                                                        <td><?=htmlspecialchars($row['department']??'')?></td><td><?=htmlspecialchars($row['team']??'')?></td>
                                                                                                                                        <td class="td-mono"><?=htmlspecialchars($row['card_number']??'')?></td>
                                                                                                                                        <td style="text-align:center;"><?php $price=(int)($row['price']??0);$pc=$price>=50000?'pill-green':($price>0?'pill-blue':'pill-gray'); ?><span class="status-pill <?=$pc?>"><?=number_format($price)?> LAK</span></td>
                                                                                                                                        <td style="text-align:center;"><?=htmlspecialchars($row['date_issue']??'')?></td>
                                                                                                                                        <td style="font-style:italic;color:var(--muted);"><?=htmlspecialchars($row['remark']??'')?></td>
                                                                                                                                        <td class="sticky-r" style="background:var(--surface);text-align:center;"><div class="action-btns"><a href="?page=card_record&action=form&id=<?=$row['id']?>" class="action-btn edit"><i class="fas fa-edit"></i></a><button class="action-btn del" onclick="deleteCard(<?=$row['id']?>)"><i class="fas fa-trash"></i></button></div></td>
                                                                                                                                        </tr>
                                                                                                                                        <?php endwhile;else: ?><tr class="empty-row"><td colspan="10"><i class="fas fa-folder-open"></i>No card records found</td></tr><?php endif; ?>
                                                                                                                                            </tbody></table></div></div>
                                                                                                                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                            <script>
                                                                                                                                            function deleteCard(id){Swal.fire({title:'Delete this record?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='?page=card_record&action=delete&id='+id;});}
                                                                                                                                            const _pc=new URLSearchParams(window.location.search);
                                                                                                                                            if(_pc.get('status')==='deleted'){Swal.fire({title:'Deleted!',icon:'success',timer:2000,showConfirmButton:false});window.history.replaceState({},'',location.pathname+location.search.replace(/&status=[^&]*/,''));}
                                                                                                                                            document.getElementById('cardSearch').addEventListener('keyup',function(){const v=this.value.toLowerCase();document.querySelectorAll('#cardTable tbody tr:not(.empty-row)').forEach(r=>{r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';});});
                                                                                                                                            </script>
                                                                                                                                            
                                                                                                                                            <?php
                                                                                                                                            
                                                                                                                                            
                                                                                                                                            //  EMPLOYEES FORM
                                                                                                                                            elseif($current_page=='employees'&&$action=='form'):
                                                                                                                                                $emp_edit=null;$emp_is_edit=false;
                                                                                                                                                if(isset($_GET['id'])&&!empty($_GET['id'])){$eid=(int)($_GET[array_key_exists("eq_id",$_GET)?"eq_id":"id"]??0);$tc=@mysqli_query($conn,"SHOW TABLES LIKE 'employees'");if($tc&&mysqli_num_rows($tc)>0){$er=@mysqli_query($conn,"SELECT * FROM employees WHERE id='$eid'");if($er&&($ed=mysqli_fetch_assoc($er))){$emp_edit=$ed;$emp_is_edit=true;}}}
                                                                                                                                                ?>
                                                                                                                                                <div class="form-wrap">
                                                                                                                                                <div class="form-header"><div class="form-header-icon"><i class="fas fa-user-plus"></i></div><div><h3><?=$emp_is_edit?'Edit Employee':'New User'?></h3><p>Register and manage employee information</p></div></div>
                                                                                                                                                <div class="form-body">
                                                                                                                                                <form id="employeeForm" action="save_employee.php" method="POST">
                                                                                                                                                <?php if($emp_is_edit): ?><input type="hidden" name="employee_id" value="<?=$emp_edit['id']?>"><?php endif; ?>
                                                                                                                                                    <div class="form-section">
                                                                                                                                                    <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>Employee Information</p></div>
                                                                                                                                                    <div class="form-grid g3">
                                                                                                                                                    <div><label class="field-lbl">Username <span>*</span></label><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" name="username" class="field" placeholder="Full Name" value="<?=htmlspecialchars($emp_edit['username']??'')?>" required></div></div>
                                                                                                                                                    <div><label class="field-lbl">INS Number <span>*</span></label><div class="field-wrap"><i class="fas fa-id-card fi"></i><input type="text" name="ins_number" class="field" placeholder="INS Number" value="<?=htmlspecialchars($emp_edit['ins_number']??'')?>" required></div></div>
                                                                                                                                                    <div><label class="field-lbl">Department <span>*</span></label><div class="field-wrap"><i class="fas fa-sitemap fi"></i><select name="department" class="field" required><option value="" disabled <?=!$emp_is_edit?'selected':''?>>Select Department</option><?php foreach(["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Medical","Expat"] as $d){$sel=(isset($emp_edit['department'])&&$emp_edit['department']==$d)?'selected':'';echo "<option value='$d' $sel>$d</option>";}?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                                                                                    <div><label class="field-lbl">Team</label><div class="field-wrap"><i class="fas fa-users fi"></i><input type="text" name="team" class="field" placeholder="Team Name" value="<?=htmlspecialchars($emp_edit['team']??'')?>"></div></div>
                                                                                                                                                    <div><label class="field-lbl">Location</label><div class="field-wrap"><i class="fas fa-map-marker-alt fi"></i><input type="text" name="location" class="field" placeholder="Location / Local" value="<?=htmlspecialchars($emp_edit['location']??'')?>"></div></div>
                                                                                                                                                    <div><label class="field-lbl">Phone</label><div class="field-wrap"><i class="fas fa-phone-alt fi"></i><input type="text" name="phone" class="field" placeholder="020 XXXXXXXX" value="<?=htmlspecialchars($emp_edit['phone']??'')?>"></div></div>
                                                                                                                                                    </div></div>
                                                                                                                                                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i><?=$emp_is_edit?'Update Employee':'Save Employee'?></button>
                                                                                                                                                    </form></div></div>
                                                                                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                    <script>
                                                                                                                                                    document.getElementById('employeeForm').addEventListener('submit',function(e){e.preventDefault();const form=this;const isEdit=form.querySelector('input[name="employee_id"]')!==null;fetch('save_employee.php',{method:'POST',body:new FormData(form)}).then(r=>{const ct=r.headers.get('content-type')||'';if(!ct.includes('json'))return r.text().then(t=>{throw new Error(t.substring(0,200));});return r.json();}).then(data=>{if(data.status==='saved'){Swal.fire({icon:'success',title:'Saved!',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});if(!isEdit)form.reset();}else if(data.status==='updated'){Swal.fire({icon:'success',title:'Updated!',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});}else{Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});}}).catch(err=>Swal.fire({icon:'error',title:'Connection Error!',text:err.message,confirmButtonColor:'#dc2626'}));});
                                                                                                                                                    </script>
                                                                                                                                                    
                                                                                                                                                    <?php
                                                                                                                                                    //  EMPLOYEES LIST
                                                                                                                                                    elseif($current_page=='employees'&&$action=='list'):
                                                                                                                                                        $ef_dept=isset($_GET['dept'])?mysqli_real_escape_string($conn,$_GET['dept']):'';
                                                                                                                                                        $ef_loc=isset($_GET['loc'])?mysqli_real_escape_string($conn,$_GET['loc']):'';
                                                                                                                                                        $e_where="1=1";if($ef_dept!='')$e_where.=" AND department='$ef_dept'";if($ef_loc!='')$e_where.=" AND location LIKE '%$ef_loc%'";
                                                                                                                                                        $te=@mysqli_query($conn,"SHOW TABLES LIKE 'employees'");
                                                                                                                                                        if($te&&mysqli_num_rows($te)>0){$e_res=mysqli_query($conn,"SELECT * FROM employees WHERE $e_where ORDER BY id DESC");}else{$e_res=null;}
                                                                                                                                                        ?>
                                                                                                                                                        <div class="page-hdr"><div><h2>User List</h2><p>All registered employees</p></div><a href="?page=employees&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> New User</a></div>
                                                                                                                                                        <div class="tbl-wrap">
                                                                                                                                                        <form method="GET" class="filter-bar"><input type="hidden" name="page" value="employees"><input type="hidden" name="action" value="list">
                                                                                                                                                        <span class="filter-lbl">Filter:</span>
                                                                                                                                                        <select name="dept" class="filter-select"><option value="">All Departments</option><?php foreach(["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Medical","Expat"] as $d) echo "<option value='$d' ".($ef_dept==$d?'selected':'').">$d</option>"; ?></select>
                                                                                                                                                        <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="empSearch" class="filter-input" placeholder="Search name, INS, team..."></div>
                                                                                                                                                        <button type="submit" class="btn-filter">Apply</button>
                                                                                                                                                        <?php if($ef_dept||$ef_loc): ?><a href="?page=employees&action=list" class="btn-clear">Clear All</a><?php endif; ?>
                                                                                                                                                            </form>
                                                                                                                                                            <div class="tbl-scroll"><table id="empTable" style="min-width:900px;">
                                                                                                                                                            <thead><tr>
                                                                                                                                                            <th class="sticky-l200 th-center" style="width:50px;background:#f4f8fd;">#</th>
                                                                                                                                                            <th style="width:200px;">Username</th><th style="width:130px;">INS Number</th>
                                                                                                                                                            <th style="width:140px;">Department</th><th style="width:120px;">Team</th>
                                                                                                                                                            <th style="width:180px;">Location</th><th style="width:140px;">Phone</th>
                                                                                                                                                            <th class="sticky-r" style="width:90px;text-align:center;">Action</th>
                                                                                                                                                            </tr></thead><tbody>
                                                                                                                                                            <?php if($e_res&&mysqli_num_rows($e_res)>0):$eno=1;while($row=mysqli_fetch_assoc($e_res)): ?>
                                                                                                                                                                <tr>
                                                                                                                                                                <td class="sticky-l200" style="background:var(--surface);color:var(--muted);text-align:center;font-weight:700;"><?=$eno++?></td>
                                                                                                                                                                <td class="td-bold"><?=htmlspecialchars($row['username']??'')?></td>
                                                                                                                                                                <td class="td-mono"><?=htmlspecialchars($row['ins_number']??'')?></td>
                                                                                                                                                                <td><?=htmlspecialchars($row['department']??'')?></td><td><?=htmlspecialchars($row['team']??'')?></td>
                                                                                                                                                                <td><?=htmlspecialchars($row['location']??'')?></td><td><?=htmlspecialchars($row['phone']??'')?></td>
                                                                                                                                                                <td class="sticky-r" style="background:var(--surface);text-align:center;"><div class="action-btns"><a href="?page=employees&action=form&id=<?=$row['id']?>" class="action-btn edit"><i class="fas fa-edit"></i></a><button class="action-btn del" onclick="deleteEmployee(<?=$row['id']?>)"><i class="fas fa-trash"></i></button></div></td>
                                                                                                                                                                </tr>
                                                                                                                                                                <?php endwhile;else: ?><tr class="empty-row"><td colspan="8"><i class="fas fa-users"></i>No employees found</td></tr><?php endif; ?>
                                                                                                                                                                    </tbody></table></div></div>
                                                                                                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                    <script>
                                                                                                                                                                    function deleteEmployee(id){Swal.fire({title:'Delete this employee?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='?page=employees&action=delete&id='+id;});}
                                                                                                                                                                    const _pe=new URLSearchParams(window.location.search);
                                                                                                                                                                    if(_pe.get('status')==='deleted'){Swal.fire({title:'Deleted!',icon:'success',timer:2000,showConfirmButton:false});window.history.replaceState({},'',location.pathname+location.search.replace(/&status=[^&]*/,''));}
                                                                                                                                                                    document.getElementById('empSearch').addEventListener('keyup',function(){const v=this.value.toLowerCase();document.querySelectorAll('#empTable tbody tr:not(.empty-row)').forEach(r=>{r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';});});
                                                                                                                                                                    </script>
                                                                                                                                                                    
                                                                                                                                                                    <?php
                                                                                                                                                                    //  INTERNET FORM  
                                                                                                                                                                    elseif($current_page=='internet'&&$action=='form'):
                                                                                                                                                                        $inet_edit=null;$inet_is_edit=false;
                                                                                                                                                                        if(isset($_GET['id'])&&!empty($_GET['id'])){
                                                                                                                                                                        $iid=(int)($_GET[array_key_exists("eq_id",$_GET)?"eq_id":"id"]??0);
                                                                                                                                                                        $ir=@mysqli_query($conn,"SELECT * FROM internet_records WHERE id='$iid'");
                                                                                                                                                                        if($ir&&($id_row=mysqli_fetch_assoc($ir))){$inet_edit=$id_row;$inet_is_edit=true;}
                                                                                                                                                                        }
                                                                                                                                                                        ?>
                                                                                                                                                                        <div class="form-wrap">
                                                                                                                                                                        <div class="form-header">
                                                                                                                                                                        <div class="form-header-icon"><i class="fas fa-wifi"></i></div>
                                                                                                                                                                        <div>
                                                                                                                                                                        <h3><?=$inet_is_edit?'Edit Internet Record':'Add New Internet'?></h3>
                                                                                                                                                                        <p>Record internet contract details: package, price and documents</p>
                                                                                                                                                                        </div>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="form-body">
                                                                                                                                                                        <form id="internetForm" action="save_internet.php" method="POST" enctype="multipart/form-data">
                                                                                                                                                                        <?php if($inet_is_edit): ?>
                                                                                                                                                                            <input type="hidden" name="internet_id" value="<?=$inet_edit['id']?>">
                                                                                                                                                                            <?php endif; ?>
                                                                                                                                                                            
                                                                                                                                                                            <!-- ======== SECTION 1 : Internet & Provider Details ======== -->
                                                                                                                                                                            <div class="form-section">
                                                                                                                                                                            <div class="form-section-hdr">
                                                                                                                                                                            <div class="step-badge" style="background:#ccfbf1;color:#0f766e;">1</div>
                                                                                                                                                                            <p>Internet &amp; Provider Details</p>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="form-grid g3">
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Internet Local <span>*</span></label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-map-marker-alt fi"></i>
                                                                                                                                                                            <input type="text" name="internet_local" class="field"
                                                                                                                                                                            placeholder="e.g. Vientiane Office, Pakse Base"
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['internet_local']??'')?>" required>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Internet Type <span>*</span></label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-network-wired fi"></i>
                                                                                                                                                                            <select name="internet_type" class="field" required>
                                                                                                                                                                            <option value="" disabled <?=!$inet_is_edit?'selected':''?>>Select Type</option>
                                                                                                                                                                            <?php foreach(["Fiber Optic Over Leased Line","Fiber Optic to home","Other"] as $t):
                                                                                                                                                                                $sel=(isset($inet_edit['internet_type'])&&$inet_edit['internet_type']==$t)?'selected':'';
                                                                                                                                                                                echo "<option value='$t' $sel>$t</option>";
                                                                                                                                                                            endforeach; ?>
                                                                                                                                                                            </select>
                                                                                                                                                                            <i class="fas fa-chevron-down select-arrow"></i>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Package Name</label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-box-open fi"></i>
                                                                                                                                                                            <input type="text" name="package" class="field"
                                                                                                                                                                            placeholder="e.g. Business 100Mbps, Unlimited"
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['package']??'')?>">
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            
                                                                                                                                                                            <!-- ======== SECTION 2 : Pricing & Contract Period ======== -->
                                                                                                                                                                            <div class="form-section">
                                                                                                                                                                            <div class="form-section-hdr">
                                                                                                                                                                            <div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">2</div>
                                                                                                                                                                            <p>Pricing &amp; Contract Period</p>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="form-grid g4">
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Price (LAK) <span>*</span></label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-money-bill-wave fi"></i>
                                                                                                                                                                            <input type="number" name="price" class="field"
                                                                                                                                                                            placeholder="0" min="0" step="1000"
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['price']??'0')?>" required>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Start Date <span>*</span></label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-calendar-plus fi"></i>
                                                                                                                                                                            <input type="date" name="start_date" class="field"
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['start_date']??'')?>" required>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">End Date</label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-calendar-minus fi"></i>
                                                                                                                                                                            <input type="date" name="end_date" class="field"
                                                                                                                                                                            id="inet_end_date"
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['end_date']??'')?>">
                                                                                                                                                                            </div>
                                                                                                                                                                            <div id="expiry-warn" style="display:none;margin-top:.35rem;padding:.35rem .65rem;border-radius:7px;font-size:.68rem;font-weight:600;"></div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Days Remaining</label>
                                                                                                                                                                            <div id="days-remaining-box" style="padding:.65rem 1rem;background:#f8fafd;border:1.5px solid var(--border);border-radius:var(--r-sm);font-size:.82rem;font-weight:700;color:var(--muted);display:flex;align-items:center;gap:.4rem;min-height:42px;">
                                                                                                                                                                            <i class="fas fa-clock" style="font-size:.7rem;"></i>
                                                                                                                                                                            <!-- ✅ ແກ້ຈາກ â€" ເປັນ &mdash; -->
                                                                                                                                                                            <span id="days-remaining-val">&mdash;</span>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            
                                                                                                                                                                            <!-- ======== SECTION 3 : Document & Reference ======== -->
                                                                                                                                                                            <div class="form-section">
                                                                                                                                                                            <div class="form-section-hdr">
                                                                                                                                                                            <div class="step-badge" style="background:#dcfce7;color:#15803d;">3</div>
                                                                                                                                                                            <p>Document &amp; Reference</p>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="form-grid g2">
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Document Local
                                                                                                                                                                            <span style="font-size:.6rem;font-weight:500;color:var(--muted);">(Browse file from device)</span>
                                                                                                                                                                            </label>
                                                                                                                                                                            <!-- Hidden real file input -->
                                                                                                                                                                            <input type="file" id="doc_file_input" name="doc_file"
                                                                                                                                                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.zip"
                                                                                                                                                                            style="display:none;" onchange="handleDocFile(this)">
                                                                                                                                                                            <div style="display:flex;gap:.45rem;align-items:center;">
                                                                                                                                                                            <div class="field-wrap" style="flex:1;">
                                                                                                                                                                            <i class="fas fa-folder-open fi"></i>
                                                                                                                                                                            <input type="text" name="document_local" id="doc_local_field" class="field"
                                                                                                                                                                            placeholder="Click Browse to select file or type path manually"
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['document_local']??'')?>">
                                                                                                                                                                            </div>
                                                                                                                                                                            <button type="button" onclick="document.getElementById('doc_file_input').click()"
                                                                                                                                                                            style="padding:.65rem .9rem;background:var(--blue);color:#fff;border:none;border-radius:8px;
                                font-size:.72rem;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0;transition:all .15s;"
                                                                                                                                                                            onmouseover="this.style.background='var(--navy)'"
                                                                                                                                                                            onmouseout="this.style.background='var(--blue)'">
                                                                                                                                                                            <i class="fas fa-folder-open"></i> Browse
                                                                                                                                                                            </button>
                                                                                                                                                                            </div>
                                                                                                                                                                            <!-- File preview badge -->
                                                                                                                                                                            <div id="doc_file_badge" style="display:none;margin-top:.45rem;padding:.4rem .75rem;
                            background:#f0fdf4;border:1px solid #bbf7d0;border-radius:7px;
                            font-size:.7rem;font-weight:600;color:#15803d;
                            align-items:center;gap:.5rem;">
                                                                                                                                                                            <i class="fas fa-file-check"></i>
                                                                                                                                                                            <!-- ✅ ແກ້ຈາກ â€" ເປັນ &mdash; -->
                                                                                                                                                                            <span id="doc_file_name">&mdash;</span>
                                                                                                                                                                            <span id="doc_file_size" style="color:var(--muted);font-weight:400;"></span>
                                                                                                                                                                            <button type="button" onclick="clearDocFile()"
                                                                                                                                                                            style="margin-left:auto;background:none;border:none;color:#b91c1c;cursor:pointer;font-size:.72rem;">
                                                                                                                                                                            <i class="fas fa-times"></i>
                                                                                                                                                                            </button>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div>
                                                                                                                                                                            <label class="field-lbl">Document Link
                                                                                                                                                                            <span style="font-size:.6rem;font-weight:500;color:var(--muted);">(URL / Google Drive / Auto-filled after Browse)</span>
                                                                                                                                                                            </label>
                                                                                                                                                                            <div class="field-wrap">
                                                                                                                                                                            <i class="fas fa-link fi"></i>
                                                                                                                                                                            <input type="text" name="document_link" id="doc_link_field" class="field"
                                                                                                                                                                            placeholder="Auto-filled from Browse or paste https://drive.google.com/..."
                                                                                                                                                                            value="<?=htmlspecialchars($inet_edit['document_link']??'')?>">
                                                                                                                                                                            </div>
                                                                                                                                                                            <div id="doc_link_preview" style="display:none;margin-top:.4rem;"></div>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            
                                                                                                                                                                            <!-- ======== SECTION 4 : Additional Notes ======== -->
                                                                                                                                                                            <div class="form-section">
                                                                                                                                                                            <div class="form-section-hdr">
                                                                                                                                                                            <div class="step-badge" style="background:#f5f3ff;color:#7c3aed;">4</div>
                                                                                                                                                                            <p>Additional Notes</p>
                                                                                                                                                                            </div>
                                                                                                                                                                            <div class="field-wrap ta-icon">
                                                                                                                                                                            <i class="fas fa-comment-dots fi"></i>
                                                                                                                                                                            <textarea name="remark" class="field" rows="3"
                                                                                                                                                                            placeholder="Additional notes, ISP contact, account number, etc..."
                                                                                                                                                                            style="padding-top:.7rem;resize:vertical;"><?=htmlspecialchars($inet_edit['remark']??'')?></textarea>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            
                                                                                                                                                                            <button type="submit" class="btn-submit">
                                                                                                                                                                            <i class="fas fa-save"></i>
                                                                                                                                                                            <?=$inet_is_edit?'Update Internet Record':'Save Internet Record'?>
                                                                                                                                                                            </button>
                                                                                                                                                                            </form>
                                                                                                                                                                            </div>
                                                                                                                                                                            </div>
                                                                                                                                                                            
                                                                                                                                                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                            <script>
                                                                                                                                                                            // ======== Days Remaining Calculator ========
                                                                                                                                                                            function calcDays(){
                                                                                                                                                                            const endVal = document.getElementById('inet_end_date').value;
                                                                                                                                                                            const box    = document.getElementById('days-remaining-val');
                                                                                                                                                                            const warn   = document.getElementById('expiry-warn');
                                                                                                                                                                            
                                                                                                                                                                            // ✅ ແກ້ຈາກ 'â€"' ເປັນ '\u2014' (em dash)
                                                                                                                                                                            if(!endVal){ box.textContent = '\u2014'; box.style.color='var(--muted)'; warn.style.display='none'; return; }
                                                                                                                                                                            
                                                                                                                                                                            const today = new Date(); today.setHours(0,0,0,0);
                                                                                                                                                                            const end   = new Date(endVal); end.setHours(0,0,0,0);
                                                                                                                                                                            const diff  = Math.round((end - today) / 86400000);
                                                                                                                                                                            
                                                                                                                                                                            if(diff < 0){
                                                                                                                                                                            box.textContent = 'Expired ('+Math.abs(diff)+'d ago)';
                                                                                                                                                                            box.style.color = '#b91c1c';
                                                                                                                                                                            warn.style.cssText = 'display:block;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;';
                                                                                                                                                                            warn.innerHTML = '<i class="fas fa-times-circle"></i> This contract has expired!';
                                                                                                                                                                            } else if(diff <= 30){
                                                                                                                                                                            box.textContent = diff+' days left';
                                                                                                                                                                            box.style.color = '#c2410c';
                                                                                                                                                                            warn.style.cssText = 'display:block;background:#ffedd5;color:#c2410c;border:1px solid #fed7aa;';
                                                                                                                                                                            warn.innerHTML = '<i class="fas fa-triangle-exclamation"></i> <b>'+diff+' days</b> remaining please renew the contract!';
                                                                                                                                                                            } else {
                                                                                                                                                                            box.textContent = diff+' days left';
                                                                                                                                                                            box.style.color = '#15803d';
                                                                                                                                                                            warn.style.display = 'none';
                                                                                                                                                                            }
                                                                                                                                                                            }
                                                                                                                                                                            document.getElementById('inet_end_date').addEventListener('change', calcDays);
                                                                                                                                                                            calcDays();
                                                                                                                                                                            
                                                                                                                                                                            // ======== File Browse ========
                                                                                                                                                                            var _selectedFile = null;
                                                                                                                                                                            function handleDocFile(input){
                                                                                                                                                                            if(!input.files||!input.files[0]) return;
                                                                                                                                                                            var file   = input.files[0];
                                                                                                                                                                            _selectedFile = file;
                                                                                                                                                                            var fname  = file.name;
                                                                                                                                                                            var fsize  = file.size>1048576?(file.size/1048576).toFixed(1)+' MB':(file.size/1024).toFixed(0)+' KB';
                                                                                                                                                                            document.getElementById('doc_local_field').value = fname;
                                                                                                                                                                            var uploadPath = 'uploads/internet/' + fname;
                                                                                                                                                                            document.getElementById('doc_link_field').value  = uploadPath;
                                                                                                                                                                            var badge = document.getElementById('doc_file_badge');
                                                                                                                                                                            badge.style.display = 'flex';
                                                                                                                                                                            document.getElementById('doc_file_name').textContent = fname;
                                                                                                                                                                            document.getElementById('doc_file_size').textContent = '('+fsize+')';
                                                                                                                                                                            var preview = document.getElementById('doc_link_preview');
                                                                                                                                                                            preview.style.cssText = 'display:flex;align-items:center;gap:.4rem;padding:.35rem .65rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:7px;font-size:.68rem;';
                                                                                                                                                                            preview.innerHTML = '<i class="fas fa-check-circle" style="color:#2563eb;"></i><span style="color:#1d4ed8;font-weight:600;">Auto link: </span><span style="color:var(--muted);font-family:Geist Mono,monospace;font-size:.65rem;">'+uploadPath+'</span>';
                                                                                                                                                                            }
                                                                                                                                                                            
                                                                                                                                                                            function clearDocFile(){
                                                                                                                                                                            _selectedFile = null;
                                                                                                                                                                            document.getElementById('doc_file_input').value    = '';
                                                                                                                                                                            document.getElementById('doc_local_field').value   = '';
                                                                                                                                                                            document.getElementById('doc_link_field').value    = '';
                                                                                                                                                                            document.getElementById('doc_file_badge').style.display  = 'none';
                                                                                                                                                                            document.getElementById('doc_link_preview').style.display = 'none';
                                                                                                                                                                            }
                                                                                                                                                                            
                                                                                                                                                                            document.getElementById('doc_local_field').addEventListener('input', function(){
                                                                                                                                                                            if(_selectedFile){ _selectedFile=null; document.getElementById('doc_file_badge').style.display='none'; }
                                                                                                                                                                            });
                                                                                                                                                                            
                                                                                                                                                                            // ======== Form Submit ========
                                                                                                                                                                            document.getElementById('internetForm').addEventListener('submit', function(e){
                                                                                                                                                                            e.preventDefault();
                                                                                                                                                                            const form   = this;
                                                                                                                                                                            const isEdit = form.querySelector('input[name="internet_id"]') !== null;
                                                                                                                                                                            fetch('save_internet.php', {method:'POST', body:new FormData(form)})
                                                                                                                                                                            .then(r=>{
                                                                                                                                                                            const ct = r.headers.get('content-type')||'';
                                                                                                                                                                            if(!ct.includes('json')) return r.text().then(t=>{ throw new Error(t.substring(0,200)); });
                                                                                                                                                                            return r.json();
                                                                                                                                                                            })
                                                                                                                                                                            .then(data=>{
                                                                                                                                                                            if(data.status==='saved'){
                                                                                                                                                                            Swal.fire({icon:'success',title:'Saved!',text:'Internet record has been saved.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});
                                                                                                                                                                            if(!isEdit) form.reset();
                                                                                                                                                                            } else if(data.status==='updated'){
                                                                                                                                                                            Swal.fire({icon:'success',title:'Updated!',text:'Internet record has been updated.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});
                                                                                                                                                                            } else {
                                                                                                                                                                            Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});
                                                                                                                                                                            }
                                                                                                                                                                            })
                                                                                                                                                                            .catch(err => Swal.fire({icon:'error',title:'Connection Error!',text:err.message||'Please try again.',confirmButtonColor:'#dc2626'}));
                                                                                                                                                                            });
                                                                                                                                                                            </script>
                                                                                                                                                                            <?php
                                                                                                                                                                            //  INTERNET LIST  
                                                                                                                                                                            elseif($current_page=='internet'&&$action=='list'):
                                                                                                                                                                                $if_type   = isset($_GET['filter_type'])   ? mysqli_real_escape_string($conn,$_GET['filter_type'])   : '';
                                                                                                                                                                                $if_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
                                                                                                                                                                                $today_db  = date('Y-m-d');
                                                                                                                                                                                $i_where   = "1=1";
                                                                                                                                                                                if($if_type!='') $i_where.=" AND internet_type='$if_type'";
                                                                                                                                                                                // status filter applied in PHP after fetch
                                                                                                                                                                                $i_res = mysqli_query($conn,"SELECT * FROM internet_records WHERE $i_where ORDER BY end_date ASC, id DESC");
                                                                                                                                                                                $all_types=["Fiber Optic","4G/LTE","ADSL","VSAT/Satellite","Dedicated Leased Line","MPLS","WiFi Hotspot","Other"];
                                                                                                                                                                                ?>
                                                                                                                                                                                <div class="page-hdr">
                                                                                                                                                                                <div><h2>All Internet</h2><p>All internet contracts, packages and records</p></div>
                                                                                                                                                                                <a href="?page=internet&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> Add New</a>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="tbl-wrap">
                                                                                                                                                                                <form method="GET" class="filter-bar">
                                                                                                                                                                                <input type="hidden" name="page" value="internet"><input type="hidden" name="action" value="list">
                                                                                                                                                                                <span class="filter-lbl">Filter:</span>
                                                                                                                                                                                <select name="filter_type" class="filter-select"><option value="">All Types</option><?php foreach($all_types as $t) echo "<option value='$t' ".($if_type==$t?'selected':'').">$t</option>"; ?></select>
                                                                                                                                                                                <select name="filter_status" class="filter-select">
                                                                                                                                                                                <option value="">All Status</option>
                                                                                                                                                                                <option value="active" <?=$if_status=='active'?'selected':''?>>Active</option>
                                                                                                                                                                                <option value="expiring" <?=$if_status=='expiring'?'selected':''?>>Expiring (â‰¤30d)</option>
                                                                                                                                                                                <option value="expired" <?=$if_status=='expired'?'selected':''?>>Expired</option>
                                                                                                                                                                                </select>
                                                                                                                                                                                <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="inetSearch" class="filter-input" placeholder="Search local, type, package..."></div>
                                                                                                                                                                                <button type="submit" class="btn-filter">Apply</button>
                                                                                                                                                                                <?php if($if_type||$if_status): ?><a href="?page=internet&action=list" class="btn-clear">Clear All</a><?php endif; ?>
                                                                                                                                                                                    </form>
                                                                                                                                                                                    <div class="tbl-scroll">
                                                                                                                                                                                    <table id="inetTable" style="min-width:1200px;">
                                                                                                                                                                                    <thead><tr>
                                                                                                                                                                                    <th style="width:50px;">#</th>
                                                                                                                                                                                    <th style="width:200px;">Internet Local</th>
                                                                                                                                                                                    <th style="width:150px;">Internet Type</th>
                                                                                                                                                                                    <th style="width:200px;">Package</th>
                                                                                                                                                                                    <th style="width:140px;text-align:right;">Price (LAK)</th>
                                                                                                                                                                                    <th style="width:120px;text-align:center;">Start Date</th>
                                                                                                                                                                                    <th style="width:120px;text-align:center;">End Date</th>
                                                                                                                                                                                    <th style="width:120px;text-align:center;">Days Left</th>
                                                                                                                                                                                    <th style="width:110px;text-align:center;">Status</th>
                                                                                                                                                                                    <th style="width:200px;">Document Local</th>
                                                                                                                                                                                    <th style="width:220px;">Document Link</th>
                                                                                                                                                                                    <th style="width:200px;">Remark</th>
                                                                                                                                                                                    <th class="sticky-r" style="width:90px;text-align:center;">Action</th>
                                                                                                                                                                                    </tr></thead>
                                                                                                                                                                                    <tbody>
                                                                                                                                                                                    <?php
                                                                                                                                                                                    $inet_no2=1;
                                                                                                                                                                                    $has_rows=false;
                                                                                                                                                                                    if($i_res&&mysqli_num_rows($i_res)>0):
                                                                                                                                                                                        while($ir=mysqli_fetch_assoc($i_res)):
                                                                                                                                                                                            $end_d=$ir['end_date']??'';
                                                                                                                                                                                            if(empty($end_d)){$diff_d=999;$status_lbl='Active';$pill='pill-teal';}
                                                                                                                                                                                            else{$diff_d=round((strtotime($end_d)-strtotime($today_db))/86400);if($diff_d<0){$status_lbl='Expired';$pill='pill-red';}elseif($diff_d<=30){$status_lbl='Expiring';$pill='pill-expiring';}else{$status_lbl='Active';$pill='pill-teal';}}
                                                                                                                                                                                            // Apply status filter
                                                                                                                                                                                            if($if_status=='active'&&!in_array($status_lbl,['Active'])) continue;
                                                                                                                                                                                            if($if_status=='expiring'&&$status_lbl!='Expiring') continue;
                                                                                                                                                                                            if($if_status=='expired'&&$status_lbl!='Expired') continue;
                                                                                                                                                                                            $has_rows=true;
                                                                                                                                                                                            ?>
                                                                                                                                                                                            <tr>
                                                                                                                                                                                            <td style="color:var(--muted);font-weight:700;"><?=$inet_no2++?></td>
                                                                                                                                                                                            <td class="td-bold"><?=htmlspecialchars($ir['internet_local']??'')?></td>
                                                                                                                                                                                            <td><span class="status-pill pill-blue"><?=htmlspecialchars($ir['internet_type']??'')?></span></td>
                                                                                                                                                                                            <td><?=htmlspecialchars($ir['package']??'')?></td>
                                                                                                                                                                                            <td style="text-align:right;font-family:'Geist Mono',monospace;font-size:.78rem;font-weight:700;color:var(--navy);"><?=number_format((float)($ir['price']??0))?></td>
                                                                                                                                                                                            <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($ir['start_date']??'')?></td>
                                                                                                                                                                                            <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($end_d)?></td>
                                                                                                                                                                                            <td style="text-align:center;">
                                                                                                                                                                                            <?php if(empty($end_d)): ?><span style="color:var(--muted);font-size:.72rem;">â€”</span>
                                                                                                                                                                                                <?php elseif($diff_d<0): ?><span style="color:#b91c1c;font-weight:700;font-size:.78rem;"><?=abs($diff_d)?>d ago</span>
                                                                                                                                                                                                    <?php elseif($diff_d<=30): ?>
                                                                                                                                                                                                        <span style="display:inline-flex;align-items:center;gap:.3rem;background:#ffedd5;color:#c2410c;border:1px solid #fed7aa;border-radius:8px;padding:.25rem .55rem;font-weight:800;font-size:.78rem;" class="inet-countdown-cell" data-days="<?=$diff_d?>">
                                                                                                                                                                                                        <i class="fas fa-hourglass-half" style="font-size:.65rem;animation:spin-slow 3s linear infinite;"></i>
                                                                                                                                                                                                        <span class="inet-cntdwn-val"><?=$diff_d?></span><span style="font-size:.6rem;font-weight:600;">days</span>
                                                                                                                                                                                                        </span>
                                                                                                                                                                                                        <?php else: ?><span style="color:#15803d;font-weight:600;font-size:.78rem;"><?=$diff_d?>d</span>
                                                                                                                                                                                                            <?php endif; ?>
                                                                                                                                                                                                            </td>
                                                                                                                                                                                                            <td style="text-align:center;">
                                                                                                                                                                                                            <?php if($status_lbl==='Expiring'): ?>
                                                                                                                                                                                                                <span class="status-pill pill-expiring"><i class="fas fa-triangle-exclamation" style="font-size:.6rem;"></i> Expiring</span>
                                                                                                                                                                                                                <?php else: ?>
                                                                                                                                                                                                                    <span class="status-pill <?=$pill?>"><?=$status_lbl?></span>
                                                                                                                                                                                                                    <?php endif; ?>
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                    <td style="font-size:.72rem;color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;" title="<?=htmlspecialchars($ir['document_local']??'')?>"><?=htmlspecialchars($ir['document_local']??'')?></td>
                                                                                                                                                                                                                    <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;">
                                                                                                                                                                                                                    <?php if(!empty($ir['document_link'])): ?>
                                                                                                                                                                                                                        <a href="<?=htmlspecialchars($ir['document_link'])?>" target="_blank" style="color:var(--blue);font-size:.73rem;text-decoration:none;" title="<?=htmlspecialchars($ir['document_link'])?>"><i class="fas fa-external-link-alt"></i> <?=htmlspecialchars(substr($ir['document_link'],0,30)).(strlen($ir['document_link'])>30?'...':'')?></a>
                                                                                                                                                                                                                        <?php else: ?><span style="color:var(--muted);font-size:.72rem;">â€”</span><?php endif; ?>
                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                            <td style="font-style:italic;color:var(--muted);font-size:.74rem;"><?=htmlspecialchars($ir['remark']??'')?></td>
                                                                                                                                                                                                                            <td class="sticky-r" style="background:var(--surface);text-align:center;">
                                                                                                                                                                                                                            <div class="action-btns">
                                                                                                                                                                                                                            <a href="?page=internet&action=form&id=<?=$ir['id']?>" class="action-btn edit"><i class="fas fa-edit"></i></a>
                                                                                                                                                                                                                            <button class="action-btn del" onclick="deleteInternet(<?=$ir['id']?>)"><i class="fas fa-trash"></i></button>
                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                            <?php endwhile;endif; ?>
                                                                                                                                                                                                                            <?php if(!$has_rows): ?>
                                                                                                                                                                                                                                <tr class="empty-row"><td colspan="13"><i class="fas fa-wifi"></i>No internet records found</td></tr>
                                                                                                                                                                                                                                <?php endif; ?>
                                                                                                                                                                                                                                </tbody>
                                                                                                                                                                                                                                </table>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                                                                                <script>
                                                                                                                                                                                                                                function deleteInternet(id){
                                                                                                                                                                                                                                Swal.fire({title:'Delete this record?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true})
                                                                                                                                                                                                                                .then(r=>{if(r.isConfirmed)window.location.href='?page=internet&action=delete&id='+id;});
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                const _pi=new URLSearchParams(window.location.search);
                                                                                                                                                                                                                                if(_pi.get('status')==='deleted'){Swal.fire({title:'Deleted!',text:'Record deleted successfully.',icon:'success',timer:2000,showConfirmButton:false});window.history.replaceState({},'',location.pathname+location.search.replace(/&status=[^&]*/,''));}
                                                                                                                                                                                                                                if(_pi.get('status')==='saved'){Swal.fire({title:'Saved!',text:'Internet record has been saved.',icon:'success',timer:2200,showConfirmButton:false});}
                                                                                                                                                                                                                                document.getElementById('inetSearch').addEventListener('keyup',function(){const v=this.value.toLowerCase();document.querySelectorAll('#inetTable tbody tr:not(.empty-row)').forEach(r=>{r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';});});
                                                                                                                                                                                                                                // Live countdown pulse for expiring rows
                                                                                                                                                                                                                                document.querySelectorAll('.inet-countdown-cell').forEach(function(cell){
                                                                                                                                                                                                                                var days = parseInt(cell.getAttribute('data-days'));
                                                                                                                                                                                                                                var val = cell.querySelector('.inet-cntdwn-val');
                                                                                                                                                                                                                                // Convert days to seconds for live countdown
                                                                                                                                                                                                                                var totalSeconds = days * 86400;
                                                                                                                                                                                                                                setInterval(function(){
                                                                                                                                                                                                                                totalSeconds = Math.max(0, totalSeconds - 1);
                                                                                                                                                                                                                                var d = Math.floor(totalSeconds / 86400);
                                                                                                                                                                                                                                var h = Math.floor((totalSeconds % 86400) / 3600);
                                                                                                                                                                                                                                var m = Math.floor((totalSeconds % 3600) / 60);
                                                                                                                                                                                                                                var s = totalSeconds % 60;
                                                                                                                                                                                                                                val.textContent = d + 'd ' + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
                                                                                                                                                                                                                                // Pulse red when under 7 days
                                                                                                                                                                                                                                if(d < 7){ cell.style.background='#fee2e2'; cell.style.borderColor='#fecaca'; cell.style.color='#b91c1c'; }
                                                                                                                                                                                                                                }, 1000);
                                                                                                                                                                                                                                });
                                                                                                                                                                                                                                </script>
                                                                                                                                                                                                                                
                                                                                                                                                                                                                                
                                                                                                                                                                                                                                <?php
                                                                                                                                                                                                                                //  EQUIPMENT STOCK: NEW EQUIPMENT FORM
                                                                                                                                                                                                                                elseif($current_page=='equipment_stock' && $action=='form'):
                                                                                                                                                                                                                                    $eq_edit=null; $eq_is_edit=false;
                                                                                                                                                                                                                                    if(isset($_GET['eq_id'])&&!empty($_GET['eq_id'])){
                                                                                                                                                                                                                                    $eq_eid=(int)($_GET[array_key_exists("eq_id",$_GET)?"eq_id":"id"]??0);
                                                                                                                                                                                                                                    $tc=@mysqli_query($conn,"SHOW TABLES LIKE 'equipment_stock'");
                                                                                                                                                                                                                                    if($tc&&mysqli_num_rows($tc)>0){$er=@mysqli_query($conn,"SELECT * FROM equipment_stock WHERE id='$eq_eid'");if($er&&($ed=mysqli_fetch_assoc($er))){$eq_edit=$ed;$eq_is_edit=true;}}
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    ?>
                                                                                                                                                                                                                                    <div class="form-wrap">
                                                                                                                                                                                                                                    <div class="form-header"><div class="form-header-icon"><i class="fas fa-boxes-stacked"></i></div><div><h3><?=$eq_is_edit?'Edit Equipment':'New Equipment'?></h3><p>Record ICT equipment stock information</p></div></div>
                                                                                                                                                                                                                                    <div class="form-body">
                                                                                                                                                                                                                                    <form id="equipForm" method="POST">
                                                                                                                                                                                                                                    <?php if($eq_is_edit): ?><input type="hidden" name="eq_id" value="<?=$eq_edit['id']?>"><?php endif; ?>
                                                                                                                                                                                                                                        <div class="form-section">
                                                                                                                                                                                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>Equipment Information</p></div>
                                                                                                                                                                                                                                        <div class="form-grid g3">
                                                                                                                                                                                                                                        <div><label class="field-lbl">E ID <span>*</span></label><div class="field-wrap"><i class="fas fa-barcode fi"></i><input type="text" name="e_id" id="eq_e_id" class="field" placeholder="Equipment ID" value="<?=htmlspecialchars($eq_edit['e_id']??'')?>" required></div></div>
                                                                                                                                                                                                                                        <div><label class="field-lbl">Eng Name</label><div class="field-wrap"><i class="fas fa-tag fi"></i><input type="text" name="eng_name" class="field" placeholder="English name" value="<?=htmlspecialchars($eq_edit['eng_name']??'')?>"></div></div>
                                                                                                                                                                                                                                        <div><label class="field-lbl">Lao Name</label><div class="field-wrap"><i class="fas fa-tag fi"></i><input type="text" name="lao_name" class="field" placeholder="Lao name" value="<?=htmlspecialchars($eq_edit['lao_name']??'')?>"></div></div>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <div class="form-section">
                                                                                                                                                                                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#dcfce7;color:#15803d;">2</div><p>Stock Quantities &amp; Details</p></div>
                                                                                                                                                                                                                                        <div class="form-grid g4">
                                                                                                                                                                                                                                        <div><label class="field-lbl">E New (Qty In)</label><div class="field-wrap"><i class="fas fa-plus-circle fi"></i><input type="number" name="e_new" id="eq_e_new" class="field" placeholder="0" value="<?=htmlspecialchars($eq_edit['e_new']??'0')?>" min="0" oninput="calcAllStock()"></div></div>
                                                                                                                                                                                                                                        <div><label class="field-lbl">All Stock</label><div class="field-wrap"><i class="fas fa-layer-group fi"></i><input type="number" name="all_stock" id="eq_all_stock" class="field" placeholder="0" value="<?=htmlspecialchars($eq_edit['all_stock']??'0')?>" readonly style="background:#f0f4fb;"></div></div>
                                                                                                                                                                                                                                        <div><label class="field-lbl">Type</label><div class="field-wrap"><i class="fas fa-laptop fi"></i><select name="type" class="field"><?php foreach(["unit","line","en","set","Piece","Other"] as $t){$sel=(isset($eq_edit['type'])&&$eq_edit['type']==$t)?'selected':'';echo "<option value='$t' $sel>$t</option>";}?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                                                                                                                                                                        <div><label class="field-lbl">Date In</label><div class="field-wrap"><i class="fas fa-calendar-plus fi"></i><input type="date" name="date_in" class="field" value="<?=htmlspecialchars($eq_edit['date_in']??'')?>"></div></div>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <button type="submit" class="btn-submit"><i class="fas fa-save"></i><?=$eq_is_edit?'Update Equipment':'Save Equipment'?></button>
                                                                                                                                                                                                                                        </form>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                                                                                        <script>
                                                                                                                                                                                                                                        var _isEdit = <?=$eq_is_edit?'true':'false'?>;
                                                                                                                                                                                                                                        var _origENew = <?=(int)($eq_edit['e_new']??0)?>;
                                                                                                                                                                                                                                        var _origAllStock = <?=(int)($eq_edit['all_stock']??0)?>;
                                                                                                                                                                                                                                        function calcAllStock(){
                                                                                                                                                                                                                                        var eNew=parseInt(document.getElementById('eq_e_new').value)||0;
                                                                                                                                                                                                                                        var newAll;
                                                                                                                                                                                                                                        if(_isEdit){
                                                                                                                                                                                                                                        // preserve stock already issued: all_stock = old_all + (new_e_new - old_e_new)
                                                                                                                                                                                                                                        newAll=Math.max(0,_origAllStock+(eNew-_origENew));
                                                                                                                                                                                                                                        }else{
                                                                                                                                                                                                                                        newAll=Math.max(0,eNew);
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        document.getElementById('eq_all_stock').value=newAll;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        calcAllStock();
                                                                                                                                                                                                                                        document.getElementById('equipForm').addEventListener('submit',function(e){
                                                                                                                                                                                                                                        e.preventDefault();
                                                                                                                                                                                                                                        var form=this;
                                                                                                                                                                                                                                        fetch('save_equipment.php',{method:'POST',body:new FormData(form)})
                                                                                                                                                                                                                                        .then(r=>r.json())
                                                                                                                                                                                                                                        .then(data=>{
                                                                                                                                                                                                                                        if(data.status==='saved'){Swal.fire({icon:'success',title:'Saved!',text:'Equipment has been saved.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});form.reset();document.getElementById('eq_all_stock').value=0;}
                                                                                                                                                                                                                                        else if(data.status==='updated'){Swal.fire({icon:'success',title:'Updated!',text:'Equipment has been updated.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});}
                                                                                                                                                                                                                                        else{Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});}
                                                                                                                                                                                                                                        }).catch(err=>Swal.fire({icon:'error',title:'Connection Error!',text:'Please try again.',confirmButtonColor:'#dc2626'}));
                                                                                                                                                                                                                                        });
                                                                                                                                                                                                                                        </script>
                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                        <?php
                                                                                                                                                                                                                                        //  EQUIPMENT STOCK: ALL EQUIPMENT TABLE
                                                                                                                                                                                                                                        elseif($current_page=='equipment_stock' && $action=='list'):
                                                                                                                                                                                                                                            $eq_tbl=@mysqli_query($conn,"SHOW TABLES LIKE 'equipment_stock'");
                                                                                                                                                                                                                                            $eq_result=null;
                                                                                                                                                                                                                                            if($eq_tbl&&mysqli_num_rows($eq_tbl)>0){$eq_result=mysqli_query($conn,"SELECT * FROM equipment_stock ORDER BY id DESC");}
                                                                                                                                                                                                                                            ?>
                                                                                                                                                                                                                                            <div class="page-hdr"><div><h2>All Equipment Stock</h2><p>All ICT equipment stock records</p></div><a href="?page=equipment_stock&action=form" class="btn-primary"><i class="fas fa-plus-circle"></i> New Equipment</a></div>
                                                                                                                                                                                                                                            <div class="tbl-wrap">
                                                                                                                                                                                                                                            <div class="filter-bar">
                                                                                                                                                                                                                                            <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="equipSearch" class="filter-input" placeholder="Search E ID, name..."></div>
                                                                                                                                                                                                                                            <select id="typeFilter" class="filter-select"><option value="">All Types</option><?php foreach(["Computer","Monitor","Printer","Scanner","Projector","UPS","Switch","Router","Cable","Other"] as $t) echo "<option value='$t'>$t</option>"; ?></select>
                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                            <div class="tbl-scroll"><table id="equipTable" style="min-width:1100px;">
                                                                                                                                                                                                                                            <thead><tr>
                                                                                                                                                                                                                                            <th style="width:50px;">#</th>
                                                                                                                                                                                                                                            <th>E ID</th><th>Eng Name</th><th>Lao Name</th>
                                                                                                                                                                                                                                            <th style="text-align:center;">E OldStock</th><th style="text-align:center;">All Stock</th>
                                                                                                                                                                                                                                            <th>Type</th><th style="text-align:center;">Date In</th><th style="text-align:center;">Created At</th><th style="text-align:center;">Action</th>
                                                                                                                                                                                                                                            </tr></thead><tbody>
                                                                                                                                                                                                                                            <?php if($eq_result&&mysqli_num_rows($eq_result)>0):$no=1;while($row=mysqli_fetch_assoc($eq_result)): ?>
                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                <td style="color:var(--muted);font-weight:700;"><?=$no++?></td>
                                                                                                                                                                                                                                                <td class="td-blue td-bold"><?=htmlspecialchars($row['e_id']??'')?></td>
                                                                                                                                                                                                                                                <td class="td-bold"><?=htmlspecialchars($row['eng_name']??'')?></td>
                                                                                                                                                                                                                                                <td><?=htmlspecialchars($row['lao_name']??'')?></td>
                                                                                                                                                                                                                                                <td style="text-align:center;"><?=htmlspecialchars($row['e_new']??0)?></td>
                                                                                                                                                                                                                                                <td style="text-align:center;font-weight:700;color:var(--navy);"><?=htmlspecialchars($row['all_stock']??0)?></td>
                                                                                                                                                                                                                                                <td><span class="status-pill pill-blue"><?=htmlspecialchars($row['type']??'')?></span></td>
                                                                                                                                                                                                                                                <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($row['date_in']??'')?></td>
                                                                                                                                                                                                                                                <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($row['created_at']??'')?></td>
                                                                                                                                                                                                                                                <td style="text-align:center;"><div class="action-btns"><a href="?page=equipment_stock&action=form&eq_id=<?=$row['id']?>" class="action-btn edit"><i class="fas fa-edit"></i></a><button class="action-btn del" onclick="deleteEquipment(<?=$row['id']?>)"><i class="fas fa-trash"></i></button></div></td>
                                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                                <?php endwhile;else: ?><tr class="empty-row"><td colspan="10"><i class="fas fa-boxes-stacked"></i>No equipment records found</td></tr><?php endif; ?>
                                                                                                                                                                                                                                                    </tbody></table></div></div>
                                                                                                                                                                                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                                                                                                    <script>
                                                                                                                                                                                                                                                    function deleteEquipment(id){Swal.fire({title:'Delete this equipment?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='?page=equipment_stock&action=delete&id='+id;});}
                                                                                                                                                                                                                                                    const _peq=new URLSearchParams(window.location.search);
                                                                                                                                                                                                                                                    if(_peq.get('status')==='deleted'){Swal.fire({title:'Deleted!',text:'Equipment deleted successfully.',icon:'success',timer:2000,showConfirmButton:false});window.history.replaceState({},'',location.pathname+location.search.replace(/&status=[^&]*/,''));}
                                                                                                                                                                                                                                                    document.getElementById('equipSearch').addEventListener('keyup',function(){
                                                                                                                                                                                                                                                    var v=this.value.toLowerCase();
                                                                                                                                                                                                                                                    var tf=document.getElementById('typeFilter').value;
                                                                                                                                                                                                                                                    document.querySelectorAll('#equipTable tbody tr:not(.empty-row)').forEach(function(r){
                                                                                                                                                                                                                                                    var cells=r.querySelectorAll('td');
                                                                                                                                                                                                                                                    var eid=(cells[1]?cells[1].textContent:'').toLowerCase();
                                                                                                                                                                                                                                                    var eng=(cells[2]?cells[2].textContent:'').toLowerCase();
                                                                                                                                                                                                                                                    var lao=(cells[3]?cells[3].textContent:'').toLowerCase();
                                                                                                                                                                                                                                                    var type=(cells[6]?cells[6].textContent:'').trim();
                                                                                                                                                                                                                                                    var matchSearch=!v||(eid.includes(v)||eng.includes(v)||lao.includes(v));
                                                                                                                                                                                                                                                    var matchType=!tf||type.includes(tf);
                                                                                                                                                                                                                                                    r.style.display=(matchSearch&&matchType)?'':'none';
                                                                                                                                                                                                                                                    });
                                                                                                                                                                                                                                                    });
                                                                                                                                                                                                                                                    document.getElementById('typeFilter').addEventListener('change',function(){
                                                                                                                                                                                                                                                    var tf=this.value;
                                                                                                                                                                                                                                                    var v=document.getElementById('equipSearch').value.toLowerCase();
                                                                                                                                                                                                                                                    document.querySelectorAll('#equipTable tbody tr:not(.empty-row)').forEach(function(r){
                                                                                                                                                                                                                                                    var cells=r.querySelectorAll('td');
                                                                                                                                                                                                                                                    var eid=(cells[1]?cells[1].textContent:'').toLowerCase();
                                                                                                                                                                                                                                                    var eng=(cells[2]?cells[2].textContent:'').toLowerCase();
                                                                                                                                                                                                                                                    var lao=(cells[3]?cells[3].textContent:'').toLowerCase();
                                                                                                                                                                                                                                                    var type=(cells[6]?cells[6].textContent:'').trim();
                                                                                                                                                                                                                                                    var matchSearch=!v||(eid.includes(v)||eng.includes(v)||lao.includes(v));
                                                                                                                                                                                                                                                    var matchType=!tf||type.includes(tf);
                                                                                                                                                                                                                                                    r.style.display=(matchSearch&&matchType)?'':'none';
                                                                                                                                                                                                                                                    });
                                                                                                                                                                                                                                                    });
                                                                                                                                                                                                                                                    </script>
                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                    <?php
                                                                                                                                                                                                                                                    //  EQUIPMENT STOCK: ISSUE EQUIPMENT FORM
                                                                                                                                                                                                                                                    elseif($current_page=='equipment_stock' && $action=='issue_form'):
                                                                                                                                                                                                                                                        ?>
                                                                                                                                                                                                                                                        <div id="eid-banner" style="display:none;margin-bottom:1rem;padding:.75rem 1.125rem;border-radius:10px;font-size:.8rem;font-weight:600;background:#ffedd5;color:#c2410c;border:1px solid #fed7aa;"></div>
                                                                                                                                                                                                                                                        <div class="form-wrap">
                                                                                                                                                                                                                                                        <div class="form-header"><div class="form-header-icon"><i class="fas fa-share-square"></i></div><div><h3>Issue Equipment</h3><p>Record equipment issued to a staff member</p></div></div>
                                                                                                                                                                                                                                                        <div class="form-body">
                                                                                                                                                                                                                                                        <form id="issueForm" method="POST">
                                                                                                                                                                                                                                                        <div class="form-section">
                                                                                                                                                                                                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#dbeafe;color:#1d4ed8;">1</div><p>Staff Information &nbsp;<span style="font-size:.62rem;font-weight:600;color:var(--muted);text-transform:none;letter-spacing:0;">(Scan/Enter INS to auto-fill)</span></p></div>
                                                                                                                                                                                                                                                        <div class="form-grid g4">
                                                                                                                                                                                                                                                        <div><label class="field-lbl">User Name</label><div class="field-wrap"><i class="fas fa-user fi"></i><input type="text" name="username" id="issue_username" class="field" placeholder="Auto-filled"></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">INS Number <span>*</span></label><div class="field-wrap" style="display:flex;gap:.4rem;align-items:center;"><i class="fas fa-id-card fi"></i><input type="text" id="issue_ins_input" name="ins_number" class="field" placeholder="Scan or type INS" style="flex:1;" required><button type="button" id="issue_ins_btn" style="padding:.65rem .75rem;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:.72rem;cursor:pointer;flex-shrink:0;"><i class="fas fa-search"></i></button></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Department</label><div class="field-wrap"><i class="fas fa-sitemap fi"></i><select name="department" id="issue_department" class="field"><option value="" disabled selected>Select Department</option><?php foreach(["Finance","Operation","Fleet","Logistic","HR","Liaison","GIS","Electrician","ICT","Translator","Eore","Expat"] as $d) echo "<option value='$d'>$d</option>"; ?></select><i class="fas fa-chevron-down select-arrow"></i></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Team</label><div class="field-wrap"><i class="fas fa-users fi"></i><input type="text" name="team" id="issue_team" class="field" placeholder="Auto-filled"></div></div>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        <div class="form-section">
                                                                                                                                                                                                                                                        <div class="form-section-hdr"><div class="step-badge" style="background:#dcfce7;color:#15803d;">2</div><p>Equipment Information &nbsp;<span style="font-size:.62rem;font-weight:600;color:var(--muted);text-transform:none;letter-spacing:0;">(Enter Equip ID to auto-fill)</span></p></div>
                                                                                                                                                                                                                                                        <div class="form-grid g4">
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Equip ID <span>*</span></label><div class="field-wrap" style="display:flex;gap:.4rem;align-items:center;"><i class="fas fa-barcode fi"></i><input type="text" id="issue_eid_input" name="e_id" class="field" placeholder="Equipment ID" style="flex:1;" required><button type="button" id="issue_eid_btn" style="padding:.65rem .75rem;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:.72rem;cursor:pointer;flex-shrink:0;"><i class="fas fa-search"></i></button></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Eng Name</label><div class="field-wrap"><i class="fas fa-tag fi"></i><input type="text" name="eng_name" id="issue_eng_name" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Lao Name</label><div class="field-wrap"><i class="fas fa-tag fi"></i><input type="text" name="lao_name" id="issue_lao_name" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">In Stock (All)</label><div class="field-wrap"><i class="fas fa-layer-group fi"></i><input type="number" name="in_stock" id="issue_in_stock" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">E OldStock</label><div class="field-wrap"><i class="fas fa-box fi"></i><input type="number" name="e_old_stock" id="issue_e_old_stock" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Type</label><div class="field-wrap"><i class="fas fa-laptop fi"></i><input type="text" name="type" id="issue_type" class="field" placeholder="Auto-filled" readonly style="background:#f0f4fb;"></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Quantity Issue <span>*</span></label><div class="field-wrap"><i class="fas fa-hashtag fi"></i><input type="number" name="quantity" id="issue_quantity" class="field" placeholder="Qty to issue" min="1" required></div></div>
                                                                                                                                                                                                                                                        <div><label class="field-lbl">Date Out</label><div class="field-wrap"><i class="fas fa-calendar-minus fi"></i><input type="date" name="date_out" class="field" value="<?=date('Y-m-d')?>"></div></div>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        <button type="submit" class="btn-submit"><i class="fas fa-share-square"></i> Issue Equipment</button>
                                                                                                                                                                                                                                                        </form>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                                                                                                        <script>
                                                                                                                                                                                                                                                        function setIssueField(id,val){var el=document.getElementById(id);if(el)el.value=val||'';}
                                                                                                                                                                                                                                                        function lockEqFields(lock){
                                                                                                                                                                                                                                                        ['issue_eng_name','issue_lao_name','issue_in_stock','issue_e_old_stock','issue_type'].forEach(function(id){
                                                                                                                                                                                                                                                        var el=document.getElementById(id);if(!el)return;
                                                                                                                                                                                                                                                        el.readOnly=lock;el.style.background=lock?'#f0f4fb':'#fff';
                                                                                                                                                                                                                                                        });
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        function lookupINS(){
                                                                                                                                                                                                                                                        var ins=document.getElementById('issue_ins_input').value.trim();
                                                                                                                                                                                                                                                        if(!ins)return;
                                                                                                                                                                                                                                                        fetch('index.php?lookup_ins=1&ins_number='+encodeURIComponent(ins))
                                                                                                                                                                                                                                                        .then(r=>r.json()).then(data=>{
                                                                                                                                                                                                                                                        if(data.status==='found'){
                                                                                                                                                                                                                                                        var d=data.data;
                                                                                                                                                                                                                                                        setIssueField('issue_username',d.username||d.full_name||'');
                                                                                                                                                                                                                                                        setIssueField('issue_team',d.team||'');
                                                                                                                                                                                                                                                        var dept=document.getElementById('issue_department');
                                                                                                                                                                                                                                                        if(dept&&d.department){for(var i=0;i<dept.options.length;i++){if(dept.options[i].value===d.department){dept.selectedIndex=i;break;}}}
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        }).catch(()=>{});
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        function lookupEID(){
                                                                                                                                                                                                                                                        var eid=document.getElementById('issue_eid_input').value.trim();
                                                                                                                                                                                                                                                        if(!eid)return;
                                                                                                                                                                                                                                                        var banner=document.getElementById('eid-banner');
                                                                                                                                                                                                                                                        fetch('index.php?lookup_eid=1&e_id='+encodeURIComponent(eid))
                                                                                                                                                                                                                                                        .then(r=>r.json()).then(data=>{
                                                                                                                                                                                                                                                        if(data.status==='found'){
                                                                                                                                                                                                                                                        var d=data.data;
                                                                                                                                                                                                                                                        setIssueField('issue_eng_name',d.eng_name);
                                                                                                                                                                                                                                                        setIssueField('issue_lao_name',d.lao_name);
                                                                                                                                                                                                                                                        setIssueField('issue_in_stock',d.all_stock);
                                                                                                                                                                                                                                                        setIssueField('issue_e_old_stock',d.e_new);
                                                                                                                                                                                                                                                        setIssueField('issue_type',d.type);
                                                                                                                                                                                                                                                        lockEqFields(true);
                                                                                                                                                                                                                                                        banner.style.display='none';
                                                                                                                                                                                                                                                        }else{
                                                                                                                                                                                                                                                        banner.style.display='block';
                                                                                                                                                                                                                                                        banner.innerHTML='<i class="fas fa-exclamation-triangle"></i> Equipment not found fill manually';
                                                                                                                                                                                                                                                        lockEqFields(false);
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        }).catch(()=>{});
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        document.getElementById('issue_ins_input').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();lookupINS();}});
                                                                                                                                                                                                                                                        document.getElementById('issue_ins_btn').addEventListener('click',lookupINS);
                                                                                                                                                                                                                                                        // Auto-lookup on INS input (debounce)
                                                                                                                                                                                                                                                        var insTimer;document.getElementById('issue_ins_input').addEventListener('input',function(){clearTimeout(insTimer);if(this.value.trim().length>=4)insTimer=setTimeout(lookupINS,600);});
                                                                                                                                                                                                                                                        document.getElementById('issue_eid_input').addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();lookupEID();}});
                                                                                                                                                                                                                                                        document.getElementById('issue_eid_btn').addEventListener('click',lookupEID);
                                                                                                                                                                                                                                                        document.getElementById('issueForm').addEventListener('submit',function(e){
                                                                                                                                                                                                                                                        e.preventDefault();
                                                                                                                                                                                                                                                        var form=this;
                                                                                                                                                                                                                                                        var qty=parseInt(document.getElementById('issue_quantity').value)||0;
                                                                                                                                                                                                                                                        var inStock=parseInt(document.getElementById('issue_in_stock').value)||0;
                                                                                                                                                                                                                                                        if(qty<=0){Swal.fire({icon:'warning',title:'Invalid Quantity',text:'Please enter the quantity to issue.',confirmButtonColor:'#01244d'});return;}
                                                                                                                                                                                                                                                        if(qty>inStock){Swal.fire({icon:'error',title:'Stock Insufficient',text:'Requested qty ('+qty+') exceeds available stock ('+inStock+').',confirmButtonColor:'#dc2626'});return;}
                                                                                                                                                                                                                                                        fetch('save_equipment_issue.php',{method:'POST',body:new FormData(form)})
                                                                                                                                                                                                                                                        .then(r=>r.json())
                                                                                                                                                                                                                                                        .then(data=>{
                                                                                                                                                                                                                                                        if(data.status==='saved'){Swal.fire({icon:'success',title:'Saved!',text:'Equipment issued successfully. Stock updated.',confirmButtonColor:'#01244d',timer:2500,timerProgressBar:true});form.reset();lockEqFields(true);document.getElementById('eid-banner').style.display='none';document.querySelector('input[name="date_out"]').value='<?=date('Y-m-d')?>';}
                                                                                                                                                                                                                                                        else{Swal.fire({icon:'error',title:'Error!',text:data.msg||'Unable to save.',confirmButtonColor:'#dc2626'});}
                                                                                                                                                                                                                                                        }).catch(err=>Swal.fire({icon:'error',title:'Connection Error!',text:'Please try again.',confirmButtonColor:'#dc2626'}));
                                                                                                                                                                                                                                                        });
                                                                                                                                                                                                                                                        </script>
                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                        <?php
                                                                                                                                                                                                                                                        //  EQUIPMENT STOCK: ISSUE LIST TABLE
                                                                                                                                                                                                                                                        elseif($current_page=='equipment_stock' && $action=='issue_list'):
                                                                                                                                                                                                                                                            $ei_tbl=@mysqli_query($conn,"SHOW TABLES LIKE 'equipment_issues'");
                                                                                                                                                                                                                                                            $ei_result=null;
                                                                                                                                                                                                                                                            if($ei_tbl&&mysqli_num_rows($ei_tbl)>0){$ei_result=mysqli_query($conn,"SELECT * FROM equipment_issues ORDER BY id DESC");}
                                                                                                                                                                                                                                                            ?>
                                                                                                                                                                                                                                                            <div class="page-hdr"><div><h2>Issue List</h2><p>All ICT equipment issue records</p></div><a href="?page=equipment_stock&action=issue_form" class="btn-primary"><i class="fas fa-plus-circle"></i> Issue Equipment</a></div>
                                                                                                                                                                                                                                                            <div class="tbl-wrap">
                                                                                                                                                                                                                                                            <div class="filter-bar">
                                                                                                                                                                                                                                                            <div class="search-wrap"><i class="fas fa-search"></i><input type="text" id="issueSearch" class="filter-input" placeholder="Search INS, name, E ID..."></div>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                            <div class="tbl-scroll"><table id="issueTable" style="min-width:1400px;">
                                                                                                                                                                                                                                                            <thead><tr>
                                                                                                                                                                                                                                                            <th style="width:50px;">#</th>
                                                                                                                                                                                                                                                            <th>User Name</th><th>INS</th><th>Department</th><th>Team</th>
                                                                                                                                                                                                                                                            <th>Equip ID</th><th>Eng Name</th><th>Lao Name</th>
                                                                                                                                                                                                                                                            <th style="text-align:center;">Quantity</th><th style="text-align:center;">In Stock</th><th style="text-align:center;">E OldStock</th>
                                                                                                                                                                                                                                                            <th>Type</th><th style="text-align:center;">Date Out</th><th style="text-align:center;">Action</th>
                                                                                                                                                                                                                                                            </tr></thead><tbody>
                                                                                                                                                                                                                                                            <?php if($ei_result&&mysqli_num_rows($ei_result)>0):$no=1;while($row=mysqli_fetch_assoc($ei_result)): ?>
                                                                                                                                                                                                                                                                <tr>
                                                                                                                                                                                                                                                                <td style="color:var(--muted);font-weight:700;"><?=$no++?></td>
                                                                                                                                                                                                                                                                <td class="td-bold"><?=htmlspecialchars($row['username']??'')?></td>
                                                                                                                                                                                                                                                                <td class="td-mono"><?=htmlspecialchars($row['ins_number']??'')?></td>
                                                                                                                                                                                                                                                                <td><?=htmlspecialchars($row['department']??'')?></td>
                                                                                                                                                                                                                                                                <td><?=htmlspecialchars($row['team']??'')?></td>
                                                                                                                                                                                                                                                                <td class="td-blue td-bold"><?=htmlspecialchars($row['e_id']??'')?></td>
                                                                                                                                                                                                                                                                <td><?=htmlspecialchars($row['eng_name']??'')?></td>
                                                                                                                                                                                                                                                                <td><?=htmlspecialchars($row['lao_name']??'')?></td>
                                                                                                                                                                                                                                                                <td style="text-align:center;font-weight:700;color:var(--danger);"><?=htmlspecialchars($row['quantity']??0)?></td>
                                                                                                                                                                                                                                                                <td style="text-align:center;font-weight:700;color:var(--navy);"><?=htmlspecialchars($row['in_stock']??0)?></td>
                                                                                                                                                                                                                                                                <td style="text-align:center;font-weight:700;color:var(--blue);"><?=htmlspecialchars($row['e_old_stock']??0)?></td>
                                                                                                                                                                                                                                                                <td><span class="status-pill pill-blue"><?=htmlspecialchars($row['type']??'')?></span></td>
                                                                                                                                                                                                                                                                <td style="text-align:center;font-size:.75rem;"><?=htmlspecialchars($row['date_out']??'')?></td>
                                                                                                                                                                                                                                                                <td style="text-align:center;"><div class="action-btns"><button class="action-btn del" onclick="deleteIssue(<?=$row['id']?>)"><i class="fas fa-trash"></i></button></div></td>
                                                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                                                <?php endwhile;else: ?><tr class="empty-row"><td colspan="14"><i class="fas fa-folder-open"></i>No issue records found</td></tr><?php endif; ?>
                                                                                                                                                                                                                                                                    </tbody></table></div></div>
                                                                                                                                                                                                                                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                                                                                                                    <script>
                                                                                                                                                                                                                                                                    function deleteIssue(id){Swal.fire({title:'Delete this issue record?',text:'This action cannot be undone!',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='?page=equipment_stock&action=delete_issue&id='+id;});}
                                                                                                                                                                                                                                                                    const _pei=new URLSearchParams(window.location.search);
                                                                                                                                                                                                                                                                    if(_pei.get('status')==='deleted'){Swal.fire({title:'Deleted!',text:'Issue record deleted successfully.',icon:'success',timer:2000,showConfirmButton:false});window.history.replaceState({},'',location.pathname+location.search.replace(/&status=[^&]*/,''));}
                                                                                                                                                                                                                                                                    if(_pei.get('status')==='saved'){Swal.fire({title:'Saved!',text:'Equipment issue has been recorded.',icon:'success',timer:2200,showConfirmButton:false});}
                                                                                                                                                                                                                                                                    document.getElementById('issueSearch').addEventListener('keyup',function(){const v=this.value.toLowerCase();document.querySelectorAll('#issueTable tbody tr:not(.empty-row)').forEach(r=>{r.style.display=r.innerText.toLowerCase().includes(v)?'':'none';});});
                                                                                                                                                                                                                                                                    </script>
                                                                                                                                                                                                                                                                    
                                                                                                                                                                                                                                                                    <?php else: ?>
                                                                                                                                                                                                                                                                        <div class="placeholder-card"><i class="fas fa-mouse-pointer"></i><h3 style="font-size:1.1rem;font-weight:800;color:var(--text);margin-bottom:.5rem;">Please Select a Menu</h3><p>Select a menu on the left to manage data.</p></div>
                                                                                                                                                                                                                                                                        <?php endif; ?>
                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                        </div><!-- /content -->
                                                                                                                                                                                                                                                                        </main>
                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                                                                                                                                                                                                                                                        <script>
                                                                                                                                                                                                                                                                        function toggleSub(id,btn){const el=document.getElementById(id);const grp=btn?btn.closest('.menu-group'):null;if(!el)return;const open=el.style.display==='block';el.style.display=open?'none':'block';if(grp)grp.classList.toggle('menu-open',!open);}
                                                                                                                                                                                                                                                                        function updateClock(){const n=new Date();const t=[n.getHours(),n.getMinutes(),n.getSeconds()].map(v=>String(v).padStart(2,'0')).join(':');const el=document.getElementById('clk');if(el)el.textContent=t;}
                                                                                                                                                                                                                                                                        updateClock();setInterval(updateClock,1000);
                                                                                                                                                                                                                                                                        function confirmLogout(){Swal.fire({title:'Leave the system?',text:'Do you want to logout from the system?',icon:'question',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#64748b',confirmButtonText:'<i class="fas fa-right-from-bracket"></i> Logout',cancelButtonText:'Cancel',reverseButtons:true}).then(r=>{if(r.isConfirmed)window.location.href='logout.php';});return false;}
                                                                                                                                                                                                                                                                        </script>
                                                                                                                                                                                                                                                                        </body>
                                                                                                                                                                                                                                                                        </html>
                                                                                                                                                                                                                                                                        
                                                                                                                                                                                                                                                                        