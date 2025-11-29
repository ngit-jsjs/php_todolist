function applyFilter() {
  let name  = document.getElementById("filter_name").value.trim();
    let day   = document.getElementById("filter_day").value;
    let month = document.getElementById("filter_month").value;
    let year  = document.getElementById("filter_year").value;
    let time  = document.getElementById("filter_time").value;
    let status= document.getElementById("filter_status").value;

    let query = [];

    if (name)  query.push("name=" + encodeURIComponent(name));
    if (day)   query.push("day=" + day);
    if (month) query.push("month=" + month);
    if (year)  query.push("year=" + year);
    if (time)  query.push("time=" + time);
    if (status)query.push("status=" + status);
    window.location.href = "search.php?" + query.join("&");
}
