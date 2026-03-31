function prepareReport(type, title) {
  document.getElementById("report_type").value = type;
  document.getElementById("modalTitle").innerText = title;
  if (document.getElementById("quarterly_input")) {
    document.getElementById("quarterly_input").value = "0";
  }
  document.getElementById("reportModal").classList.remove("hidden");
}

function closeModal() {
  document.getElementById("reportModal").classList.add("hidden");
  document.getElementById("quarterly_input").value = "0";
}

function validateDateSelection() {
  const year = document.getElementById("year").value;
  const month = document.getElementById("month").value;
  const now = new Date();
  const currentYear = now.getFullYear();
  const currentMonth = now.getMonth() + 1;

  if (year == currentYear && month > currentMonth) {
    document.getElementById("dateError").classList.remove("hidden");
    document.getElementById("btnPreview").disabled = true;
    document.getElementById("btnDownload").disabled = true;
  } else {
    document.getElementById("dateError").classList.add("hidden");
    document.getElementById("btnPreview").disabled = false;
    document.getElementById("btnDownload").disabled = false;
  }
}
