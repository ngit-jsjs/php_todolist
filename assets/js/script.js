// các hàm toggle password/dark mode giữ nguyên nhưng kiểm tra phần tử tồn tại
function toggleDarkMode() {
  document.body.classList.toggle("dark-mode");
}

// phần dark mode / loginCard (giữ nguyên logic nhưng kiểm tra tồn tại)
const loginCard = document.getElementById("loginCard");
const darkToggle = document.getElementById("darkToggle");

const moonIcon =
  '<i style="font-size: 25px; color: #550064ff;" class="fa fa-moon-o" aria-hidden="true"></i>';
const sunIcon =
  '<i style="font-size: 25px; color: #ffcf30ff;" class="fa fa-sun-o" aria-hidden="true"></i>';

if (darkToggle) {
  // load trạng thái đã lưu
  if (localStorage.getItem("darkMode") === "true") {
    document.body.classList.add("dark-mode");
    darkToggle.innerHTML = sunIcon;
  } else {
    darkToggle.innerHTML = moonIcon;
  }

  // click toggle
  darkToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    const isDark = document.body.classList.contains("dark-mode");

    darkToggle.innerHTML = isDark ? sunIcon : moonIcon;
    localStorage.setItem("darkMode", isDark);
  });
}

const mainDarkToggle = document.getElementById("mainDarkToggle");

if (mainDarkToggle) {
  // Load trạng thái đã lưu
  if (localStorage.getItem("darkMode") === "true") {
    document.body.classList.add("dark-mode");
    mainDarkToggle.innerHTML = sunIcon;
  } else {
    mainDarkToggle.innerHTML = moonIcon;
  }

  // Click toggle
  mainDarkToggle.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    const isDark = document.body.classList.contains("dark-mode");

    mainDarkToggle.innerHTML = isDark ? sunIcon : moonIcon;
    localStorage.setItem("darkMode", isDark);
  });
}

// custom select - kiểm tra tất cả phần tử trước khi dùng
const selectBox = document.querySelector(".select-selected");
const selectItems = document.querySelector(".select-items");
const hiddenInput = document.getElementById("filter_status");
const displayText = document.getElementById("filter_status_display");

if (selectBox && selectItems && hiddenInput && displayText) {
  selectBox.addEventListener("click", function (e) {
    e.stopPropagation();
    selectItems.classList.toggle("show");
  });

  selectItems.addEventListener("click", function (e) {
    e.stopPropagation();
  });

  selectItems.querySelectorAll("li").forEach((item) => {
    item.addEventListener("click", function () {
      const val = this.getAttribute("data-value") || "";
      hiddenInput.value = val;
      displayText.textContent = this.textContent;
      selectItems.classList.remove("show");
    });
  });

  document.addEventListener("click", function () {
    selectItems.classList.remove("show");
  });
}

// days/start/end inputs: add listener chỉ khi tồn tại
const daysInput = document.getElementById("daysInput");
const startInput = document.getElementById("startInput");
const endInput = document.getElementById("endInput");

if (daysInput && (startInput || endInput)) {
  daysInput.addEventListener("input", () => {
    const days = parseInt(daysInput.value, 10);
    if (isNaN(days) || days < 0) return;

    const start =
      startInput && startInput.value ? new Date(startInput.value) : new Date();

    if (days === 0) {
      // Trong ngày: giữ nguyên ngày, chỉ set giờ cuối ngày (23:59)
      start.setHours(23, 59, 0, 0);
    } else {
      start.setDate(start.getDate() + days);
    }

    const year = start.getFullYear();
    const month = String(start.getMonth() + 1).padStart(2, "0");
    const day = String(start.getDate()).padStart(2, "0");
    const hours = String(start.getHours()).padStart(2, "0");
    const minutes = String(start.getMinutes()).padStart(2, "0");

    if (endInput) {
      endInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
  });
}

if (startInput) {
  startInput.addEventListener("change", () => {
    if (daysInput && daysInput.value)
      daysInput.dispatchEvent(new Event("input"));
    if (typeof validateEndTime === "function") validateEndTime();
  });
}

if (endInput) {
  endInput.addEventListener("change", () => {
    if (typeof validateEndTime === "function") validateEndTime();

    if (!startInput || !endInput) return;

    const start = new Date(startInput.value);
    const end = new Date(endInput.value);
    const diffTime = end - start;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (!isNaN(diffDays) && diffDays >= 0 && daysInput) {
      daysInput.value = diffDays;
    }
  });
}

function validateEndTime() {
  if (!startInput || !endInput) return;

  const start = new Date(startInput.value);
  const end = new Date(endInput.value);

  if (isNaN(start) || isNaN(end)) return;

  if (end <= start) {
    endInput.setCustomValidity(
      "Thời gian kết thúc phải sau thời gian bắt đầu!"
    );
    endInput.reportValidity();
  } else {
    endInput.setCustomValidity("");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const ham = document.getElementById("hamburgerBtn");
  const menu = document.querySelector(".menu-bar");
  const top = document.querySelector(".top");
  const filter = document.querySelector(".filter-bar");

  // Nếu không có top hoặc menu thì thôi, khỏi làm gì
  if (!top || !menu) {
    return;
  }

  // ====== HÀM DI CHUYỂN FILTER-BAR THEO KÍCH THƯỚC MÀN HÌNH ======
  function moveFilterBar() {
    if (!filter || !menu || !top) return;

    if (window.innerWidth <= 768) {
      // MOBILE
      let mobileTop = document.querySelector(".top.mobile-top");

      // tạo wrapper nếu chưa có
      if (!mobileTop) {
        mobileTop = document.createElement("div");
        mobileTop.classList.add("top", "mobile-top");
      }

      // đưa filter vào wrapper
      if (!mobileTop.contains(filter)) {
        mobileTop.appendChild(filter);
      }

      // đặt wrapper ngay sau menu
      if (menu.nextElementSibling !== mobileTop) {
        menu.insertAdjacentElement("afterend", mobileTop);
      }
    } else {
      // DESKTOP
      const mobileTop = document.querySelector(".top.mobile-top");

      // đưa filter về lại top gốc
      if (filter.parentElement !== top) {
        top.appendChild(filter);
      }

      // xóa wrapper mobile
      if (mobileTop) {
        mobileTop.remove();
      }
    }
  }

  // Chạy lần đầu
  moveFilterBar();
  // Chạy lại khi resize
  window.addEventListener("resize", moveFilterBar);

  // ====== HAMBURGER CLICK ======
 // ...existing code...
  if (ham) {
    ham.addEventListener("click", () => {
      // Toggle menu
      menu.classList.toggle("show");

      // Ở mobile thì cho filter xổ/ẩn theo luôn (nếu có)
      if (window.innerWidth <= 768 && filter) {
        filter.classList.toggle("show");
      }
      if (window.innerWidth <= 768) {
        const mobileTop = document.querySelector(".top.mobile-top");
        // nếu có wrapper mobile chứa filter thì toggle wrapper, còn không thì toggle chính filter
        if (mobileTop) {
          mobileTop.classList.toggle("show");
        } else if (filter) {
          filter.classList.toggle("show");
        }
      }
    });
  }
});


document.querySelectorAll(".task-content").forEach((el) => {
  el.addEventListener("click", (e) => {
    e.stopPropagation();
    el.classList.toggle("active");
  });
});

document.addEventListener("click", () => {
  document
    .querySelectorAll(".task-content.active")
    .forEach((el) => el.classList.remove("active"));
});

document.addEventListener("DOMContentLoaded", () => {
  const toast = document.getElementById("toast");
  if (!toast) return;

  const textEl = toast.querySelector(".toast-text");
  const closeBtn = toast.querySelector(".toast-close");

  textEl.textContent = toast.dataset.message;

  // Hiện toast
  toast.classList.add("show");

  // Auto hide sau 3s
  const timer = setTimeout(() => hideToast(), 3000);

  // Bấm X để tắt ngay
  closeBtn.addEventListener("click", () => {
    clearTimeout(timer);
    hideToast();
  });

  function hideToast() {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 350);
  }
});

function saveProgress(taskId, btn) {
  const task = btn.closest(".task");
  const range = task.querySelector('input[type="range"]');
  const progress = range.value;

  btn.disabled = true;
  btn.textContent = "Đang lưu...";

  fetch("../actions/update_progress.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${taskId}&progress=${progress}`,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        btn.textContent = "Đã lưu";
        setTimeout(() => (btn.textContent = "Lưu"), 1000);
      } else {
        alert(" Lưu tiến độ thất bại");
        btn.textContent = "Lưu";
      }
    })
    .catch(() => {
      alert(" Lỗi kết nối");
      btn.textContent = "Lưu";
    })
    .finally(() => {
      btn.disabled = false;
    });
}
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("avatarInput");
  const preview = document.getElementById("avatarPreview");

  if (!input || !preview) return;

  input.addEventListener("change", () => {
    const file = input.files[0];
    if (!file) return;

    // chỉ cho xem ảnh
    if (!file.type.startsWith("image/")) {
      alert("Vui lòng chọn file ảnh");
      input.value = "";
      return;
    }

    // preview ảnh
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
});
function togglePasswordById(inputId, iconEl) {
  const input = document.getElementById(inputId);
  if (!input || !iconEl) return;

  const img = iconEl.querySelector("img");

  if (input.type === "password") {
    input.type = "text";
    img.src = "../assets/icon/closeye.png";
  } else {
    input.type = "password";
    img.src = "../assets/icon/eye (1).png";
  }
}
