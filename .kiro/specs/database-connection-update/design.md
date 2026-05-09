# Design Document

## Overview

ການອັບເດດການເຊື່ອມຕໍ່ຖານຂໍ້ມູນໃນລະບົບ ICT ໃຫ້ໃຊ້ຂໍ້ມູນການເຊື່ອມຕໍ່ໃໝ່ຈາກ InfinityFree hosting. ການອອກແບບນີ້ຈະຮັບປະກັນວ່າການເຊື່ອມຕໍ່ຖານຂໍ້ມູນຖືກຈັດການແບບສູນກາງ, ປອດໄພ, ແລະ ງ່າຍຕໍ່ການບຳລຸງຮັກສາ.

## Architecture

### ການຈັດການການເຊື່ອມຕໍ່ແບບສູນກາງ

ລະບົບຈະໃຊ້ໄຟລ໌ `config.php` ເປັນຈຸດສູນກາງສຳລັບການຈັດການການເຊື່ອມຕໍ່ຖານຂໍ້ມູນທັງໝົດ. ໄຟລ໌ອື່ນໆ ທີ່ຕ້ອງການເຊື່ອມຕໍ່ຖານຂໍ້ມູນຈະ include ໄຟລ໌ນີ້.

### ການຈັດການຂໍ້ຜິດພາດ

ລະບົບຈະມີການຈັດການຂໍ້ຜິດພາດທີ່ເໝາະສົມສຳລັບສະພາບແວດລ້ອມ production:
- ປິດການສະແດງ error ລະອຽດ
- ບັນທຶກ error ໃນ log file
- ສະແດງຂໍ້ຄວາມທີ່ເໝາະສົມກັບຜູ້ໃຊ້

## Components and Interfaces

### 1. ໄຟລ໌ການເຊື່ອມຕໍ່ຫຼັກ (config.php)

```php
<?php
// Database configuration for InfinityFree
$host = "sql207.infinityfree.com";
$user = "if0_41843014";
$pass = "60suJN8PgyU9SL";
$db   = "if0_41843014_ict_system";

// Error reporting settings for production
ini_set('display_errors', 0);
error_reporting(0);

// Create connection
$conn = @mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Service temporarily unavailable. Please try again later.");
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>
```

### 2. ໄຟລ໌ທີ່ຕ້ອງອັບເດດ

ຈາກການວິເຄາະ, ມີໄຟລ໌ທີ່ຕ້ອງອັບເດດດັ່ງນີ້:

**ໄຟລ໌ທີ່ມີການເຊື່ອມຕໍ່ຖານຂໍ້ມູນໂດຍກົງ:**
- `index.php`
- `login.php`
- `register.php`
- `reset_api.php`
- `save_account.php`
- `save_card.php`
- `save_device.php`
- `save_employee.php`
- `save_equipment.php`
- `save_equipment_issue.php`
- `save_internet.php`
- `save_mistake.php`
- `save_transfer.php`
- `update_account.php`
- `update_device.php`

**ໄຟລ໌ທີ່ຕ້ອງອັບເດດ/ລຶບ:**
- `db.php` (ໃຊ້ PDO, ຕ້ອງອັບເດດ)
- `db_connect.php` (ຕ້ອງອັບເດດ ຫຼື ລຶບ)

## Data Models

### ຂໍ້ມູນການເຊື່ອມຕໍ່ໃໝ່

```
Hostname: sql207.infinityfree.com
Port: 3306
Username: if0_41843014
Password: 60suJN8PgyU9SL
Database: if0_41843014_ict_system
```

### ການຈັດການ Character Set

ລະບົບຈະໃຊ້ `utf8mb4` ເປັນ character set ມາດຕະຖານເພື່ອຮອງຮັບພາສາລາວ ແລະ ອັກສອນພິເສດອື່ນໆ.

## Error Handling

### ການຈັດການຂໍ້ຜິດພາດໃນການເຊື່ອມຕໍ່

1. **ສຳລັບສະພາບແວດລ້ອມ Production:**
   - ປິດການສະແດງ error ລະອຽດ (`display_errors = 0`)
   - ບັນທຶກ error ໃນ log file
   - ສະແດງຂໍ້ຄວາມທົ່ວໄປກັບຜູ້ໃຊ້

2. **ການຈັດການ Connection Failure:**
   - ໃຊ້ `@mysqli_connect()` ເພື່ອປ້ອງກັນການສະແດງ warning
   - ກວດສອບການເຊື່ອມຕໍ່ດ້ວຍ `if (!$conn)`
   - ບັນທຶກ error ດ້ວຍ `error_log()`
   - ສະແດງຂໍ້ຄວາມທີ່ເໝາະສົມກັບຜູ້ໃຊ້

### ການຈັດການ Character Encoding

ກຳນົດ character set ເປັນ `utf8mb4` ສຳລັບທຸກການເຊື່ອມຕໍ່ເພື່ອຮອງຮັບພາສາລາວ:

```php
mysqli_set_charset($conn, 'utf8mb4');
```

## Testing Strategy

### 1. ການທົດສອບການເຊື່ອມຕໍ່

- ທົດສອບການເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໃໝ່
- ກວດສອບວ່າທຸກໄຟລ໌ສາມາດເຊື່ອມຕໍ່ໄດ້ຢ່າງຖືກຕ້ອງ
- ທົດສອບການຈັດການຂໍ້ຜິດພາດ

### 2. ການທົດສອບຟັງຊັນ

- ທົດສອບການ login/logout
- ທົດສອບການບັນທຶກຂໍ້ມູນ
- ທົດສອບການອ່ານຂໍ້ມູນ
- ທົດສອບການອັບເດດຂໍ້ມູນ

### 3. ການທົດສອບ Character Encoding

- ທົດສອບການບັນທຶກ ແລະ ອ່ານຂໍ້ມູນພາສາລາວ
- ກວດສອບວ່າອັກສອນພິເສດສະແດງຜົນຖືກຕ້ອງ

### 4. ການທົດສອບ Error Handling

- ທົດສອບເມື່ອຖານຂໍ້ມູນບໍ່ສາມາດເຊື່ອມຕໍ່ໄດ້
- ກວດສອບວ່າ error ຖືກບັນທຶກຢ່າງຖືກຕ້ອງ
- ກວດສອບວ່າຜູ້ໃຊ້ໄດ້ຮັບຂໍ້ຄວາມທີ່ເໝາະສົມ

## Implementation Approach

### ຂັ້ນຕອນການອັບເດດ

1. **ອັບເດດໄຟລ໌ config.php** - ໃຫ້ມີຂໍ້ມູນການເຊື່ອມຕໍ່ໃໝ່
2. **ອັບເດດໄຟລ໌ທີ່ມີການເຊື່ອມຕໍ່ໂດຍກົງ** - ປ່ຽນໃຫ້ໃຊ້ config.php
3. **ອັບເດດໄຟລ໌ db.php** - ປ່ຽນຂໍ້ມູນການເຊື່ອມຕໍ່ PDO
4. **ອັບເດດໄຟລ໌ db_connect.php** - ປ່ຽນຂໍ້ມູນການເຊື່ອມຕໍ່
5. **ທົດສອບການເຮັດວຽກ** - ກວດສອບວ່າທຸກຟັງຊັນເຮັດວຽກປົກກະຕິ

### ການຮັກສາຄວາມເຂົ້າກັນໄດ້

- ຮັກສາຊື່ຕົວແປ `$conn` ເພື່ອຄວາມເຂົ້າກັນໄດ້ກັບໂຄດເກົ່າ
- ຮັກສາການຕັ້ງຄ່າ character set
- ຮັກສາການຈັດການຂໍ້ຜິດພາດທີ່ມີຢູ່ແລ້ວ