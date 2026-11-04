  </div><!-- /.content -->
</div><!-- /.main-wrapper -->

<script>
// Mobile sidebar toggle
const sidebar = document.getElementById('adminSidebar');
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') sidebar.classList.remove('open');
});

// Auto-dismiss alerts
document.querySelectorAll('.alert[data-auto-dismiss]').forEach(alert => {
  setTimeout(() => alert.style.opacity = '0', 4000);
});

// Confirm delete actions
document.querySelectorAll('[data-confirm]').forEach(btn => {
  btn.addEventListener('click', (e) => {
    if (!confirm(btn.dataset.confirm || 'Are you sure?')) {
      e.preventDefault();
    }
  });
});

// DataTable-like search for tables
const tableSearch = document.getElementById('tableSearch');
if (tableSearch) {
  tableSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}
</script>
</body>
</html>
