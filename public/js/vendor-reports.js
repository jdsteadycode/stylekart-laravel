function prepareReport(type, title) {
  const reportTypeInput = document.getElementById("report_type");
  const modalTitle = document.getElementById("modalTitle");
  const reportModal = document.getElementById("reportModal");

  if (reportTypeInput && modalTitle && reportModal) {
    reportTypeInput.value = type;
    modalTitle.innerText = title;
    reportModal.classList.remove("hidden");
    validateDateSelection(); // Run validation immediately on open
  }
}

function closeModal() {
  const reportModal = document.getElementById("reportModal");
  if (reportModal) {
    reportModal.classList.add("hidden");
  }
}

function validateDateSelection() {
  // Current date for 2026 validation
  const now = new Date();
  const currentYear = now.getFullYear();
  const currentMonth = now.getMonth() + 1; // JS months are 0-indexed
  const currentDay = now.getDate();

  // Get selected values from the DOM
  const selYear = parseInt(document.getElementById("year").value);
  const selMonthInput = document.getElementById("month").value;
  const selDayInput = document.getElementById("day").value;

  // Convert empty strings (Full Year/Month) to 0 for logic
  const selMonth = selMonthInput ? parseInt(selMonthInput) : 0;
  const selDay = selDayInput ? parseInt(selDayInput) : 0;

  let isFuture = false;

  // Validation Logic
  if (selYear > currentYear) {
    isFuture = true;
  } else if (selYear === currentYear) {
    if (selMonth > currentMonth) {
      isFuture = true;
    } else if (selMonth === currentMonth && selDay > currentDay) {
      isFuture = true;
    }
  }

  // UI Feedback
  const errorMsg = document.getElementById("dateError");
  const previewBtn = document.getElementById("btnPreview");
  const downloadBtn = document.getElementById("btnDownload");

  if (isFuture) {
    errorMsg.classList.remove("hidden");
    [previewBtn, downloadBtn].forEach((btn) => {
      btn.disabled = true;
      btn.classList.add("opacity-50", "cursor-not-allowed");
    });
  } else {
    errorMsg.classList.add("hidden");
    [previewBtn, downloadBtn].forEach((btn) => {
      btn.disabled = false;
      btn.classList.remove("opacity-50", "cursor-not-allowed");
    });
  }
}

// Close modal when clicking outside the content area
window.onclick = function (event) {
  const modal = document.getElementById("reportModal");
  if (event.target == modal) {
    closeModal();
  }
};
