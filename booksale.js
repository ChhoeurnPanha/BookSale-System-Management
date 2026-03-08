////-----Book Sale ------- ////

// ── SAMPLE DATA ──
// const book_b = [
//   { id: 1, title: "The Great Gatsby", price: 12.99 },
//   { id: 2, title: "To Kill a Mockingbird", price: 10.99 },
//   { id: 3, title: "1984", price: 9.99 },
//   { id: 4, title: "Pride and Prejudice", price: 8.49 },
//   { id: 5, title: "The Catcher in the Rye", price: 11.5 },
//   { id: 6, title: "Brave New World", price: 10.0 },
//   { id: 7, title: "The Hobbit", price: 14.99 },
//   { id: 8, title: "Harry Potter I", price: 16.99 },
//   { id: 9, title: "Sapiens", price: 18.0 },
//   { id: 10, title: "Atomic Habits", price: 15.49 },
//   { id: 11, title: "Math ", price: 1.49 },
// ];
let books = [];
let cart = [];

function loadBooks() {
  fetch("get_books_sale.php")
    .then((res) => res.json())
    .then((data) => {
      books = data;

      const sel = document.getElementById("bookSelect");
      sel.innerHTML = "";

      books.forEach((book) => {
        const opt = document.createElement("option");
        opt.value = book.book_id;
        opt.textContent = `${book.title} — $${parseFloat(book.price).toFixed(
          2
        )}`;
        sel.appendChild(opt);
      });
    });
}

document.addEventListener("DOMContentLoaded", () => {
  loadBooks();

  const today = new Date().toISOString().split("T")[0];
  document.getElementById("saleDate").value = today;
});

// ── INIT ──
// (function init() {
//   const sel = document.getElementById("bookSelect");
//   book_b.forEach((b) => {
//     const opt = document.createElement("option");
//     opt.value = b.id;
//     opt.textContent = `${b.title}  —  $${b.price.toFixed(2)}`;
//     sel.appendChild(opt);
//   });
//   const today = new Date().toISOString().split("T")[0];
//   document.getElementById("saleDate").value = today;
// })();

// ── CART ──
function addToCart() {
  const id = parseInt(document.getElementById("bookSelect").value);
  const bookd = books.find((b) => b.book_id == id);
  const existing = cart.find((c) => c.book_id == id);
  if (existing) {
    existing.qty++;
  } else {
    // cart.push({ ...bookd, qty: 1 });
    cart.push({
      book_id: bookd.book_id,
      id: bookd.book_id,
      title: bookd.title,
      price: parseFloat(bookd.price),
      qty: 1,
    });
  }
  renderCart();
  showToasts(`"${bookd.title}" added to cart`);
}

function removeFromCart(id) {
  cart = cart.filter((c) => c.id != id);
  renderCart();
}

function changeQty(id, delta) {
  const item = cart.find((c) => c.id == id);
  if (!item) return;
  item.qty = Math.max(1, item.qty + delta);
  renderCart();
}

function setQty(id, val) {
  const item = cart.find((c) => c.id == id);
  if (!item) return;
  const q = parseInt(val);
  item.qty = isNaN(q) || q < 1 ? 1 : q;
  renderCart();
}

function renderCart() {
  const tbody = document.getElementById("cartBody");
  tbody.innerHTML = "";

  if (cart.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:20px;color:#b0a898;font-size:12px;">No books added yet</td></tr>`;
    updateTotals();
    updateBill();
    return;
  }

  cart.forEach((item) => {
    const total = item.price * item.qty;
    const tr = document.createElement("tr");
    tr.className = "cart-item";
    tr.innerHTML = `
                <td>${item.id}</td>
                <td>${item.title}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                <div class="qty-cell">
                    <button class="qty-btn" onclick="changeQty(${
                      item.id
                    },-1)">−</button>
                    <input type="number" value="${
                      item.qty
                    }" min="1" style="width:50px;text-align:center;padding:4px 5px;min-width:unset;border-radius: 5px;border: 1px solid rgba(0, 0, 0, 0.637);" onchange="setQty(${
      item.id
    },this.value)">
                    <button class="qty-btn" onclick="changeQty(${
                      item.id
                    },1)">+</button>
                </div>
                </td>
                <td>$${total.toFixed(2)}</td>
                <td><button class="remove-btn" onclick="removeFromCart(${
                  item.id
                })">Remove</button></td>
            `;
    tbody.appendChild(tr);
  });

  updateTotals();
  updateBill();
}
function updateBill() {
  const name = document.getElementById("customerName").value || "—";
  const date = document.getElementById("saleDate").value || "—";

  const billInfoEl = document.getElementById("billInfo");
  const billTableWrap = document.getElementById("billTableWrap");
  const billBody = document.getElementById("billBody");

  if (cart.length === 0) {
    billInfoEl.innerHTML = `<div class="bill-empty"><span class="icon">🧾</span>Bill will appear here once you add items</div>`;
    billTableWrap.style.display = "none";
    return;
  }

  billInfoEl.innerHTML = `
      <div class="info-row"><span class="info-label">Staff</span><span class="info-value">${name}</span></div>
      <div class="info-row"><span class="info-label">Date</span><span class="info-value">${date}</span></div>
    `;

  billTableWrap.style.display = "table";
  billBody.innerHTML = "";
  cart.forEach((item) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `<td>${item.title}</td><td>${item.qty}</td><td>$${(
      item.price * item.qty
    ).toFixed(2)}</td>`;
    billBody.appendChild(tr);
  });
}
function updateTotals() {
  const subtotal = cart.reduce((s, c) => s + c.price * c.qty, 0);
  const discount = parseFloat(document.getElementById("discount").value) || 0;
  const grand = Math.max(0, subtotal - discount);
  document.getElementById("subtotal").textContent = subtotal.toFixed(2);
  document.getElementById("grandTotal").textContent = grand.toFixed(2);
  document.getElementById("billTotal").textContent = grand.toFixed(2);
  calculateChange();
}

function calculateTotal() {
  updateTotals();
}

function calculateChange() {
  const paid = parseFloat(document.getElementById("paidAmount").value) || 0;
  const grand =
    parseFloat(document.getElementById("grandTotal").textContent) || 0;
  const change = Math.max(0, paid - grand);
  document.getElementById("change").textContent = change.toFixed(2);
}
function resetSale() {
  // Clear cart array
  cart = [];

  // Clear staff name
  document.getElementById("customerName").value = "";

  // Reset discount and paid amount
  document.getElementById("discount").value = 0;
  document.getElementById("paidAmount").value = "";

  // Reset totals
  document.getElementById("subtotal").textContent = "0.00";
  document.getElementById("grandTotal").textContent = "0.00";
  document.getElementById("billTotal").textContent = "0.00";
  document.getElementById("change").textContent = "0.00";

  // Reset date to today
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("saleDate").value = today;

  // Re-render empty cart and bill
  renderCart();

  // Reset bill section
  document.getElementById("billBody").innerHTML = "";
  document.getElementById("billTableWrap").style.display = "none";
  document.getElementById("billInfo").innerHTML = `
      <div class="bill-empty">
          <span class="icon">🧾</span>
          Bill will appear here once you add items
      </div>
  `;

  showToasts("Sale reset successfully ✓");
}

function saveSale() {
  if (cart.length === 0) {
    showToasts("Cart is empty!");
    return;
  }

  const staffName = document.getElementById("customerName").value.trim();
  const saleDate = document.getElementById("saleDate").value;
  const discount = parseFloat(document.getElementById("discount").value) || 0;
  const grandTotal = parseFloat(
    document.getElementById("grandTotal").textContent
  );

  if (!staffName) {
    showToasts("Please enter staff name!");
    return;
  }

  const data = new URLSearchParams();
  data.append("staff_name", staffName);
  data.append("sale_date", saleDate);
  data.append("discount", discount);
  data.append("grand_total", grandTotal);
  data.append("items", JSON.stringify(cart)); // send all items at once

  fetch("save_sale.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: data.toString(),
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        showToasts("Sale saved successfully ✓");
      } else {
        showToasts("Error saving sale!");
        console.error(res.message);
      }
    })
    .catch((err) => {
      console.error(err);
      showToasts("Server error!");
    });
  console.log(cart);
}

// function printBill(sale_id) {
//   if (!sale_id || cart.length === 0) return;

//   const staffName = document.getElementById("customerName").value.trim();
//   const saleDate = document.getElementById("saleDate").value;
//   const discount = parseFloat(document.getElementById("discount").value) || 0;
//   const grandTotal = parseFloat(
//     document.getElementById("grandTotal").textContent
//   );

//   let rows = "";
//   cart.forEach((item) => {
//     rows += `<tr><td>${item.title}</td><td>${item.qty}</td><td>$${(
//       item.price * item.qty
//     ).toFixed(2)}</td></tr>`;
//   });

//   const printWindow = window.open("", "", "width=900,height=700");
//   printWindow.document.write(`
//     <html>
//     <head><title>Sale Bill</title></head>
//     <body>
//       <h2>📖 Book Sale Receipt</h2>
//       <div><strong>Sale ID:</strong> ${sale_id}</div>
//       <div><strong>Staff:</strong> ${staffName}</div>
//       <div><strong>Date:</strong> ${saleDate}</div>
//       <table border="1" cellspacing="0" cellpadding="5">
//         <thead><tr><th>Book</th><th>Qty</th><th>Total</th></tr></thead>
//         <tbody>${rows}</tbody>
//       </table>
//       <div>Discount: $${discount.toFixed(2)}</div>
//       <div>Grand Total: $${grandTotal.toFixed(2)}</div>
//     </body>
//     </html>
//   `);
//   printWindow.document.close();
//   printWindow.focus();
//   printWindow.print();
//   printWindow.close();
// }

// ── TOAST ──
function showToasts(msg) {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.classList.add("show");
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove("show"), 2400);
}
