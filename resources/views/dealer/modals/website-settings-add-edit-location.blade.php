{{-- ══════════════════════════════
     ADD / EDIT LOCATION MODAL
══════════════════════════════ --}}
<div class="el-overlay" id="editLocationModal">
    <div class="el-modal">
        <div class="el-header">
            <h5 id="modalTitle">Add Location</h5>
            <button class="el-btn-close" id="btnCloseEditLocation">&times;</button>
        </div>
        <div class="el-body">
            <!-- Left nav -->
            <nav class="el-nav">
                <button class="el-nav-item active" data-panel="general">General</button>
                <button class="el-nav-item" data-panel="phones">Phone Numbers</button>
                <button class="el-nav-item" data-panel="emails">Email Addresses</button>
                <button class="el-nav-item" data-panel="hours-sales">Hours: Sales</button>
                <button class="el-nav-item" data-panel="hours-service">Hours: Service</button>
                <button class="el-nav-item" data-panel="hours-parts">Hours: Parts</button>
                <button class="el-nav-item" data-panel="hours-rentals">Hours: Rentals</button>
                <button class="el-nav-item" data-panel="hours-collision">Hours: Collision</button>
                <button class="el-nav-item" data-panel="holidays">Holidays / Closures</button>
            </nav>

            <!-- Panels -->
            <div class="el-panel active" id="panel-general">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="el-label">Location Name</label>
                        <input type="text" class="el-control" id="locName">
                    </div>
                    <div class="col-8">
                        <label class="el-label">Street Address</label>
                        <input type="text" class="el-control" id="locStreet">
                    </div>
                    <div class="col-4">
                        <label class="el-label">Suite</label>
                        <input type="text" class="el-control" id="locSuite">
                    </div>
                    <div class="col-6">
                        <label class="el-label">Country</label>
                        <select class="el-select" id="locCountry">
                            <option value="US" selected>United States</option>
                            <option value="CA">Canada</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="el-label">City</label>
                        <input type="text" class="el-control" id="locCity">
                    </div>
                    <div class="col-6">
                        <label class="el-label">State</label>
                        <select class="el-select" id="locState">
                            <option value="AL">Alabama</option><option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option><option value="AR">Arkansas</option>
                            <option value="CA">California</option><option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option><option value="DE">Delaware</option>
                            <option value="FL">Florida</option><option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option><option value="ID">Idaho</option>
                            <option value="IL">Illinois</option><option value="IN">Indiana</option>
                            <option value="IA">Iowa</option><option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option><option value="LA">Louisiana</option>
                            <option value="ME">Maine</option><option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option><option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option><option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option><option value="MT">Montana</option>
                            <option value="NE">Nebraska</option><option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option><option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option><option value="NY">New York</option>
                            <option value="NC">North Carolina</option><option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option><option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option><option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option><option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option><option value="TN" selected>Tennessee</option>
                            <option value="TX">Texas</option><option value="UT">Utah</option>
                            <option value="VT">Vermont</option><option value="VA">Virginia</option>
                            <option value="WA">Washington</option><option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option><option value="WY">Wyoming</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="el-label">Zip Code</label>
                        <input type="text" class="el-control" id="locZip">
                    </div>
                    <div class="col-12">
                        <label class="el-label">Map Address Override</label>
                        <input type="text" class="el-control" id="locMapOverride">
                    </div>
                </div>
            </div>

            <!-- Phone panel -->
            <div class="el-panel" id="panel-phones">
                <div class="el-panel-title">Phone Numbers</div>
                <div class="row g-3">
                    @foreach(['Main','Sales','Service','Parts','Rentals','Collision'] as $dept)
                    <div class="col-12">
                        <label class="el-label">Phone: {{ $dept }}</label>
                        <input type="tel" class="el-control" id="phone_{{ strtolower($dept) }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Email panel -->
            <div class="el-panel" id="panel-emails">
                <div class="el-panel-title">Email Addresses</div>
                <div class="row g-3">
                    @foreach(['Main','Sales','Service','Parts','Rentals','Collision'] as $dept)
                    <div class="col-12">
                        <label class="el-label">Email: {{ $dept }}</label>
                        <input type="email" class="el-control" id="email_{{ strtolower($dept) }}">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Hours panels -->
            @php
                $departments = ['sales','service','parts','rentals','collision'];
                $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                $timeOptions = ['[Select]','6:00 AM','6:30 AM','7:00 AM','7:30 AM','8:00 AM','8:30 AM','9:00 AM','9:30 AM','10:00 AM','10:30 AM','11:00 AM','11:30 AM','12:00 PM','12:30 PM','1:00 PM','1:30 PM','2:00 PM','2:30 PM','3:00 PM','3:30 PM','4:00 PM','4:30 PM','5:00 PM','5:30 PM','6:00 PM','6:30 PM','7:00 PM','7:30 PM','8:00 PM','9:00 PM'];
            @endphp

            @foreach($departments as $dept)
            <div class="el-panel" id="panel-hours-{{ $dept }}">
                <div class="el-panel-title">{{ ucfirst($dept) }} Hours</div>
                <table class="hours-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Opens at</th>
                            <th>Closes at</th>
                            <th class="text-center">Closed</th>
                            <th class="text-center">Appointment Only</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $dayIndex => $day)
                        <tr>
                            <td>{{ $day }}</td>
                            <td><select class="day-select" data-dept="{{ $dept }}" data-day="{{ $dayIndex }}" data-type="open"></select></td>
                            <td><select class="day-select" data-dept="{{ $dept }}" data-day="{{ $dayIndex }}" data-type="close"></select></td>
                            <td class="text-center"><input type="checkbox" class="day-check" data-dept="{{ $dept }}" data-day="{{ $dayIndex }}" data-type="closed"></td>
                            <td class="text-center"><input type="checkbox" class="day-check" data-dept="{{ $dept }}" data-day="{{ $dayIndex }}" data-type="appointment_only"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach

            <!-- Holidays panel -->
            <div class="el-panel" id="panel-holidays">
                <div class="el-panel-title">Add Closed Dates Outside of Normal Operation</div>
                <table class="holiday-table" id="holidayTable">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="holidayRows"></tbody>
                </table>
                <button type="button" class="btn-add-row" id="btnAddHolidayRow">Add Row</button>
            </div>
        </div>
        <div class="el-footer">
            <button type="button" class="el-btn-next" id="btnElNext">Next &rsaquo;</button>
        </div>
    </div>
</div>