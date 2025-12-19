
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Ticky-Tock' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300..700;1,300..700&family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
</head>
<body>
<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">
<div class="header-wrapper">
<div class="top">
    <button class="hamburger" id="hamburgerBtn">
    ☰
</button>

    <h1><a style="display: flex; align-items: center; gap:5px; padding:5px; text-decoration: none;" href="home.php">
        <img style="width: auto; height: 70px;" class="icon-user" src="../assets/animation/RetroCat.png" alt=""> 
        <h1 class="header-text">Ticky-Tock
        </h1>
        </a>
    </h1>
    <button class="main-dark-toggle" id="mainDarkToggle">
    <i style="color: #000000ff;" class="fa fa-moon-o" aria-hidden="true"></i>
    </button>


   <form class="filter-bar" method="GET" action="../pages/search.php">

    <input type="text"
           name="name"
           placeholder="Tên công việc..."
           value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">

    <select name="day">
  <option value="">Ngày</option>
  <?php for ($d = 1; $d <= 31; $d++): ?>
    <option value="<?= $d ?>"
      <?= (($_GET['day'] ?? '') == $d) ? 'selected' : '' ?>>
      <?= $d ?>
    </option>
  <?php endfor; ?>
</select>


    <select name="month">
        <option value="">Tháng</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>"
                <?= (($_GET['month'] ?? '') == $m) ? 'selected' : '' ?>>
                <?= $m ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="year">
        <option value="">Năm</option>
        <?php for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++): ?>
            <option value="<?= $y ?>"
                <?= (($_GET['year'] ?? '') == $y) ? 'selected' : '' ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>

    <select name="status">
        <option value="">-- Trạng thái --</option>
        <option value="overdue" <?= ($_GET['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>📛 Quá hạn</option>
        <option value="soon" <?= ($_GET['status'] ?? '') === 'soon' ? 'selected' : '' ?>>⏳ Sắp đến hạn</option>
        <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>🔄 Đang tiến hành</option>
        <option value="no_deadline" <?= ($_GET['status'] ?? '') === 'no_deadline' ? 'selected' : '' ?>>♾️ Vô thời hạn</option>
        <option value="new" <?= ($_GET['status'] ?? '') === 'new' ? 'selected' : '' ?>>🆕 Mới thêm</option>
        <option value="done" <?= ($_GET['status'] ?? '') === 'done' ? 'selected' : '' ?>>✅ Hoàn thành</option>
    </select>

    <button style="font-weight: bold;" type="submit" class="btn">Lọc</button>
</form>



</div>

<div class="menu-bar">
    <a href="user.php" class="menu-item" style="cursor: pointer; align-items: center; display: flex; gap: 5px;"> <img class="icon-user" src="../assets/animation/Box3.png" alt=""> <?= htmlspecialchars($username) ?></a>
    <a href="add.php" class="menu-item">Thêm công việc</a>
    <a href="../actions/logout.php" class="menu-item">Đăng xuất</a>
    <a href="lab.php" class="menu-item">Lab thực hành</a>
</div>
</div>
