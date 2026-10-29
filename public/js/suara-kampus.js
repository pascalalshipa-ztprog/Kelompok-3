/**
 * SuaraKampus Kelompok 3 - Frontend UI/UX Controller
 * Modern, robust, and interactive experience for campus complaint portal.
 */
(() => {
  const $ = (selector, root = document) => root ? root.querySelector(selector) : null;
  const $$ = (selector, root = document) => root ? [...root.querySelectorAll(selector)] : [];
  const on = (el, event, handler) => el && el.addEventListener(event, handler);

  // Default fallback data jika backend belum mengirimkan data
  const defaultInitialReports = [
    {
      id: "PGD-2025-084",
      title: "AC ruang kelas Gedung B tidak berfungsi",
      category: "Fasilitas dan Sarana Prasarana",
      location: "Gedung B Lantai 3, Ruang B.301",
      date: "22 Mei 2025",
      status: "Diproses",
      description: "AC di ruang B.301 mengeluarkan suara bising dan hembusan angin tidak dingin sejak 3 hari yang lalu, mengganggu kenyamanan perkuliahan mahasiswa.",
      response: "Petugas teknisi sarana dan prasarana telah menjadwalkan pengecekan unit kompresor pendingin ruangan pada Jumat pagi.",
      attachment: "foto_ac_rusak.jpg"
    },
    {
      id: "PGD-2025-071",
      title: "Jaringan Wi-Fi perpustakaan tidak stabil",
      category: "Layanan Digital & Fasilitas",
      location: "Perpustakaan Pusat Lt. 2",
      date: "18 Mei 2025",
      status: "Selesai",
      description: "Koneksi Wi-Fi sering terputus saat jam ramai (12.00 - 15.00 WIB) sehingga menyulitkan mahasiswa yang sedang mengerjakan tugas akhir.",
      response: "Access point di ruang baca barat telah diganti dengan perangkat throughput tinggi serta alokasi bandwidth ditingkatkan.",
      attachment: null
    },
    {
      id: "PGD-2025-063",
      title: "Keterlambatan input nilai KHS Semester Ganjil",
      category: "Akademik",
      location: "Fakultas Ilmu Komputer",
      date: "10 Mei 2025",
      status: "Selesai",
      description: "Nilai mata kuliah Pemrograman Web Lanjut belum muncul di portal akademik padahal batas pengisian KRS semester baru sudah dekat.",
      response: "Dosen pengampu telah mengunggah nilai akhir ke sistem akademik BAAK. Nilai kini dapat diakses di portal SIAKAD.",
      attachment: null
    },
    {
      id: "PGD-2025-052",
      title: "Klarifikasi penagihan ganda praktikum laboratorium",
      category: "Administrasi dan Keuangan",
      location: "Bagian Keuangan Rektorat",
      date: "28 Apr 2025",
      status: "Selesai",
      description: "Tercatat tagihan praktikum dua kali pada invoice semester 4. Mohon bantuan untuk penyesuaian nominal.",
      response: "Biro Keuangan telah mengoreksi invoice terkait. Kelebihan pembayaran telah dikonfirmasi dan dialihkan ke periode berikutnya.",
      attachment: "bukti_pembayaran.pdf"
    },
    {
      id: "PGD-2025-045",
      title: "Proyektor LCD Gedung C sering mati mendadak",
      category: "Fasilitas dan Sarana Prasarana",
      location: "Ruang C.204",
      date: "15 Apr 2025",
      status: "Diproses",
      description: "Lampu proyektor sering mati sendiri di tengah sesi presentasi kuliah dikarenakan indikator overheat menyala.",
      response: "Unit pendingin proyektor sedang dibersihkan dan unit lampu cadangan telah dipasang sementara.",
      attachment: null
    }
  ];

  // Sinkronisasi data dari window, default, dan LocalStorage
  const loadReports = () => {
    let sourceReports = (window.SUARA_KAMPUS_REPORTS && window.SUARA_KAMPUS_REPORTS.length > 0)
      ? window.SUARA_KAMPUS_REPORTS
      : defaultInitialReports;

    try {
      const stored = localStorage.getItem("SUARA_KAMPUS_USER_REPORTS");
      if (stored) {
        const userReports = JSON.parse(stored);
        if (Array.isArray(userReports) && userReports.length > 0) {
          // Gabungkan laporan buatan user yang belum ada di source
          const existingIds = new Set(sourceReports.map(r => r.id));
          const newItems = userReports.filter(r => !existingIds.has(r.id));
          return [...newItems, ...sourceReports];
        }
      }
    } catch (e) {
      console.warn("Gagal membaca dari localStorage", e);
    }
    return [...sourceReports];
  };

  let reports = loadReports();
  let selectedId = null;

  // Elemen DOM Shell & Drawer
  const sidebar = $("#sidebar");
  const sidebarBackdrop = $("#sidebar-backdrop");
  const drawer = $("#report-drawer");
  const drawerBackdrop = $("#drawer-backdrop");
  const toast = $("#toast");
  const toastMessage = $("#toast-message");

  // Helper Escape HTML
  const escapeHtml = (value = "") =>
    String(value).replace(/[&<>"']/g, (c) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;",
    })[c]);

  // Helper SVG Icon
  const icon = (name, size = 18) =>
    `<svg aria-hidden="true" class="icon" width="${size}" height="${size}"><use href="#i-${name}"></use></svg>`;

  // Helper Status Badge
  const statusBadge = (status = "Pending") => {
    const s = String(status).toLowerCase();
    let badgeClass = "status-pending";
    if (s.includes("selesai")) badgeClass = "status-selesai";
    else if (s.includes("proses")) badgeClass = "status-diproses";
    else if (s.includes("tolak")) badgeClass = "status-ditolak";

    return `<span class="status ${badgeClass}"><span class="status-dot"></span>${escapeHtml(status)}</span>`;
  };

  // Toast Notification
  function showToast(message) {
    if (!toast || !toastMessage) return;
    toastMessage.textContent = message;
    toast.hidden = false;
    window.clearTimeout(showToast.timeout);
    showToast.timeout = window.setTimeout(() => {
      toast.hidden = true;
    }, 3200);
  }
  window.showToast = showToast;

  // Sidebar Mobile Toggle
  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.add("sidebar-open");
    if (sidebarBackdrop) sidebarBackdrop.hidden = false;
    document.body.classList.add("sidebar-locked");
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove("sidebar-open");
    if (sidebarBackdrop) sidebarBackdrop.hidden = true;
    document.body.classList.remove("sidebar-locked");
  }

  // Drawer Modal Detail Pengaduan
  function openDrawer(id) {
    const report = reports.find((item) => item.id === id);
    if (!report || !drawer) return;

    selectedId = id;

    // Isi field detail
    const idEl = $('[data-drawer="id"]');
    if (idEl) idEl.textContent = report.id;

    const catEl = $('[data-drawer="category"]');
    if (catEl) catEl.textContent = report.category;

    const titleEl = $('[data-drawer="title"]');
    if (titleEl) titleEl.textContent = report.title;

    const statusEl = $('[data-drawer="status"]');
    if (statusEl) statusEl.innerHTML = statusBadge(report.status);

    const dateEl = $('[data-drawer="date"]');
    if (dateEl) dateEl.textContent = report.date;

    const locEl = $('[data-drawer="location"]');
    if (locEl) locEl.textContent = report.location || "Area Kampus Terpadu";

    const descEl = $('[data-drawer="description"]');
    if (descEl) descEl.textContent = report.description;

    const respEl = $('[data-drawer="response"]');
    if (respEl) {
      respEl.textContent = report.response || "Laporan sedang dalam antrean verifikasi petugas bidang terkait.";
    }

    const statusSelect = $("#drawer-status-select");
    if (statusSelect) statusSelect.value = report.status;

    // Attachment Section
    const attachSection = $("#drawer-attachment-section");
    const attachName = $("#drawer-attachment-name");
    if (attachSection) {
      if (report.attachment) {
        attachSection.hidden = false;
        if (attachName) attachName.textContent = report.attachment;
      } else {
        attachSection.hidden = true;
      }
    }

    // Timeline Rendering
    const timeline = $("#drawer-timeline");
    if (timeline) {
      const isVerified = report.status !== "Pending";
      const isCompleted = report.status === "Selesai";
      timeline.innerHTML = `
        <div class="done">
          <i></i>
          <span>
            <strong>Laporan Berhasil Diterima</strong>
            <small>${escapeHtml(report.date)} • Sistem SuaraKampus</small>
          </span>
        </div>
        <div class="${isVerified ? "done" : ""}">
          <i></i>
          <span>
            <strong>Verifikasi Administrasi Petugas</strong>
            <small>${isVerified ? "Laporan telah diverifikasi oleh biro sarpras" : "Menunggu peninjauan antrean"}</small>
          </span>
        </div>
        <div class="${isCompleted ? "done" : ""}">
          <i></i>
          <span>
            <strong>Tindak Lanjut & Penanganan Selesai</strong>
            <small>${isCompleted ? "Masalah telah ditangani tuntas di lapangan" : "Dalam proses penanganan teknisi"}</small>
          </span>
        </div>
      `;
    }

    drawer.hidden = false;
    if (drawerBackdrop) drawerBackdrop.hidden = false;
    document.body.classList.add("drawer-locked");
  }

  function closeDrawer() {
    if (!drawer) return;
    drawer.hidden = true;
    if (drawerBackdrop) drawerBackdrop.hidden = true;
    document.body.classList.remove("drawer-locked");
    selectedId = null;
  }

  window.openDrawerModal = openDrawer;
  window.closeDrawerModal = closeDrawer;
  window.openFirstReport = () => reports[0] && openDrawer(reports[0].id);

  // Update Dynamic Statistics Counter
  function updateCounters() {
    const total = reports.length;
    const processing = reports.filter(r => r.status === "Diproses").length;
    const completed = reports.filter(r => r.status === "Selesai").length;

    $$("[data-total]").forEach(el => el.textContent = total);
    $$("[data-processing]").forEach(el => el.textContent = processing);
    $$("[data-completed]").forEach(el => el.textContent = completed);
    $$("[data-total-badge]").forEach(el => el.textContent = total);
  }

  // Riwayat Table Filtering
  function filterReports() {
    const searchVal = ($("[data-filter-search]")?.value || "").toLowerCase().trim();
    const catVal = $("[data-filter-category]")?.value || "Semua";
    const statusVal = $("[data-filter-status]")?.value || "Semua";

    return reports.filter((item) => {
      const matchSearch = !searchVal ||
        item.title.toLowerCase().includes(searchVal) ||
        item.id.toLowerCase().includes(searchVal) ||
        (item.location && item.location.toLowerCase().includes(searchVal)) ||
        item.category.toLowerCase().includes(searchVal);

      const matchCategory = catVal === "Semua" || item.category === catVal;
      const matchStatus = statusVal === "Semua" || item.status === statusVal;

      return matchSearch && matchCategory && matchStatus;
    });
  }

  function renderHistoryTable() {
    const historyContainer = $('[data-table="history"]');
    if (!historyContainer) return;

    const filtered = filterReports();
    const tableFooter = $("#table-footer");

    if (filtered.length === 0) {
      historyContainer.innerHTML = `
        <div class="empty-state">
          ${icon("search", 24)}
          <strong>Pengaduan tidak ditemukan</strong>
          <span>Coba sesuaikan kata kunci pencarian atau ubah filter kategori/status di atas.</span>
        </div>
      `;
    } else {
      historyContainer.innerHTML = `
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Pengaduan & Tiket</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th aria-label="Aksi"></th>
              </tr>
            </thead>
            <tbody>
              ${filtered.map(report => `
                <tr data-report-id="${escapeHtml(report.id)}">
                  <td>
                    <strong>${escapeHtml(report.title)}</strong>
                    <small>${escapeHtml(report.id)}</small>
                  </td>
                  <td>${escapeHtml(report.category)}</td>
                  <td>${escapeHtml(report.location || "Area Kampus")}</td>
                  <td>${escapeHtml(report.date)}</td>
                  <td>${statusBadge(report.status)}</td>
                  <td>
                    <button aria-label="Lihat detail tiket ${escapeHtml(report.id)}" class="row-arrow" type="button">
                      ${icon("chevron", 18)}
                    </button>
                  </td>
                </tr>
              `).join("")}
            </tbody>
          </table>
        </div>
      `;

      // Attach click events to rows
      $$("[data-report-id]", historyContainer).forEach((row) => {
        row.addEventListener("click", () => openDrawer(row.dataset.reportId));
      });
    }

    if (tableFooter) {
      tableFooter.textContent = `Menampilkan ${filtered.length} dari ${reports.length} pengaduan`;
    }
  }

  function renderCompactTable() {
    const compactContainer = $('[data-table="compact"]');
    if (!compactContainer) return;

    const items = reports.slice(0, 4);
    compactContainer.innerHTML = `
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Pengaduan</th>
              <th>Kategori</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th aria-label="Aksi"></th>
            </tr>
          </thead>
          <tbody>
            ${items.map(report => `
              <tr data-report-id="${escapeHtml(report.id)}">
                <td>
                  <strong>${escapeHtml(report.title)}</strong>
                  <small>${escapeHtml(report.id)} • ${escapeHtml(report.location || "Kampus")}</small>
                </td>
                <td>${escapeHtml(report.category)}</td>
                <td>${escapeHtml(report.date)}</td>
                <td>${statusBadge(report.status)}</td>
                <td>
                  <button aria-label="Lihat detail tiket ${escapeHtml(report.id)}" class="row-arrow" type="button">
                    ${icon("chevron", 18)}
                  </button>
                </td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    `;

    $$("[data-report-id]", compactContainer).forEach((row) => {
      row.addEventListener("click", () => openDrawer(row.dataset.reportId));
    });
  }

  // Quick Search Navbar Popover
  const quickSearchInput = $("#quick-search-input");
  const quickSearchResults = $("#quick-search-results");
  const quickSearchList = $("#quick-search-list");
  const quickSearchCount = $("#quick-search-count");

  function handleQuickSearch(e) {
    const query = (e.target.value || "").trim().toLowerCase();
    if (!query) {
      if (quickSearchResults) quickSearchResults.hidden = true;
      return;
    }

    const matches = reports.filter(r =>
      r.title.toLowerCase().includes(query) ||
      r.id.toLowerCase().includes(query) ||
      (r.location && r.location.toLowerCase().includes(query)) ||
      r.category.toLowerCase().includes(query)
    );

    if (quickSearchResults && quickSearchList) {
      quickSearchResults.hidden = false;
      if (quickSearchCount) quickSearchCount.textContent = `${matches.length} ditemukan`;

      if (matches.length === 0) {
        quickSearchList.innerHTML = `
          <div style="padding: 16px; text-align: center; color: var(--ink-muted); font-size: 11px;">
            Tidak ditemukan pengaduan yang sesuai "${escapeHtml(query)}"
          </div>
        `;
      } else {
        quickSearchList.innerHTML = matches.slice(0, 5).map(m => `
          <div class="quick-search-item" data-id="${escapeHtml(m.id)}">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <small style="color: var(--teal); font-weight: 700;">${escapeHtml(m.id)}</small>
              ${statusBadge(m.status)}
            </div>
            <strong>${escapeHtml(m.title)}</strong>
            <small>${escapeHtml(m.location || m.category)}</small>
          </div>
        `).join("");

        $$(".quick-search-item", quickSearchList).forEach(item => {
          item.addEventListener("click", () => {
            quickSearchResults.hidden = true;
            quickSearchInput.value = "";
            openDrawer(item.dataset.id);
          });
        });
      }
    }
  }

  // Notifikasi Popover Toggle
  const notificationButton = $("#notification-button");
  const notificationPopover = $("#notification-popover");

  // Inisialisasi Event Listener
  function initEvents() {
    // Menu mobile & Drawer dismiss
    on($("#menu-button"), "click", openSidebar);
    $$("[data-close-sidebar]").forEach(btn => on(btn, "click", closeSidebar));
    on(sidebarBackdrop, "click", closeSidebar);
    on($("#close-drawer"), "click", closeDrawer);
    on(drawerBackdrop, "click", closeDrawer);

    // Auto close sidebar ketika item menu diklik pada layar mobile/minimalis
    if (sidebar) {
      $$(".nav-item", sidebar).forEach(item => {
        on(item, "click", () => {
          if (window.innerWidth <= 1024) {
            closeSidebar();
          }
        });
      });
    }

    // Auto reset sidebar lock jika ukuran window diperbesar kembali ke desktop
    window.addEventListener("resize", () => {
      if (window.innerWidth > 1024) {
        closeSidebar();
      }
    });

    // Quick Search navbar
    on(quickSearchInput, "input", handleQuickSearch);
    on(quickSearchInput, "focus", (e) => {
      if (e.target.value.trim()) handleQuickSearch(e);
    });

    // Keyboard Shortcut (⌘K / Ctrl+K & Escape)
    document.addEventListener("keydown", (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        if (quickSearchInput) {
          quickSearchInput.focus();
          quickSearchInput.select();
        }
      } else if (e.key === "Escape") {
        closeSidebar();
        closeDrawer();
        if (quickSearchResults) quickSearchResults.hidden = true;
        if (notificationPopover) notificationPopover.hidden = true;
      }
    });

    // Klik luar untuk menutup popovers
    document.addEventListener("click", (e) => {
      if (quickSearchResults && !quickSearchResults.hidden) {
        if (!e.target.closest(".quick-search") && !e.target.closest("#quick-search-results")) {
          quickSearchResults.hidden = true;
        }
      }
      if (notificationPopover && !notificationPopover.hidden) {
        if (!e.target.closest("#notification-button") && !e.target.closest("#notification-popover")) {
          notificationPopover.hidden = true;
        }
      }
    });

    // Notifikasi Popover Toggle
    on(notificationButton, "click", (e) => {
      e.stopPropagation();
      if (!notificationPopover) return;
      notificationPopover.hidden = !notificationPopover.hidden;
      // Hilangkan unread dot ketika sudah dibuka
      const dot = $("#unread-dot");
      if (dot) dot.style.display = "none";
    });

    // Theme Mode Toggle (Terang / Gelap)
    on($("#theme-toggle"), "click", () => {
      const isDark = document.documentElement.getAttribute("data-theme") === "dark";
      const nextTheme = isDark ? "light" : "dark";
      document.documentElement.setAttribute("data-theme", nextTheme);
      try {
        localStorage.setItem("suara_kampus_theme", nextTheme);
      } catch (e) {
        console.warn("Gagal menyimpan preferensi tema", e);
      }
      showToast(nextTheme === "dark" ? "Mode Gelap diaktifkan." : "Mode Terang diaktifkan.");
    });

    // Admin Status Updater pada Drawer
    on($("#save-status-btn"), "click", () => {
      if (!selectedId) return;
      const statusSelect = $("#drawer-status-select");
      if (!statusSelect) return;
      const newStatus = statusSelect.value;
      const report = reports.find(r => r.id === selectedId);
      if (report) {
        report.status = newStatus;
        try {
          localStorage.setItem("SUARA_KAMPUS_USER_REPORTS", JSON.stringify(reports));
        } catch (e) {
          console.warn("Gagal menyimpan status", e);
        }
        updateCounters();
        renderCompactTable();
        renderHistoryTable();
        openDrawer(selectedId);
        showToast(`Status ${selectedId} berhasil diubah menjadi ${newStatus}!`);
      }
    });

    // Filter Riwayat Pengaduan
    on($("[data-filter-search]"), "input", renderHistoryTable);
    on($("[data-filter-category]"), "change", renderHistoryTable);
    on($("[data-filter-status]"), "change", renderHistoryTable);

    // CSV Export
    on($("#export-button"), "click", () => {
      const header = "ID Pengaduan,Judul Pengaduan,Kategori,Lokasi,Tanggal,Status\n";
      const rows = reports.map(r =>
        [r.id, r.title, r.category, r.location || "-", r.date, r.status]
          .map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(",")
      ).join("\n");

      const blob = new Blob(["\uFEFF" + header + rows], { type: "text/csv;charset=utf-8;" });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = `rekap-pengaduan-suarakampus-${new Date().toISOString().slice(0, 10)}.csv`;
      link.click();
      URL.revokeObjectURL(url);
      showToast("Rekap CSV riwayat pengaduan berhasil diunduh.");
    });

    // Halaman Buat Pengaduan (Create Form Interactivity)
    const complaintForm = $("#complaint-form");
    if (complaintForm) {
      // Pilihan Kategori Interaktif
      $$(".complaint-type-option input").forEach((input) => {
        on(input, "change", () => {
          $$(".complaint-type-option").forEach((opt) => {
            const isChecked = $("input", opt).checked;
            opt.classList.toggle("selected", isChecked);
          });
        });
      });

      // Live Character Counter
      const descInput = $("#description");
      const charCounter = $("#character-count");
      if (descInput && charCounter) {
        // Set awal
        charCounter.textContent = `${descInput.value.length}/500 karakter`;
        on(descInput, "input", (e) => {
          const len = e.target.value.length;
          charCounter.textContent = `${len}/500 karakter`;
          if (len < 10) {
            charCounter.style.color = "var(--coral)";
          } else if (len >= 450) {
            charCounter.style.color = "var(--amber)";
          } else {
            charCounter.style.color = "var(--teal)";
          }
        });
      }

      // Drag & Drop / Upload File Preview
      const fileInput = $("#attachment");
      const dropzone = $("#upload-dropzone");
      const previewCard = $("#file-preview-card");
      const previewName = $("#preview-file-name");
      const previewSize = $("#preview-file-size");
      const removeFileBtn = $("#remove-file-button");

      if (fileInput && previewCard) {
        on(fileInput, "change", () => {
          if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const sizeInKb = (file.size / 1024).toFixed(1);
            const sizeText = file.size > 1024 * 1024
              ? `${(file.size / (1024 * 1024)).toFixed(2)} MB`
              : `${sizeInKb} KB`;

            if (previewName) previewName.textContent = file.name;
            if (previewSize) previewSize.textContent = sizeText;
            previewCard.hidden = false;
            if (dropzone) dropzone.style.display = "none";
          }
        });

        on(removeFileBtn, "click", () => {
          fileInput.value = "";
          previewCard.hidden = true;
          if (dropzone) dropzone.style.display = "flex";
        });
      }

      // Validasi & Penyimpanan Data Pengaduan
      on(complaintForm, "submit", (e) => {
        const errorAlert = $("#complaint-error");
        const categoryInput = $('input[name="kategori"]:checked');
        const descriptionVal = ($("#description")?.value || "").trim();
        const judulVal = ($("#judul")?.value || "").trim();
        const lokasiVal = ($("#lokasi")?.value || "").trim();

        if (!categoryInput || descriptionVal.length < 10 || !judulVal) {
          e.preventDefault();
          if (errorAlert) {
            errorAlert.hidden = false;
            errorAlert.scrollIntoView({ behavior: "smooth", block: "center" });
          }
          return;
        }

        if (errorAlert) errorAlert.hidden = true;

        // Buat objek laporan baru untuk persistensi instan
        const now = new Date();
        const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
        const dateFormatted = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        const newId = `PGD-2025-${String(85 + reports.length).padStart(3, "0")}`;

        const newReport = {
          id: newId,
          title: judulVal,
          category: categoryInput.value,
          location: lokasiVal || "Kampus Terpadu",
          date: dateFormatted,
          status: "Pending",
          description: descriptionVal,
          response: "Pengaduan baru saja diterima sistem dan sedang menunggu alokasi ke tim teknisi/sarpras.",
          attachment: fileInput?.files?.[0]?.name || null
        };

        // Simpan ke localStorage agar sinkron seketika
        try {
          const stored = localStorage.getItem("SUARA_KAMPUS_USER_REPORTS");
          const userReports = stored ? JSON.parse(stored) : [];
          userReports.unshift(newReport);
          localStorage.setItem("SUARA_KAMPUS_USER_REPORTS", JSON.stringify(userReports));
        } catch (err) {
          console.warn("Gagal menyimpan ke localStorage", err);
        }

        // Tampilkan feedback toast
        showToast("Pengaduan berhasil disampaikan!");
      });
    }
  }

  // Jalankan render awal
  updateCounters();
  renderCompactTable();
  renderHistoryTable();
  initEvents();
})();
