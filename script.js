function applyFilter() {
    let name = document.getElementById("filter_name").value.trim();
    let day = document.getElementById("filter_day").value;
    let month = document.getElementById("filter_month").value;
    let year = document.getElementById("filter_year").value;
    let time = document.getElementById("filter_time").value;
    let status = document.getElementById("filter_status").value;

    let query = [];
    if (name) query.push("name=" + encodeURIComponent(name));
    if (day) query.push("day=" + day);
    if (month) query.push("month=" + month);
    if (year) query.push("year=" + year);
    if (time) query.push("time=" + time);
    if (status) query.push("status=" + status);
    window.location.href = "search.php?" + query.join("&");
}

function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
}

const selectBox = document.querySelector('.select-selected');
const selectItems = document.querySelector('.select-items');
const hiddenInput = document.getElementById('filter_status');
const displayText = document.getElementById('filter_status_display');

if (selectBox && selectItems) {
    selectBox.addEventListener('click', function(e) {
        e.stopPropagation();
        selectItems.classList.toggle('show');
    });

    selectItems.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    selectItems.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', function() {
            hiddenInput.value = this.getAttribute('data-value');
            displayText.textContent = this.textContent;
            selectItems.classList.remove('show');
        });
    });

    document.addEventListener('click', function() {
        selectItems.classList.remove('show');
    });
}
