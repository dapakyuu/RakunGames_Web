document.addEventListener("DOMContentLoaded", function () {
  const toggleButton = document.getElementById("userMenuToggle");
  const dropdownIcon = document.getElementById("dropdownIcon");
  const pushMenuButton = document.querySelector("[data-widget='pushmenu']");
  const logout = document.getElementById("logout");
  let isExpand = false;

  toggleButton.addEventListener("click", function () {
    const isExpanded = toggleButton.getAttribute("aria-expanded") === "true";
    // Ganti ikon berdasarkan kondisi dropdown
    dropdownIcon.classList.toggle("fa-chevron-left", isExpanded);
    dropdownIcon.classList.toggle("fa-chevron-down", !isExpanded);
  });

  pushMenuButton.addEventListener("click", function () {
    if (isExpand) {
      dropdownIcon.classList.remove("hide-icon");
      isExpand = false;
      logout.innerHTML = '<i class="fas fa-sign-out-alt"></i> Sign Out';
    } else {
      dropdownIcon.classList.add("hide-icon");
      isExpand = true;
      logout.innerHTML = '<i class="fas fa-sign-out-alt"></i>';
    }
  });
});

$(function () {
  bsCustomFileInput.init();
});

document.addEventListener("DOMContentLoaded", function () {
  // Event listener untuk input file
  document.querySelectorAll(".custom-file-input").forEach((input) => {
    input.addEventListener("change", function () {
      // Ambil nama file yang dipilih
      const fileName = this.files[0]?.name || "Choose File";
      // Perbarui teks label
      this.nextElementSibling.textContent = fileName;
    });
  });
});
