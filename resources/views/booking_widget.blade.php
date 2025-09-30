{{-- resources/views/widget/booking.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Booking Widget</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  {{-- If served via web routes, this helps with CSRF; API routes typically won't need it --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    /* Minimal, iframe-friendly styles */
    :root { --primary:#0d6efd; --muted:#6c757d; --card:#fff; --bg:#f7f7f8; }
    body { font-family: Inter, system-ui, Arial; margin:0; padding:16px; background:var(--bg); color:#222; }
    .wrap { max-width:820px; margin:0 auto; }
    header { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    h2 { margin:0; font-size:1.1rem; }
    .controls { display:flex; gap:8px; align-items:center; margin-bottom:12px; flex-wrap:wrap; }
    .card { background:var(--card); border-radius:8px; padding:12px; box-shadow:0 1px 3px rgba(0,0,0,.06); margin-bottom:10px; }
    .card + .card { margin-top:8px; }
    .avail-grid { display:grid; grid-template-columns:1fr; gap:8px; }
    @media(min-width:720px){ .avail-grid{ grid-template-columns:1fr 1fr; } }
    .meta { font-size:.875rem; color:var(--muted); margin-top:6px; }
    button { cursor:pointer; border:0; padding:8px 12px; border-radius:6px; }
    .btn-primary { background:var(--primary); color:#fff; }
    .btn-ghost { background:transparent; color:var(--primary); border:1px solid rgba(13,110,253,.12); }
    .small { font-size:.9rem; padding:6px 8px; }
    #bookingSection { margin-top:14px; display:none; }
    .label { display:block; font-size:.85rem; margin:8px 0 4px; }
    input[type=text], input[type=email], textarea, select, input[type=number] {
      width:100%; padding:8px; border-radius:6px; border:1px solid #e6e6e6; box-sizing:border-box;
    }
    .rooms-list { display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; }
    .room-pill { border:1px solid #e9e9ef; padding:8px; border-radius:6px; min-width:120px; background:#fbfbff; display:flex; flex-direction:column; gap:6px; }
    .muted { color:var(--muted); font-size:.9rem; }
    .message { padding:10px; border-radius:6px; margin-top:10px; }
    .message.success { background:#e6ffed; color:#0f5132; border:1px solid #c7f0d1; }
    .message.error { background:#fff0f0; color:#6b0d0d; border:1px solid #f5c2c2; }
  </style>
</head>
<body>
  <div class="wrap card">
    <header>
      <h2>Booking Widget</h2>
      <div class="muted">Embed this via &lt;iframe&gt; — reads `company` from query string</div>
    </header>

    <div class="controls">
      <div class="muted">Company:</div>
      <div id="companyDisplay" class="card small" style="padding:6px 10px;"></div>
      <div style="margin-left:auto" id="loadingIndicator" class="muted">Loading…</div>
    </div>

    <section>
      <div id="availabilitiesContainer" class="avail-grid"></div>
      <div id="noAvail" class="muted" style="display:none">No availabilities found for this company / filters.</div>
    </section>

    <section id="bookingSection" class="card">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <strong id="bookingTitle"></strong>
        <button class="btn-ghost small" onclick="closeBooking()">Close</button>
      </div>

      <div id="bookingMeta" class="meta"></div>

      <label class="label">Trip type</label>
      <select id="tripTypeSelect"></select>

      <label class="label">Rooms (select one or more)</label>
      <div id="roomsContainer" class="rooms-list"></div>

      <hr>

      <div>
        <div class="muted">Lead guest</div>
        <label class="label">Name</label>
        <input id="leadName" type="text" placeholder="John Doe" required>

        <label class="label">Email</label>
        <input id="leadEmail" type="email" placeholder="john@example.com">

        <label class="label">Phone</label>
        <input id="leadPhone" type="text" placeholder="+62...">
      </div>

      <label class="label">Notes (optional)</label>
      <textarea id="notes" rows="3" placeholder="Dietary requirements, pick-up notes..."></textarea>

      <div style="display:flex; gap:8px; margin-top:12px;">
        <button class="btn-primary" id="submitBtn" onclick="submitPrebooking()">Create Pre-booking</button>
        <button class="btn small" onclick="resetForm()">Reset</button>
      </div>

      <div id="formMessage" style="margin-top:10px;"></div>
    </section>
  </div>

<script>
  // --- config & state
  const company = (new URLSearchParams(window.location.search)).get('company') || 'SAMARA';
  document.getElementById('companyDisplay').innerText = company;
  const availContainer = document.getElementById('availabilitiesContainer');
  const bookingSection = document.getElementById('bookingSection');
  const loadingIndicator = document.getElementById('loadingIndicator');
  const formMessage = document.getElementById('formMessage');

  let availabilities = [];
  let selectedAvailability = null;
  let selectedRooms = {}; // { room_id: { room_id, guests_count, extra_bed_used } }

  // helper to show messages
  function showMessage(targetEl, text, type='success') {
    targetEl.innerHTML = `<div class="message ${type}">${text}</div>`;
  }

  function clearMessage(targetEl) { targetEl.innerHTML = ''; }

  // fetch availabilities
  async function loadAvailabilities() {
    loadingIndicator.innerText = 'Loading…';
    try {
      const res = await fetch(`/public/availabilities?company=${encodeURIComponent(company)}`);
      if (!res.ok) throw new Error('Failed to fetch availabilities');
      const data = await res.json();
      availabilities = data;
      renderAvailabilities();
    } catch (err) {
      availContainer.innerHTML = '';
      document.getElementById('noAvail').style.display = 'block';
      loadingIndicator.innerText = 'Error';
      console.error(err);
    } finally {
      loadingIndicator.innerText = '';
    }
  }

  function formatDates(dates) {
    if (!dates) return '';
    try {
      const s = new Date(dates[0]).toLocaleDateString();
      const e = new Date(dates[1]).toLocaleDateString();
      return `${s} — ${e}`;
    } catch { return dates.join(' to '); }
  }

  function renderAvailabilities() {
    availContainer.innerHTML = '';
    document.getElementById('noAvail').style.display = availabilities.length ? 'none' : 'block';

    availabilities.forEach(av => {
      const div = document.createElement('div');
      div.className = 'card';
      div.innerHTML = `
        <div>
          <strong>${escapeHtml(av.boat)}</strong>
          <div class="muted">${escapeHtml(av.trip_type || '')} • ${formatDates(av.dates || [])}</div>
          <div style="margin-top:8px;">
            <span class="muted">Status: ${escapeHtml(av.status || '—')}</span>
            <span style="float:right" class="muted">Available rooms: ${av.available_rooms_count ?? '—'}</span>
          </div>
          <div style="margin-top:10px; display:flex; gap:8px;">
            <button class="btn-primary small" onclick="openBooking(${av.id})">Book</button>
            <button class="btn small" onclick="viewDetails(${av.id})">Details</button>
          </div>
        </div>
      `;
      availContainer.appendChild(div);
    });
  }

  // open booking inline (fetch detail & populate form)
  async function openBooking(id) {
    clearMessage(formMessage);
    bookingSection.style.display = 'none';
    selectedRooms = {};
    try {
      const res = await fetch(`/public/availability/${id}`);
      if (!res.ok) {
        const err = await res.json().catch(()=>null);
        throw new Error(err?.message || 'Failed fetching availability details');
      }
      const data = await res.json();
      selectedAvailability = data;
      populateBookingForm(data);
      bookingSection.style.display = 'block';
      window.scrollTo({ top: bookingSection.offsetTop - 10, behavior: 'smooth' });
    } catch (err) {
      console.error(err);
      showMessage(formMessage, 'Unable to load availability details. Try again later.', 'error');
    }
  }

  function viewDetails(id) {
    openBooking(id);
  }

  function populateBookingForm(data) {
    document.getElementById('bookingTitle').innerText = `${data.boat} (${formatDates(data.dates)})`;
    document.getElementById('bookingMeta').innerText = `Trip type: ${data.trip_type ?? '—'} • Status: ${data.status ?? '—'}`;
    // Trip type select
    const tripTypeSelect = document.getElementById('tripTypeSelect');
    tripTypeSelect.innerHTML = '';
    // If API returns single trip_type, use it. If you want to support choosing between both types, adapt here.
    const opt = document.createElement('option');
    opt.value = data.trip_type ?? 'open_trip';
    opt.innerText = data.trip_type ?? 'open_trip';
    tripTypeSelect.appendChild(opt);

    // Rooms
    const roomsContainer = document.getElementById('roomsContainer');
    roomsContainer.innerHTML = '';
    const available = data.rooms?.available ?? [];
    if (!available.length) {
      roomsContainer.innerHTML = '<div class="muted">No rooms available.</div>';
      return;
    }

    available.forEach(roomId => {
      const pill = document.createElement('div');
      pill.className = 'room-pill';
      pill.innerHTML = `
        <div><label><input type="checkbox" data-room="${roomId}" onchange="onRoomToggle(this)"> <strong>Room ${roomId}</strong></label></div>
        <div style="display:flex; gap:8px; align-items:center;">
          <div style="flex:1">
            <label class="muted">Guests</label>
            <input type="number" min="1" value="1" data-guests-for="${roomId}" oninput="onGuestsChange(${roomId}, this.value)">
          </div>
          <div style="width:1px"></div>
          <div style="flex:1">
            <label class="muted">Extra bed</label>
            <select data-extra-for="${roomId}" onchange="onExtraChange(${roomId}, this.value)">
              <option value="false">No</option>
              <option value="true">Yes</option>
            </select>
          </div>
        </div>
      `;
      roomsContainer.appendChild(pill);
      // initialize selectedRooms with unchecked state only when checked
    });
  }

  // room handlers
  function onRoomToggle(cb) {
    const room = cb.getAttribute('data-room');
    if (cb.checked) {
      const guestsInput = document.querySelector(`[data-guests-for="${room}"]`);
      const extraSelect = document.querySelector(`[data-extra-for="${room}"]`);
      selectedRooms[room] = {
        room_id: Number(room),
        guests_count: Number(guestsInput?.value || 1),
        extra_bed_used: (extraSelect?.value === 'true')
      };
    } else {
      delete selectedRooms[room];
    }
  }

  function onGuestsChange(room, val) {
    if (selectedRooms[room]) selectedRooms[room].guests_count = Number(val || 1);
  }
  function onExtraChange(room, val) {
    if (selectedRooms[room]) selectedRooms[room].extra_bed_used = (val === 'true');
  }

  // submit prebooking
  async function submitPrebooking() {
    clearMessage(formMessage);
    if (!selectedAvailability) {
      showMessage(formMessage, 'Select an availability first.', 'error');
      return;
    }
    const roomsArray = Object.values(selectedRooms);
    if (!roomsArray.length) {
      showMessage(formMessage, 'Please select at least one room.', 'error');
      return;
    }
    const leadName = (document.getElementById('leadName').value || '').trim();
    const leadEmail = (document.getElementById('leadEmail').value || '').trim();
    const leadPhone = (document.getElementById('leadPhone').value || '').trim();

    if (!leadName) {
      showMessage(formMessage, 'Lead guest name is required.', 'error');
      return;
    }

    const payload = {
      company,
      availability_id: selectedAvailability.id,
      trip_type: document.getElementById('tripTypeSelect').value,
      rooms: roomsArray,
      lead_guest: { name: leadName, email: leadEmail || null, phone: leadPhone || null },
      notes: document.getElementById('notes').value || '',
      source: 'direct'
    };

    try {
      // include CSRF token if present in meta (useful when this file is served via web.php)
      const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
      const tokenMeta = document.querySelector('meta[name="csrf-token"]');
      if (tokenMeta && tokenMeta.content) headers['X-CSRF-TOKEN'] = tokenMeta.content;

      const res = await fetch('/public/prebooking', {
        method: 'POST',
        headers,
        body: JSON.stringify(payload)
      });

      const data = await res.json().catch(()=>({}));
      if (!res.ok) {
        const errMsg = data.message || 'Failed creating pre-booking. Please check the form.';
        showMessage(formMessage, errMsg, 'error');
        return;
      }

      // success
      showMessage(formMessage, `Pre-booking created. Booking ID: ${data.booking_id}`, 'success');
      // optional: clear form & selected rooms
      resetForm();
    } catch (err) {
      console.error(err);
      showMessage(formMessage, 'Unexpected error — try again later.', 'error');
    }
  }

  function closeBooking() {
    bookingSection.style.display = 'none';
    selectedAvailability = null;
    selectedRooms = {};
    clearMessage(formMessage);
  }

  function resetForm() {
    document.getElementById('leadName').value = '';
    document.getElementById('leadEmail').value = '';
    document.getElementById('leadPhone').value = '';
    document.getElementById('notes').value = '';
    // uncheck all room checkboxes
    document.querySelectorAll('[data-room]').forEach(cb => { cb.checked = false; });
    selectedRooms = {};
  }

  // small helper to escape HTML for safety
  function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // init
  loadAvailabilities();
</script>
</body>
</html>
