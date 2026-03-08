<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
}
$username = $_SESSION['username'];
$firstLetter = strtoupper($username[0]);
include 'Mysql.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sale Management System</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="sale.css">
    
</head>
<body>
    <header class="topnav">
        <div class="topnav-brand">
            <span class="brand-icon">📖</span>
            Book Sale Management System 
        </div>
        <div class="topnav-right">
            <div class="admin-avatar"><?php echo $firstLetter; ?></div>
            <?php echo htmlspecialchars($username); ?> &nbsp;
        </div>
    </header>

    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="nav-item active" data-page="dashboard"><span class="nav-icon">🏠</span> Dashboard</div>
            <div class="nav-item" data-page="manage-books"><span class="nav-icon">📋</span> Manage Books</div>
            <div class="nav-item" data-page="book-sale"><span class="nav-icon">🛒</span> Book Sale</div>
            <div class="nav-item" data-page="" onclick="logout()"><span class="nav-icon">⏻</span> Logout</div>
        </aside>

        <main class="content">

            <!-- ══ DASHBOARD ══ -->
            <div class="page active" id="page-dashboard">
                <div class="kpi-grid">
                    <div class="kpi-card kpi-card-1">
                        <div class="kpi-header"> Total Books</div>
                        <div class="kpi-value" id="kpi-books">0</div>
                    </div>
                    <div class="kpi-card kpi-card-2">
                        <div class="kpi-header">Total Sales</div>
                        <div class="kpi-value"​ id="kpi-sales">0</div>
                    </div>
                    <div class="kpi-card kpi-card-3">
                        <div class="kpi-header">Total Revenue</div>
                        <div class="kpi-value" id="kpi-revenue">$0</div>
                    </div>
                    <div class="kpi-card kpi-card-4">
                        <div class="kpi-header"> Total Staff</div>
                        <div class="kpi-value" id="kpi-staff">0</div>
                    </div>
                </div>
                <h2>Sales History</h2>
                <table id="salesTable" border="1" cellspacing="0" cellpadding="5" style="width:100%; text-align:center;">
                    <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Discount</th>
                        <th>Total Amount</th>
                        <th>User ID</th>
                        <th>Staff Name</th>
                    </tr>
                    </thead>
                    <tbody id="salesTableBody">
                    <tr>
                        <td colspan="8">Loading sales...</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- ══ MANAGE BOOKS ══ -->
            <div class="page" id="page-manage-books">
                <div class="page-heading">Manage Books</div>
                <div class="books-toolbar">
                    <button class="btn-add" onclick="openAddModal()">+ Add Book</button>
                    <button class="btn-search"> Search ▾</button>
                </div>
                <div class="books-card">
                    <div class="datatable-controls">
                        <div class="show-entries">
                            Show
                            <select id="perPageSelect" onchange="currentPage=1;renderTable()">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                            </select>
                            entries
                        </div>
                        <div class="search-box">
                            <span>🔍</span>
                            <input type="text" id="searchInput" placeholder="Search..."
                                oninput="currentPage=1;renderTable()">
                        </div>
                    </div>

                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Date Added</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="booksTableBody"></tbody>
                    </table>

                    <div class="datatable-footer">
                        <div id="tableInfo"></div>
                        <div class="pagination" id="pagination"></div>
                    </div>
                </div>
            </div>

            <!-- ═════════ BOOK SALE PAGE ═════════ -->
            <div class="page" id="page-book-sale">

                <div class="sale-container">

                    <!-- LEFT SIDE -->
                    <div class="sale-left">

                        <!-- Customer Info -->
                        <div class="sale-card">
                            <h3>Staff Information</h3>

                            <div class="form-row">
                                <input type="text" id="customerName" placeholder="Staff Name">
                                <input type="date" id="saleDate">
                            </div>
                        </div>

                        <!-- Book Info -->
                        <div class="sale-card">
                            <h3>Book Information</h3>

                            <div class="form-row">
                                <select id="bookSelect"></select>
                                <button onclick="addToCart()" class="btn-amber">Add To Cart</button>
                            </div>

                            <table class="sale-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <tr id="cartEmpty">
                                        <td colspan="6" style="text-align: center;padding:20px;color:#b0a898;font-size:12px;">
                                            No book added yet
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment -->
                        <div class="sale-card">
                            <h3>Payment Details</h3>

                            <div class="payment-row">
                                <div>Subtotal: <strong style="font-weight: 500;">$<span id="subtotal" style="font-weight: 400;">0.00</span></strong></div>
                                <div>Discount ($): <input type="number" id="discount" value="0" style="width: 80px;flex:none;padding:5px;border-radius: 3px;border: 1px solid rgba(0, 0, 0, 0.637);font-size: 14px;" oninput="calculateTotal()"></div>
                            </div>

                            <div class="grand-total-row">
                                <span class="label">Grand Total</span>
                                <span class="amount">$<span id="grandTotal">0.00</span></span>
                            </div>

                            <div class="payment-row">
                                <div>
                                    Paid Amount:
                                    <input type="number" id="paidAmount" placeholder="0.00" min="0"
                                        style="width:100px;flex:none;padding:5px;border-radius: 3px;border: 1px solid rgba(0, 0, 0, 0.637);font-size: 14px;" oninput="calculateChange()">
                                </div>
                                <div>Change: <strong style="color:var(--green);font-weight: 500;">$<span id="change" style="font-weight: 400;">0.00</span></strong></div>
                            </div>

                            <div class="sale-buttons">
                                <button type="button" class="btn-green" onclick="saveSale()">✔ Save Sale</button>
                                <!-- <button class="btn-blue" onclick="printBill()">⎙ Print</button> -->
                                <button type="button" class="btn-red" onclick="resetSale()">✕ Reset</button>
                            </div>
                        </div>

                    </div>

                    <!-- RIGHT SIDE BILL -->
                    <div class="sale-right">
                        <div class="bill-card">
                            <h3>Sale Bill</h3>
                            <div id="billInfo">
                                <div class="bill-empty">
                                    <span class="icon">🧾</span>
                                    Bill will appear here once you add items
                                </div>
                            </div>
                            <table class="bill-table" id="billTableWrap" style="display:none">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="billBody"></tbody>
                            </table>
                            <div class="bill-total-box">
                                <span class="bt-label">Total</span>
                                <span class="bt-amount">$<span id="billTotal">0.00</span></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>
    <div class="modal-overlay" id="bookModal">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Add Book</div>
                <button class="modal-close" onclick="closeModal('bookModal')">✕</button>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Title</label><input type="text" id="f-title" placeholder="Book title">
                </div>
                <div class="form-group"><label>Author</label><input type="text" id="f-author" placeholder="Author name">
                </div>
                <div class="form-group"><label>Isbn</label><input type="text" id="f-isbn" placeholder="Enter isbn">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select id="f-category">
                        <option>Fiction</option>
                        <option>Non-Fiction</option>
                        <option>Science</option>
                        <option>History</option>
                        <option>Biography</option>
                        <option>Technology</option>
                    </select>
                </div>
                <div class="form-group"><label>Price ($)</label><input type="number" id="f-price" placeholder="0.00"
                        step="0.01" min="0"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Quantity</label><input type="number" id="f-qty" placeholder="0" min="0">
                </div>
                <div class="form-group"><label>Date Added</label><input type="date" id="f-date"></div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('bookModal')">Cancel</button>
                <button class="btn-save" onclick="saveBook()">Save Book</button>
            </div>
        </div>
    </div>
    <!-- DELETE CONFIRM MODAL -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal confirm-modal">
            <div class="confirm-icon">🗑️</div>
            <div class="modal-header" style="justify-content:center;border:none;margin-bottom:8px;">
                <div class="modal-title">Delete Book?</div>
            </div>
            <div class="confirm-msg">Are you sure you want to delete <strong id="deleteBookName"></strong>?<br>This
                action cannot be undone.</div>
            <div class="modal-actions" style="justify-content:center;">
                <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn-confirm-del" onclick="confirmDelete()">Yes, Delete</button>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"></div>
    <script>
        let nextId = 9, currentPage = 1, editingId = null, deletingId = null;

        // ── NAVIGATION ──
        document.querySelectorAll('.nav-item[data-page]').forEach(item => {
            item.addEventListener('click', () => {
                const page = item.dataset.page;
                if (!page) return;
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                item.classList.add('active');
                document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
                const target = document.getElementById('page-' + page);
                if (target) {
                    target.classList.add('active');
                    // if (page === 'manage-books') { currentPage = 1; renderTable(); }
                }
            });
        });

        // ── CHART TOGGLE ──
        function setActive(btn) {
            btn.parentElement.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        // ── RENDER TABLE ──
        function filtered() {
            const q = (document.getElementById('searchInput').value || '').toLowerCase();
            return books.filter(b =>
                b.title.toLowerCase().includes(q) ||
                b.author.toLowerCase().includes(q) ||
                b.category.toLowerCase().includes(q)
            );
        }
        
        function logout(){
            window.location.href = "logout.php";
        }
        function renderTable() {
            const perPage = parseInt(document.getElementById('perPageSelect').value);
            const rows = filtered(); // filtered() uses search input
            const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * perPage;
            const pageRows = rows.slice(start, start + perPage);

            const tbody = document.getElementById('booksTableBody');
            tbody.innerHTML = pageRows.length
                ? pageRows.map(b => `
                    <tr>
                        <td>${b.book_id}</td>
                        <td><span class="book-title-link">${b.title}</span></td>
                        <td>${b.author}</td>
                        <td>${b.isbn}</td>
                        <td>${b.category}</td>
                        <td>$${parseFloat(b.price).toFixed(2)}</td>
                        <td>${b.qty}</td>
                        <td>${b.date}</td>
                        <td>
                            <button class="btn-edit" onclick="openEditModal(${b.book_id})">✏ Edit</button>
                            <button class="btn-delete" onclick="openDeleteModal(${b.book_id})">🗑 Delete</button>
                        </td>
                    </tr>
                `).join('')
                : `<tr><td colspan="9" style="text-align:center;color:#aaa;padding:24px;">No books found.</td></tr>`;

            // Table info
            const end = Math.min(start + perPage, rows.length);
            document.getElementById('tableInfo').textContent = rows.length
                ? `Showing ${start + 1}–${end} of ${rows.length} entries`
                : 'No entries found';

            // Pagination
            let phtml = `<div class="page-btn" onclick="goPage(${currentPage - 1})">‹</div>`;
            for (let i = 1; i <= totalPages; i++)
                phtml += `<div class="page-btn${i === currentPage ? ' active' : ''}" onclick="goPage(${i})">${i}</div>`;
            phtml += `<div class="page-btn" onclick="goPage(${currentPage + 1})">›</div>`;
            phtml += `<span style="margin-left:6px;font-size:13px;color:#888;align-self:center;">Next</span>`;
            document.getElementById('pagination').innerHTML = phtml;
        }

        function goPage(p) {
            const perPage = parseInt(document.getElementById('perPageSelect').value);
            const total = Math.max(1, Math.ceil(filtered().length / perPage));
            currentPage = Math.max(1, Math.min(p, total));
            renderTable();
        }

        // ── ADD MODAL ──
        function openAddModal() {
            editingId = null;
            document.getElementById('modalTitle').textContent = 'Add Book';
            ['f-title', 'f-author', 'f-price', 'f-qty'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('f-category').value = 'Fiction';
            document.getElementById('f-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('bookModal').classList.add('open');
        }

        function openEditModal(id) {
            const b = books.find(x => x.book_id == id); if (!b) return;
            editingId = id;
            document.getElementById('modalTitle').textContent = 'Edit Book';
            document.getElementById('f-title').value = b.title;
            document.getElementById('f-author').value = b.author;
            document.getElementById('f-category').value = b.category;
            document.getElementById('f-price').value = b.price;
            document.getElementById('f-qty').value = b.qty;

            // Convert dd/mm/yyyy → yyyy-mm-dd

            const p = b.date.split('/');
            document.getElementById('f-date').value = `${p[2]}-${p[1]}-${p[0]}`;
            document.getElementById('bookModal').classList.add('open');
        }


        function saveBook() {
            const title = document.getElementById('f-title').value.trim();
            const author = document.getElementById('f-author').value.trim();
            const isbn = document.getElementById('f-isbn').value.trim();
            const category = document.getElementById('f-category').value;
            const price = document.getElementById('f-price').value;
            const qty = document.getElementById('f-qty').value;
            const date = document.getElementById('f-date').value;

            if (!title || !author || !isbn || !price || !qty || !date) {
                alert("Please fill all fields");
                return;
            }

            // Prepare form data
            const formData = new URLSearchParams();
            formData.append("title", title);
            formData.append("author", author);
            formData.append("isbn", isbn);
            formData.append("category", category);
            formData.append("price", price);
            formData.append("qty", qty);
            formData.append("date", date);
            
            // 🔥 IMPORTANT PART
            if (editingId !== null) {
                formData.append("action", "edit");
                formData.append("book_id", editingId);
            } else {
                formData.append("action", "add");
            }

            fetch("books_api.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: formData.toString()
            })
            .then(res => res.text())
            .then(data => {
                if (data === "success") {
                    showToast("Book saved to database!");
                    closeModal('bookModal');
                    // location.reload(); // reload page
                    fetchBooks();
                } else {
                    alert("Error saving book");
                }
            });
        }

        // ── DELETE ──
        function openDeleteModal(id) {
            deletingId = id;
            const b = books.find(x => x.book_id == id);
            document.getElementById('deleteBookName').textContent = b ? `"${b.title}"` : 'this book';
            document.getElementById('deleteModal').classList.add('open');
        }
        function confirmDelete() {
            if (!deletingId) return;

            const formData = new URLSearchParams();
            formData.append("action", "delete");
            formData.append("book_id", deletingId);

            fetch("books_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: formData.toString()
            })
            .then(res => res.text())
            .then(data => {
                if (data === "success") {
                    showToast('Book deleted.', '#e74c3c');
                    closeModal('deleteModal');
                    fetchBooks(); // refresh table from DB
                } else {
                    alert("Error deleting book" + data);
                }
            });
        }

        function fetchBooks() {
            fetch("books_api.php")
                .then(res => res.json())
                .then(data => {
                    books = data; // use the database data
                    renderTable();
                });
        }
        function loadDashboardData() {
            fetch("dashboard_api.php")
                .then(res => res.json())
                .then(data => {
                    document.getElementById("kpi-books").textContent = data.total_books;
                    document.getElementById("kpi-sales").textContent = data.total_sales;
                    document.getElementById("kpi-revenue").textContent = "$" + data.total_revenue.toFixed(2);
                    document.getElementById("kpi-staff").textContent = data.total_staff;
                })
                .catch(err => {
                console.error("Dashboard error:", err);
                });
        }

        // Load when page opens
         document.addEventListener("DOMContentLoaded", loadDashboardData);
        document.addEventListener('DOMContentLoaded', fetchBooks);
        function loadSalesTable() {
            const tbody = document.getElementById("salesTableBody");
            tbody.innerHTML = `<tr><td colspan="8">Loading sales...</td></tr>`;

            fetch("get_sales.php")
                .then(res => res.json())
                .then(data => {
                    if(data.status === "success" && data.data.length > 0) {
                        tbody.innerHTML = "";
                        data.data.forEach(sale => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${sale.sale_id}</td>
                            <td>${sale.title}</td>
                            <td>$${parseFloat(sale.price).toFixed(2)}</td>
                            <td>${sale.qty}</td>
                            <td>$${parseFloat(sale.discount).toFixed(2)}</td>
                            <td>$${parseFloat(sale.total_amount).toFixed(2)}</td>
                            <td>${sale.user_id}</td>
                            <td>${sale.staff_name}</td>
                        `;
                        tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="8">No sales found</td></tr>`;
                    }
                })
                .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="8">Error loading sales</td></tr>`;
                });
        }

        // Load table on page load
        document.addEventListener("DOMContentLoaded", loadSalesTable);

        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
        });

        // ── TOAST ──
        function showToast(msg, color = '#27ae60') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.style.background = color;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2800);
        }
        
    </script>
    <script src="booksale.js"></script>
</body>
</html>