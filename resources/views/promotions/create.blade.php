@extends('layouts.app')
@section('title', 'Promotion Banner')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <a href="{{ route('promotions.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="promotion-form">
            <div class="row">
                <div class="col-12">
                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Create Promotion Banner (PNG)</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form method="POST" action="{{ route('promotions.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label>Hero Banner Image (Optional) <small>1130X</small></label>
                                            <input type="file" name="hero_banner" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Brand Logo (Optional)</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Hotel Name</label>
                                            <input type="text" name="hotel_name" class="form-control" value="{{ old('hotel_name') }}">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Hotel Year</label>
                                            <input type="text" name="hotel_year" class="form-control" value="{{ old('hotel_year') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 1<small>(240X160)</small></label>
                                            <input type="file" name="room_image_1" class="form-control" accept="image/*" >
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 2<small>(240X160)</small></label>
                                            <input type="file" name="room_image_2" class="form-control" accept="image/*" >
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 3<small>(240X160)</small></label>
                                            <input type="file" name="room_image_3" class="form-control" accept="image/*" >
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 4<small>(240X160)</small></label>
                                            <input type="file" name="room_image_4" class="form-control" accept="image/*" >
                                        </div>
                                    </div>

                                    <hr>
                                    <h5>Offer Details (Maximum 5 rows)</h5>
                                    <div id="offerRows"></div>

                                    <div class="mt-1">
                                        <button class="btn btn-outline-primary" type="button" id="addRowBtn">Add Row</button>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                    <hr class="mt-3">
                                    <h5>Footer contacts</h5>
                                    <p class="text-muted small">First two contacts are required (defaults are pre-filled). A third contact is optional and appears on the banner only when both phone and name are filled.</p>
                                    @foreach ([0 => 'Contact 1', 1 => 'Contact 2', 2 => 'Contact 3 (optional)'] as $idx => $label)
                                    <div class="form-group border rounded p-2 mb-2 px-3">
                                        <label class="font-weight-bold d-block">{{ $label }}</label>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label for="promo_contact_{{ $idx }}_number" class="small">Phone / WhatsApp @if ($idx < 2)<span class="text-danger">*</span>@endif</label>
                                                <input type="text" name="contacts[{{ $idx }}][number]" id="promo_contact_{{ $idx }}_number" class="form-control"
                                                    value="{{ old('contacts.'.$idx.'.number', $defaultContacts[$idx]['number'] ?? '') }}"
                                                    @if ($idx < 2) required @endif>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="promo_contact_{{ $idx }}_name" class="small">Name @if ($idx < 2)<span class="text-danger">*</span>@endif</label>
                                                <input type="text" name="contacts[{{ $idx }}][name]" id="promo_contact_{{ $idx }}_name" class="form-control"
                                                    value="{{ old('contacts.'.$idx.'.name', $defaultContacts[$idx]['name'] ?? '') }}"
                                                    @if ($idx < 2) required @endif>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="promo_contact_{{ $idx }}_location" class="small">Location @if ($idx < 2)<span class="text-danger">*</span>@endif</label>
                                                <input type="text" name="contacts[{{ $idx }}][location]" id="promo_contact_{{ $idx }}_location" class="form-control"
                                                    value="{{ old('contacts.'.$idx.'.location', $defaultContacts[$idx]['location'] ?? '') }}"
                                                    @if ($idx < 2) required @endif>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Generate Banner</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('script')
<script>
    (function() {
        const maxRows = 5;
        const container = document.getElementById('offerRows');
        const addBtn = document.getElementById('addRowBtn');
        let rowCount = 0;

        function rowTemplate(index) {
            return `
                <div class="border p-2 mb-2 offer-row">
                    <div class="row">
                        <div class="col-md-2 mb-1">
                            <label>From<span class="text-danger">*</span></label>
                            <input type="date" name="offers[${index}][from_date]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>To<span class="text-danger">*</span></label>
                            <input type="date" name="offers[${index}][to_date]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Double (Price)<span class="text-danger">*</span></label>
                            <input type="number" min="1" name="offers[${index}][double_rate]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Triple (Price)<span class="text-danger">*</span></label>
                            <input type="number" min="1" name="offers[${index}][triple_rate]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Quad (Price)<span class="text-danger">*</span></label>
                            <input type="number" min="1" name="offers[${index}][quad_rate]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Meals</label>
                            <input type="text" maxlength="20" name="offers[${index}][meals]" class="form-control" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                </div>
            `;
        }

        function addRow() {
            if (rowCount >= maxRows) {
                return;
            }

            container.insertAdjacentHTML('beforeend', rowTemplate(rowCount));
            rowCount += 1;
            syncControls();
        }

        function syncControls() {
            addBtn.disabled = rowCount >= maxRows;
            const removeButtons = container.querySelectorAll('.remove-row');
            removeButtons.forEach((btn) => {
                btn.disabled = rowCount <= 1;
            });
        }

        addBtn.addEventListener('click', addRow);
        container.addEventListener('click', function(event) {
            if (!event.target.classList.contains('remove-row')) {
                return;
            }

            const row = event.target.closest('.offer-row');
            if (!row) {
                return;
            }

            row.remove();
            rowCount -= 1;
            syncControls();
        });

        addRow();
    })();
</script>
@endsection
