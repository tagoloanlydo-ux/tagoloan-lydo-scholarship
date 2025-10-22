function showTable() {
    document.getElementById("tableView").classList.remove("hidden");
    document.getElementById("listView").classList.add("hidden");
    document.querySelector('.tab.active').classList.remove('active');
    document.querySelectorAll('.tab')[0].classList.add('active');
    localStorage.setItem("viewMode", "table"); // save preference
}

function showList() {
    document.getElementById("listView").classList.remove("hidden");
    document.getElementById("tableView").classList.add("hidden");
    document.querySelector('.tab.active').classList.remove('active');
    document.querySelectorAll('.tab')[1].classList.add('active');
    localStorage.setItem("viewMode", "list"); // save preference
}

// Restore view mode on page load
window.addEventListener("DOMContentLoaded", () => {
    const viewMode = localStorage.getItem("viewMode");
    if (viewMode === "list") {
        showList();
    } else {
        showTable();
    }
});
